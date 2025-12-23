<?php
require 'Auth.php';

Auth::logout();
header("Location: /EasyBuy-x-PackIT/EasyBuy/index.php");
exit();