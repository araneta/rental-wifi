<?php

namespace App\Service;

use DrizzlePHP\DrizzlePHP;
use PDO;

class DrizzleService
{
    private DrizzlePHP $drizzle;

    public function __construct(PDO $pdo)
    {
        $this->drizzle = new DrizzlePHP($pdo);
    }

    public function getDb(): DrizzlePHP
    {
        return $this->drizzle;
    }
}
