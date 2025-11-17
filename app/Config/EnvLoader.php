<?php

namespace App\Config;

use Dotenv\Dotenv;

class EnvLoader
{
    public static function load(string $basePath)
    {
        // Carga el autoload de Composer si no está ya cargado
        if (!class_exists(Dotenv::class)) {
            require_once $basePath . '/vendor/autoload.php';
        }

        // Cargar archivo .env si existe
        if (file_exists($basePath . '/.env')) {
            $dotenv = Dotenv::createImmutable($basePath);
            $dotenv->load();
        } else {
            error_log('[EnvLoader] Archivo .env no encontrado en ' . $basePath);
        }
    }
}
