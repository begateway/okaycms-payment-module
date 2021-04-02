# Модуль оплаты beGateway для Okay CMS

## Установка

  * [Скачайте архив модуля](https://github.com/beGateway/okaycms-payment-module/raw/master/okaycms-payment-module.zip), распакуйте его и скопируйте его содержимое в директорию модулей вашей Okay CMS инсталляции (Okay/Modules/OkayCMS/).

## Настройка

  * Войдите в личный кабинет администратора, откройте раздел _Модули -> Мои модули_. Для модуля **OkayCMS/BeGateway** нажмите установить. После чего выставьте флажок активности в положение включено.
  * Откройте _Настройки - Оплата_ и создайте новый вид оплаты, при этом выбрать **BeGateway** в качестве платежного модуля
  * Указать параметры Идентификатор магазина, Секретный ключ магазина, Домен страницы оплаты и выбрать доступные способы оплаты
  * Указать один из доступных способов доставки, иначе способ оплаты не будет виден покупателю
  * Установить флажок Активен
  * Остальные параметры в соответствии с конкретными предпочтениями

После этого доступен новый модуль оплаты.

## Примечания

Разработано и протестировано c OkayCMS 4.0.2

## Тестовые данные

Вы можете использовать приведенные ниже тестовые данные, чтобы протестировать оплату. При переключении в рабочий режим обязательно задайте свои данные, выданные вашим провайдером платежей.

  * Id магазина __361__
  * Секретный ключ магазина __b8647b68898b084b836474ed8d61ffe117c9a01168d867f24953b776ddcb134d__
  * Публичный ключ магазина __MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEArO7bNKtnJgCn0PJVn2X7QmhjGQ2GNNw412D+NMP4y3Qs69y6i5T/zJBQAHwGKLwAxyGmQ2mMpPZCk4pT9HSIHwHiUVtvdZ/78CX1IQJON/Xf22kMULhquwDZcy3Cp8P4PBBaQZVvm7v1FwaxswyLD6WTWjksRgSH/cAhQzgq6WC4jvfWuFtn9AchPf872zqRHjYfjgageX3uwo9vBRQyXaEZr9dFR+18rUDeeEzOEmEP+kp6/Pvt3ZlhPyYm/wt4/fkk9Miokg/yUPnk3MDU81oSuxAw8EHYjLfF59SWQpQObxMaJR68vVKH32Ombct2ZGyzM7L5Tz3+rkk7C4z9oQIDAQAB__
  * Домен страницы оплаты __checkout.begateway.com__

  Используйте следующие данные тестовой карты:

    * номер карты __4200000000000000__
    * имя на карте __JOHN DOE__
    * срок действия карты __01/30__, чтобы получить успешный платеж
    * срок действия карты __10/30__, чтобы получить неуспешный платеж
    * CVC __123__
