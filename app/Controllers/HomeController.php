<?php

namespace App\Controllers;

use App\Core\View;

class HomeController extends BaseController
{
    public function index()
    {
        View::render('home/index.php', [
            'title' => 'Dashboard | Enterprise ERP',
        ]);
    }
}
