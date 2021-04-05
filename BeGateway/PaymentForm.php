<?php


namespace Okay\Modules\OkayCMS\BeGateway;

require_once(__DIR__ . '/begateway-api-php/lib/BeGateway.php');

use BeGateway\GetPaymentToken;
use BeGateway\PaymentMethod\CreditCard;
use BeGateway\PaymentMethod\CreditCardHalva;
use BeGateway\PaymentMethod\Erip;
use BeGateway\Settings;
use Okay\Core\EntityFactory;
use Okay\Core\Modules\AbstractModule;
use Okay\Core\Modules\Interfaces\PaymentFormInterface;
use Okay\Core\Money;
use Okay\Core\Router;
use Okay\Core\Languages;
use Okay\Core\ServiceLocator;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\OrdersEntity;
use Okay\Entities\PaymentsEntity;
use Okay\Entities\LanguagesEntity;
use Okay\Modules\OkayCMS\BeGateway\Helpers\CommonHelpers;
use Psr\Log\LoggerInterface;

class PaymentForm extends AbstractModule implements PaymentFormInterface
{

    /**
     * @var EntityFactory
     */
    private $entityFactory;

    /**
     * @var Languages
     */
    private $languages;

    /**
     * @var Money
     */
    private $money;


    public function __construct(EntityFactory $entityFactory, Languages $languages, Money $money)
    {
        parent::__construct();
        $this->entityFactory = $entityFactory;
        $this->languages     = $languages;
        $this->money         = $money;
    }

