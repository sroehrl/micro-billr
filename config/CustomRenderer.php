<?php

namespace Config;

use I18nTranslate\Translate;
use Neoan\Helper\DataNormalization;

class CustomRenderer extends \Neoan\Render\Renderer
{
    static public function render(array|DataNormalization $data = [], $view = null): string
    {
        $t = new Translate();
        return preg_replace('/{{[^}]+}}/','', $t->translate(parent::render($data, $view)));

    }

}