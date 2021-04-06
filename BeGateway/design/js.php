<?php

use Okay\Core\Router;
use Okay\Core\TemplateConfig\Js;


$js = [];

$js[] = (new Js('script.js'))->setPosition('footer')->setDefer(true)->setIndividual(true);


return $js;
