{$meta_title = $btr->okaycms__begateway__description_title|escape scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->okaycms__begateway__description_title|escape}
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="alert alert--icon alert--info">
            <div class="alert__content">
                <div class="alert__title">{$btr->alert_info|escape}</div>
                <p>
                    {$btr->okaycms__begateway__description_part_1}<br>
                    {$btr->okaycms__begateway__description_part_2}<br>
                    {$btr->okaycms__begateway__description_part_3}<br>
                    {$btr->okaycms__begateway__description_part_4}
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="boxed">
            <div>
                <img src="{$rootUrl}/Okay/Modules/OkayCMS/BeGateway/Backend/design/images/begateway.png">
            </div>
        </div>
    </div>
</div>