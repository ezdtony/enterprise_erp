<?php

namespace App\Core;

class View
{
    public static function render($view, $data = [], $layout = 'layouts/main.php')
    {
        extract($data);
        ob_start();
        require __DIR__ . '/../Views/' . $view;
        $content = ob_get_clean();
        require __DIR__ . '/../Views/' . $layout;
    }
}