    /**
     * @inheritDoc
     */
    public function checkoutForm($orderId)
    {
        /** @var OrdersEntity $ordersEntity */
        $ordersEntity = $this->entityFactory->get(OrdersEntity::class);

        /** @var PaymentsEntity $paymentsEntity */
        $paymentsEntity = $this->entityFactory->get(PaymentsEntity::class);

        /** @var CurrenciesEntity $currenciesEntity */
        $currenciesEntity = $this->entityFactory->get(CurrenciesEntity::class);

        /** @var LanguagesEntity $languagesEntity */
        $languagesEntity = $this->entityFactory->get(LanguagesEntity::class);

        $SL = ServiceLocator::getInstance();
        $logger = $SL->getService(LoggerInterface::class);

        $order = $ordersEntity->get((int)$orderId);

        $currentLangId    = (int) $order->lang_id;
        $currentLangLabel = $languagesEntity->get($currentLangId)->label;

        $paymentMethod = $paymentsEntity->get($order->payment_method_id);
        $settings = $paymentsEntity->getPaymentSettings($paymentMethod->id);

        if ($settings['debug']) {
            $logger->info('begateway: Preparing request to get token for order Nо' . $orderId . ' ...');
            $lang = CommonHelpers::initDictionary($currentLangLabel, $logger);
        }
        else{
            $lang = CommonHelpers::initDictionary($currentLangLabel);
        }

        $token = new GetPaymentToken;

        CommonHelpers::initBeGatewaySettings($settings);

        if ($settings['transaction_type'] == 'authorization') {
            $token->setAuthorizationTransactionType();
        }

        $paymentCurrency = $currenciesEntity->get(intval($paymentMethod->currency_id));
        $paymentCurrencyCode = $paymentCurrency->code !== 'RUR' ? : 'RUB';
        $token->money->setCurrency($paymentCurrencyCode);

        $price = round($this->money->convert($order->total_price, $paymentMethod->currency_id, false), 2);
        $token->money->setAmount($price);

        $description = $lang['order_payment_N'] ? : 'Payment for order Nо';
        $description = $description. $order->id . '.';
        $token->setDescription($description);
        $token->setTrackingId("$order->id|$order->payment_method_id");

        list($firstName, $lastName) = $this->separateFullNameOnFirstNameAndLastName($order->name);
        $token->customer->setFirstName($firstName);
        $token->customer->setLastName($lastName);

        //$token->customer->setCountry($order->get_billing_country());
        //$token->customer->setCity($order->get_billing_city());
        $token->customer->setPhone($order->phone);
        //$token->customer->setZip($order->get_billing_postcode());
        $token->customer->setAddress($order->address);
        $token->customer->setEmail($order->email);

        $orderUrl = Router::generateUrl('order', ['url' => $order->url], true, $currentLangId);
        $mainUrl = Router::generateUrl('main', ['url' => $order->url], true, $currentLangId);
        $token->setSuccessUrl($orderUrl);
        $token->setDeclineUrl($orderUrl);
        $token->setFailUrl($orderUrl);
        $token->setCancelUrl($mainUrl);
        $token->setNotificationUrl(Router::generateUrl('BeGateway_callback', [], true, $currentLangId));
        if($settings['payment_valid'] && intval($settings['payment_valid']) > 0) {
            $paymentValidTime =  intval($settings['payment_valid']);
        }
        else{
            $paymentValidTime = 60;
        }
        $token->setExpiryDate(date("c", $paymentValidTime * 60 + time() + 1));

        $token->setLanguage(CommonHelpers::mapLangLabelForBeGateway($currentLangLabel));

        if ($settings['enable_bankcard']) {
            $cc = new CreditCard;
            $token->addPaymentMethod($cc);
        }

        if ($settings['enable_bankcard_halva']) {
            $halva = new CreditCardHalva;
            $token->addPaymentMethod($halva);
        }

        if ($settings['enable_erip']) {
            $erip = new Erip(array(
                'order_id' => $order->id,
                'account_number' => ltrim($order->get_order_number()),
                'service_no' => $this->settings['erip_service_no']
            ));
            $token->addPaymentMethod($erip);
        }

        if ($settings['mode'] == 'test') {
            $token->setTestMode(true);
        }

        if ($settings['debug']){
            $logger->info('begateway: Requesting token for order ' . $order->id);
            $logger->info('begateway: Token data ' . print_r($token, true));
        }

        $response = $token->submit();

        $this->design->assign('cancel_url', $mainUrl);
        $this->design->assign('cancel_begateway_payment',
            $lang['cancel_begateway_payment'] ? : "Cancel order");
        $this->design->assign('language', $currentLangLabel);

        if(!$response->isSuccess()) {

            /* Если токен не получен - предлагаем перепослать запрос или выйти из оплаты*/
            $errMsg1 = $lang['unable_to_get_token'] ? : 'Unable to get payment token on order: ';
            $errMsg1 = $errMsg1 . $order->id;
            $errMsg2 = $lang['reason'] ? : 'Reason: ';
            $errMsg2 = $errMsg2 . $response->getMessage();

            if ($settings['debug']){
                $logger->info('begateway: ' . $errMsg1 . ' .' . $errMsg2);
            }
            $this->design->assign('something_wrong', $lang['something_wrong'] ? : "Something went wrong!");
            $this->design->assign('err_msg', $errMsg1);
            $this->design->assign('repeat_request',
                $lang['repeat_request'] ? : "Try again");
            $this->design->assign('order_url', $orderUrl);

            return $this->design->fetch('somethingWrong.tpl');
        } else {
            /*now look to the result array for the token*/
            $paymentUrl = $response->getRedirectUrlScriptName();
            $this->design->assign('payment_url', $paymentUrl);
            $this->design->assign('token', $response->getToken());
            $this->design->assign('submit_begateway_payment',
                $lang['submit_begateway_payment'] ? : "Make payment");
                $this->design->assign('checkout_url', Settings::$checkoutBase);

            if ($settings['debug']) {
                $logger->info('begateway: Token received, proposing customer submit payment and go to: ' . $paymentUrl);
            }

            $jsUrl = explode('.', $settings['domain-checkout']);
            $jsUrl[0] = 'js';
            $this->design->assign('js_url', 'https://' . implode('.', $jsUrl) . '/widget/be_gateway.js');
            return $this->design->fetch('form.tpl');
        }
    }

    private function separateFullNameOnFirstNameAndLastName($fullName)
    {
        $parts = explode(' ', $fullName);
        $firstName = isset($parts[0]) ? $parts[0] : '';
        $lastName  = isset($parts[1]) ? $parts[1] : '';
        return [$firstName, $lastName];
    }
}
