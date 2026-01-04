<?php
require_once __DIR__ .'/../api/classes/Auth.php';

Auth::logout();
header('Location: login.php');
exit();