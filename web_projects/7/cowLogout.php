<?php
session_save_path("php_tmp");
session_start();


unset($_SESSION["uName"]);
unset($_SESSION["uPsw"]);

session_destroy();
session_regenerate_id(TRUE);  # flushes out session ID number
//session_start();
?>
