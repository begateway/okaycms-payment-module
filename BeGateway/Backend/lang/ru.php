<?php

$lang['BeGateway'] = "BeGateway";
$lang['shop-id'] = "ID магазина";
$lang['secret-key'] = "Секретный ключ магазина";
$lang['public-key'] = "Публичный ключ магазина";
$lang['domain-gateway'] = "Введите домен платежного шлюза вашей платежной компании";
$lang['domain-checkout'] = "Введите домен платежной страницы вашей платежной компании";
$lang['payment'] = "Оплата";
$lang['authorization'] = "Авторизация";
$lang['transaction_type'] = "Тип операции (Выберите Оплата или Авторизация)";
$lang['enable_bankcard'] = "Включить оплату банковской картой (Включает оплату с помощью карты VISA/Mastercard и т.д.)";
$lang['yes'] = "Да";
$lang['no'] = "Нет";
$lang['enable_bankcard_halva'] = "Включить оплату банковской картой Халва";
$lang['enable_erip'] = "Включить оплату через ЕРИП";
$lang['erip_service_no'] = "Введите код услуги ЕРИП, сообщенной вам ваши провайдером платежей";
$lang['payment_valid'] = "Оплата действительная (минут) - укажите время, за которое заказ должен быть оплачен";
$lang['show-transaction-table'] = "Включить администратору возможность списания/отмены авторизации/возврат";
$lang['mode'] = "Режим работы";
$lang['test'] = "Тест";
$lang['live'] = "Рабочий";
$lang['debug'] = "Журнал отладки. (Включить журнал сообщений)";

$lang['submit_begateway_payment'] = "Оплатить";
$lang['cancel_begateway_payment'] = "Отменить заказ";
$lang['order_payment_N'] = "Оплата заказа №";

$lang['repeat_request'] = "Повторить запрос";
$lang['something_wrong'] = "Что-то пошло не так!";
$lang['unable_to_get_token'] = 'Не был получен токен на оплату заказа: ';
$lang['reason'] = 'Причина';

$lang['payment_uid'] = "UID оплаты: ";
$lang['payment_success'] = "Оплата успешна.";
$lang['payment_failed'] = "Платеж не прошел.";
$lang['payment_incomplete'] =  "Платеж не завершен. Статус заказа не изменен.";
$lang['payment_error'] = "Ошибка оплаты. Статус заказа не изменен.";
$lang['authorisation_success'] = "Платеж авторизирован. Деньги реально еще не списаны.";
$lang['authorisation_failed'] = "Авторизация не успешна.";
$lang['authorisation_incomplete'] = "Авторизация не завершена. Статус заказа не изменен.";
$lang['authorisation_error'] = "Ошибка авторизации. Статус заказа не изменен.";
$lang['callback_error'] = "Ошибка нотификации. Статус заказа не изменен.";

$lang['order_not_found'] = "Оплачиваемый заказ не найден. ";
$lang['order_N'] = "Заказ №";
$lang['payment_not_belongs'] = "Оплата не принадлежит заказу.";
$lang['not_authorised'] = "Транзакция не авторизована. Невозможно продолжить обработку " ;
$lang['order_payed'] = "Этот заказ уже оплачен. ";
$lang['wrong_amount'] = "Неверная сумма. ";

$lang['okaycms__begateway__description_title'] = "Платежный шлюз BeGateway";
$lang['okaycms__begateway__description_part_1'] = "Войдите в личный кабинет администратора, откройте Настройки - Оплата и создайте новый вид оплаты, при этом выбрать BeGateway в качестве платежного модуля.";
$lang['okaycms__begateway__description_part_2'] = "Укажите параметры Идентификатор магазина, Секретный ключ магазина, Домен страницы оплаты и выберите доступные способы оплаты.";
$lang['okaycms__begateway__description_part_3'] = "Установите флажок Активен. Остальные параметры в соответствии с конкретными предпочтениями.";
$lang['okaycms__begateway__description_part_4'] = "После этого доступен новый модуль оплаты.";
