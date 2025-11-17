<?php
// public/index.php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\EnvLoader;

// Cargar variables de entorno (.env)
EnvLoader::load(__DIR__ . '/../');


require_once __DIR__ . '/../app/Config/config.php';
// Cargar rutas
require_once __DIR__ . '/../app/Routes.php';
