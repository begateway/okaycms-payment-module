# Модуль оплаты beGateway для Okay CMS

## Установка

  * [Скачайте архив модуля](https://github.com/beGateway/okaycms-payment-module/raw/master/okaycms-payment-module.zip), распакуйте его и скопируйте его содержимое в директорию модулей вашей Okay CMS инсталляции (Okay/Modules/OkayCMS/).

## Настройка

  * Войдите в личный кабинет администратора, откройте Настройки - Оплата и создайте новый вид оплаты, при этом выбрать BeGateway в качестве платежного модуля
  * Указать параметры Идентификатор магазина, Секретный ключ магазина, Домен страницы оплаты и выбрать доступные способы оплаты
  * Указать один из доступных способов доставки, иначе способ оплаты не будет виден покупателю
  * Установить флажок Активен
  * Остальные параметры в соответствии с конкретными предпочтениями

После этого доступен новый модуль оплаты.

## Примечания

Разработано и протестировано c OkayCMS 4.0.1

## Тестовые данные

Вы можете использовать приведенные ниже тестовые данные, чтобы протестировать оплату. При переключении в рабочий режим обязательно задайте свои данные, выданные вашим провайдером платежей.

  * Id магазина __361__
  * Секретный ключ магазина __b8647b68898b084b836474ed8d61ffe117c9a01168d867f24953b776ddcb134d__
  * Домен страницы оплаты __checkout.begateway.com__
