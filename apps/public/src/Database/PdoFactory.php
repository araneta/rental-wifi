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
        // ✅ Set session timezone (no SUPER privilege required)
        $pdo->exec("SET time_zone = '+07:00'");
        date_default_timezone_set('Asia/Jakarta');
        return $pdo;
    }
}
