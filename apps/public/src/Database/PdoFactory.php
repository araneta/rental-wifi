<?php
// src/Database/PdoFactory.php
namespace App\Database;

use PDO;

class PdoFactory
{
    public static function create(): PDO
    {
        $url = parse_url($_ENV['DATABASE_URL']);
        $host = $url['host'];
        $dbname = ltrim($url['path'], '/');
        $user = $url['user'];
        $pass = $url['pass'];

        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }
}
