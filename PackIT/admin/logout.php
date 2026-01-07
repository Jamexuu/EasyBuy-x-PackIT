<?php
require_once '../api/classes/Auth.php';
Auth::logout();
header("Location: index.php");
exit();
