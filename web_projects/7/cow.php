<?php
session_save_path("php_tmp");
session_start();
if (isset($_SESSION["uName"]) && isset($_SESSION["uPsw"])) {
    $arr = array("userName" => $_SESSION["uName"], "passWord" => $_SESSION["uPsw"]);
} else {
    $arr = array("userName" => null);
}
echo json_encode($arr);
?>

