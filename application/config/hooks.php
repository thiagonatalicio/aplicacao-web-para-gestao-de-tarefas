<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cors
{
    public function handle()
    {
        $uri = $_SERVER['REQUEST_URI'];
        if (strpos($uri, '/tarefas/api') !== false) {
            header("Access-Control-Allow-Origin: http://localhost:3000");
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
            header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

            if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
                http_response_code(200);
                exit();
            }
        }
    }
}

