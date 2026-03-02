<?php
require_once 'includes/config.php';
session_destroy();
session_start();
$_SESSION = [];
header('Location: index.php');
exit;
