<?php

use Dotenv\Dotenv;

// Automatically load .env values into $_ENV
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();
