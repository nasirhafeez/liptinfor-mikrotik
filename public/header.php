<?php

session_start();

if (!(isset($_SESSION['mac']) or isset($POST['mac']))) {
  exit('Cannot access this page directly');
}

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

$business_name = $_SERVER['BUSINESS_NAME'];