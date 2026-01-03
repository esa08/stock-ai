<?php
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'stock';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, 3307);
if ($mysqli->connect_error) {
  die('Database connection failed.');
}

$mysqli->set_charset('utf8mb4');