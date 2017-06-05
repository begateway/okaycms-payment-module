<?php
require_once(__DIR__ . DS . 'begateway-api-php' . DS . 'lib' . DS . 'beGateway.php');
// Работаем в корневой директории
chdir ('../../');
require_once('api/Okay.php');
$okay = new OKay();

$webhook = new \beGateway\Webhook;

// Сумма, которую заплатил покупатель. Дробная часть отделяется точкой.
$money = new \beGateway\Money;
$money->setCents($webhook->getResponse()->transacton->amount);
$money->setCurrency($webhook->getResponse()->transaction->currency);

$amount = $money->getAmount();

// Внутренний номер покупки продавца
// В этом поле передается id заказа в нашем магазине.
list($order_id, $payment_method_id) = explode('|', $webhook->getTrackingId());
$order_id = intval($order_id);
$payment_method_id = intval($payment_method_id);

// Проверим статус
if(!$webhook->isSuccess())
	die('Incorrect Status');
////////////////////////////////////////////////
// Выберем заказ из базы
////////////////////////////////////////////////
$order = $okay->orders->get_order(intval($order_id));
if(empty($order))
	die('Оплачиваемый заказ не найден');


// Нельзя оплатить уже оплаченный заказ
if($order->paid)
	die('Этот заказ уже оплачен');

////////////////////////////////////////////////
// Выбираем из базы соответствующий метод оплаты
////////////////////////////////////////////////
if ($order->payment_method_id != $payment_method_id)
  die('Опата не принадлежит заказу');

$method = $okay->payment->get_payment_method(intval($order->payment_method_id));
if(empty($method))
	die("Неизвестный метод оплаты");

if(round($okay->money->convert($order->total_price, $method->currency_id, false), 2) != $money->getAmount())
  die("Неверная сумма");

$settings = unserialize($method->settings);

\beGateway\Settings::$shopId = $settings['shop_id'];
\beGateway\Settings::$shopKey = $settings['shop_key'];

// Проверяем авторизационные данные
if (!$webhook->isAuthorized())
  die('Нет авторизации');

////////////////////////////////////
// Проверка наличия товара
////////////////////////////////////
$purchases = $okay->orders->get_purchases(array('order_id'=>intval($order->id)));
foreach($purchases as $purchase)
{
	$variant = $okay->variants->get_variant(intval($purchase->variant_id));
	if(empty($variant) || (!$variant->infinity && $variant->stock < $purchase->amount))
	{
		die("Нехватка товара $purchase->product_name $purchase->variant_name");
	}
}

// Установим статус оплачен
$okay->orders->update_order(intval($order->id), array('paid'=>1));

// Спишем товары
$okay->orders->close(intval($order->id));
$okay->notify->email_order_user(intval($order->id));
$okay->notify->email_order_admin(intval($order->id));

die("OK".$order_id."\n");
