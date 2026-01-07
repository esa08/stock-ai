<?php
session_start();

$_SESSION['is_login'] = false;
unset($_SESSION['username']);

session_destroy();
exit;