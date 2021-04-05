<script defer type="text/javascript" src="{$js_url|escape}"></script>

<div id="begateway_payment_form_container" data-checkout_url="{$checkout_url|escape}">
    <form method="get" action="{$payment_url|escape}"
          id="begateway_payment_form" accept-charset="utf-8"
          onSubmit="okaycms_start_begateway_payment_widget(event);">

        <input id="begateway_payment_token" type="hidden" name="token" value="{$token|escape}">

        <input type="submit" class="button" id="submit_begateway_payment_form"
               value="{$submit_begateway_payment}">
    </form>
</div>
<br>
<div>
    <a class="button" id="begateway_cancel_payment" href="{$cancel_url|escape}">{$cancel_begateway_payment}</a>
</div>
