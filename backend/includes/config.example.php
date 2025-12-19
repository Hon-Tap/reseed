<?php
// backend/includes/config.example.php

define('APP_ENV', getenv('APP_ENV') ?: 'production');

$DB_HOST = getenv('DB_HOST');
$DB_NAME = getenv('DB_NAME');
$DB_USER = getenv('DB_USER');
$DB_PASS = getenv('DB_PASS');

// Add other config values here
