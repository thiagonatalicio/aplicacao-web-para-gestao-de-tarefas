<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cors
{
    public function handle()
    {
        // Somente seu frontend permitido
        header("Access-Control-Allow-Origin: http://localhost:3000");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        // Para requisições OPTIONS
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit; 
        }
    }
}
