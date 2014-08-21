<?php
session_save_path("php_tmp");
session_start();

// get the credentials passed by ajax
$user = $_POST["user"];
$psw = $_POST["password"];

if ($user == "testuser" && $psw == "testpsw") {
    $resp = array("resp" => "OK");
//    initialize the session
    $_SESSION["uName"] = $user;
    $_SESSION["uPsw"] = $psw;
    echo json_encode($resp);
} else {
    $resp = array("resp" => "ERROR");
    echo json_encode($resp);
}
?>