<?php

use Okay\Core\TemplateConfig\Css;

$css[] = (new Css('style.css'))->setPosition('footer')->setIndividual(true);

return $css;
