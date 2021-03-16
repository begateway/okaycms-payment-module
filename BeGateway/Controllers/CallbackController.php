<?php

namespace Okay\Modules\OkayCMS\BeGateway\Controllers;

require_once($_SERVER['DOCUMENT_ROOT'].'/Okay/Modules/OkayCMS/BeGateway/begateway-api-php/lib/BeGateway.php');

use BeGateway\Webhook;
use Okay\Core\Money;
use Okay\Core\Notify;
use Okay\Entities\LanguagesEntity;
use Okay\Entities\OrdersEntity;
use Okay\Entities\PaymentsEntity;
use Okay\Controllers\AbstractController;
use Okay\Entities\PurchasesEntity;
use Okay\Entities\VariantsEntity;
use Okay\Modules\OkayCMS\BeGateway\Helpers\CommonHelpers;
use Psr\Log\LoggerInterface;

class CallbackController extends AbstractController
{
    protected $settings;
    protected $lang;
    protected $logger;
    protected $order;
    protected $ordersEntity;
    protected $webhook;
    protected $notify;


    public function payOrder(
        Money $money,
        Notify $notify,
        OrdersEntity $ordersEntity,
        PaymentsEntity $paymentsEntity,
        PurchasesEntity $purchasesEntity,
        VariantsEntity $variantsEntity,
        LoggerInterface $logger
    ) {

        $this->webhook = new Webhook;
        $this->logger = $logger;
        $this->ordersEntity = $ordersEntity;
        $this->notify = $notify;
        $response = $this->webhook->getResponse();
        $this->lang = CommonHelpers::initDictionary($response->transaction->language);

        /* Сумма, которую заплатил покупатель.
        Дробная часть отделяется точкой. */
        $bgtwMoney = new \BeGateway\Money;
        $bgtwMoney->setCents($response->transaction->amount);
        $bgtwMoney->setCurrency($response->transaction->currency);
        $amount = $bgtwMoney->getAmount();

        /* Внутренний номер покупки продавца
        В этом поле передается id заказа в нашем магазине.*/
        list($orderId, $payment_method_id) = explode('|', $this->webhook->getTrackingId());
        $this->order =  $this->ordersEntity->get((int)$orderId);

        /* Оплачиваемый заказ не найден */
        if(empty($this->order)) {
            $this->warnAndDie('order_not_found', $orderId);
        }

        /*Оплата не принадлежит заказу*/
        if ($this->order->payment_method_id != $payment_method_id) {
            $this->warnAndDie('payment_not_belongs', $orderId);
        }

        $paymentMethod = $paymentsEntity->get($this->order->payment_method_id);

        /*Неизвестный метод оплаты*/
        if(empty($paymentMethod)) {
            $this->warnAndDie('payment_method_unknown', $orderId);
        }

        $this->settings = $paymentsEntity->getPaymentSettings($paymentMethod->id);
        if ( $this->settings['debug'] ) {
            $this->logger->info('begateway: Callback has been got:' . var_export($_SERVER, true));
        }

        CommonHelpers::initBeGatewaySettings($this->settings);
        /*Транзакция не авторизована*/
        if (!$this->webhook->isAuthorized()) {
            $this->warnAndDie('not_authorised', $orderId);
        }

        if ( $this->settings['debug'] ) {
            $this->logger->info("begateway callback: The transaction is authorised.");
        }

        $type = $this->webhook->getResponse()->transaction->type;
        if ( $this->settings['debug'] ) {
            $this->logger->info("begateway callback: The transaction type is: " . $type);
        }
        if ($type == 'authorization') {
            $this->orderStatusCheck($type);
            die(0);
        }

        /* Нельзя оплатить уже оплаченный заказ */
        if($this->order->paid){
            $this->warnAndDie('order_payed', $orderId);
        }

        /*Неверная сумма*/
        if(round($money->convert
            ($this->order->total_price, $paymentMethod->currency_id, false), 2) != $amount){
            $this->warnAndDie('wrong_amount', $orderId);
        }

        /* Проверка наличия товара */
        $purchases = $purchasesEntity->find(['order_id'=> (int) $this->order->id]);
        foreach($purchases as $purchase) {
            $variant = $variantsEntity->get((int) $purchase->variant_id);

            if(empty($variant) || (!$variant->infinity && $variant->stock < $purchase->amount)) {
                $this->warnAndDie('low_stock' . $purchase->product_name . $purchase->variant_name, $orderId);
            }

        }
        /* Определим статус и обновим ордер*/
        $this->orderStatusCheck($type);

    }

    private function warnAndDie($frase, $orderId, $debug = true) {
        $errMsg = 'begateway: ' . $this->lang[$frase] . $this->lang['order_N'] . $orderId;
        if($debug){
            $this->logger->warning($errMsg);
        }
        if($this->order){
            $this->updateOrder(['note' => $errMsg . $this->noticeUIDAndPaymentMethod()]);
        }
        die($errMsg);
    }

    private function updateOrder($data){
        $this->ordersEntity->update((int) $this->order->id, $data);
        $this->ordersEntity->close(intval($this->order->id));
    }

    private function noticeUIDAndPaymentMethod(){
        $notice = array(
            ' ',
            $this->lang['payment_uid'] . $this->webhook->getUid(),
            'Payment method: ' . $this->webhook->getPaymentMethod()
        );

        $notice = implode('<br>', $notice);
        return $notice;
    }

    private function orderStatusCheck($type) {
        if (in_array($type, array('payment','authorization'))) {
            $status = $this->webhook->getStatus();

            $messages = array(
                'payment' => array(
                    'success' => $this->lang['payment_success'],
                    'failed' => $this->lang['payment_failed'],
                    'incomplete' => $this->lang['payment_incomplete'],
                    'error' => $this->lang['payment_error'],
                ),
                'authorization' => array(
                    'success' => $this->lang['authorisation_success'],
                    'failed' => $this->lang['authorisation_failed'],
                    'incomplete' => $this->lang['authorisation_incomplete'],
                    'error' => $this->lang['authorisation_error'],
                ),
                'callback_error' => $this->lang['callback_error']
            );

            if ( $this->settings['debug'] ){
                $this->logger->info( 'begateway callback: Transaction type: ' . $type . '. Payment status '.$status.'. UID: '.$this->webhook->getUid());
            }

            $notice = $this->noticeUIDAndPaymentMethod();

            if ($this->webhook->isSuccess()) {
                if ($type == 'payment' ) {
                    /* Списание товаров */
                    $this->order->note = $messages[$type]['success'] . $notice;
                    $this->updateOrder(['paid'=>1, 'note'=>$this->order->note]);
                    /* Нотификация*/
                    $this->notify->emailOrderUser(intval($this->order->id));
                    $this->notify->emailOrderAdmin(intval($this->order->id));
                }elseif ($type == 'authorization' ){
                    $this->order->note = $messages[$type]['success'] . $notice;
                }
            } else {
                if
                    ($this->webhook->isFailed()){
                    $this->order->note = $messages[$type]['failed'] . $notice;

                } elseif
                    ($this->webhook->isIncomplete() || $this->webhook->isPending()){
                    $this->order->note = $messages[$type]['incomplete'] . $notice;

                } elseif
                    ($this->webhook->isError()){
                    $this->order->note = $messages[$type]['error'] . $notice;
                } else {
                        $this->order->note = $messages['callback_error'] . $notice;
                    }
                $this->updateOrder(['note'=>$this->order->note]);
            }
            if ( $this->settings['debug'] ) {
                $this->logger->info("begateway callback:" . $this->lang['order_N'] . $this->order->id . ' ' . $this->order->note);
            }
        }
    }
}