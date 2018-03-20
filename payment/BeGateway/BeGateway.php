<?php
require_once('api/Okay.php');
require_once(__DIR__ . '/begateway-api-php/lib/BeGateway.php');

class BeGateway extends Okay
{
	public function checkout_form($order_id, $button_text = null)
	{
    $order = $this->orders->get_order(intval($order_id));

    if (empty($order))
    	return array(
        'error'=>true,
        'error_message'=>'Ошибка получения данных заказа'
      );

    $payment_method = $this->payment->get_payment_method($order->payment_method_id);
    if (empty($payment_method))
    	return array(
        'error'=>true,
        'error_message'=>'Ошибка получения способа оплаты'
      );

    $payment_currency = $this->money->get_currency(intval($payment_method->currency_id));
    $settings = $this->payment->get_payment_settings($payment_method->id);

    \BeGateway\Settings::$shopId = $settings['shop_id'];
    \BeGateway\Settings::$shopKey = $settings['shop_key'];
    \BeGateway\Settings::$checkoutBase = 'https://' . $settings['domain_checkout'];

    $desc = 'Оплата заказа №'.$order->id;
    $result_url = $this->config->root_url.'/order/'.$order->url;
    $server_url = $this->config->root_url.'/payment/BeGateway/callback.php';
    $server_url = str_replace('okaycms.local', 'okaycms.webhook.begateway.com:8443', $server_url);

    $fio_arr = explode(" ", $order->name);
    $firstname = trim($fio_arr[0]);
    $lastname = trim($fio_arr[1]);

    $transaction = new \BeGateway\GetPaymentToken;

    $transaction->money->setAmount($order->total_price);
    $transaction->money->setCurrency(str_replace("RUR", "RUB", $payment_currency->code));
    $transaction->setDescription($desc);
    $transaction->setTrackingId("$order_id|$order->payment_method_id");
    $transaction->setLanguage('ru');
    $transaction->setNotificationUrl($server_url);
    $transaction->setSuccessUrl($result_url);
    $transaction->setDeclineUrl($result_url);
    $transaction->setFailUrl($result_url);
    $transaction->setCancelUrl($this->config->root_url);

    $transaction->customer->setFirstName($firstname);
    $transaction->customer->setLastName($lastname);
    $transaction->customer->setEmail($order->email);
    $transaction->customer->setPhone($order->phone);

    if ($settings['pm_bankcard']) {
      $cc = new \BeGateway\PaymentMethod\CreditCard;
      $transaction->addPaymentMethod($cc);
    }

    if ($settings['pm_bankcard_halva']) {
      $halva = new \BeGateway\PaymentMethod\CreditCardHalva;
      $transaction->addPaymentMethod($halva);
    }

    if ($settings['pm_erip']) {
      $erip = new \BeGateway\PaymentMethod\Erip(array(
        'order_id' => $order_id,
        'account_number' => strval($order_id),
        'service_no' => $settings['pm_erip_service_no'],
        'service_info' => array($desc)
      ));
      $transaction->addPaymentMethod($erip);
    }

    $response = $transaction->submit();

    if (!$response->isSuccess()) {
      return array(
        'error'=>true,
        'error_message' => $response->getMessage()
      );
    }

    return array(
      'action' => $response->getRedirectUrlScriptName(),
      'token' => $response->getToken(),
      'error' => false
    );
  }
}
