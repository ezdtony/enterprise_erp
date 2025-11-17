<?php

namespace App\Controllers;

use App\Models\MenuModel;

class BaseController
{
    protected $modules;
    protected $submenus;

    public function __construct()
    {
        $menuModel = new MenuModel();

        $this->modules  = $menuModel->getMainModules();
        $this->submenus = $menuModel->getSubModules();

        // Disponibles globalmente en las vistas
        $GLOBALS['modules']  = $this->modules;
        $GLOBALS['submenus'] = $this->submenus;
    }
}
