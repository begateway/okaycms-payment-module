<div>
    <form method="post" action="{$paymentUrl|escape}" id="begateway_payment_form" accept-charset="utf-8">
        <input type="hidden" name="token" value="{$token|escape}">
        <input type="submit" class="button" id="submit_begateway_payment_form" value="{$submit_begateway_payment}">
    </form>
</div>
<br>
<div>
    <a class="button" href="{$cancelUrl|escape}">{$cancel_begateway_payment}</a>
</div>
