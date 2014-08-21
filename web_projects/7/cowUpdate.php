<?php
session_save_path("php_tmp");
session_start();

$jsonStr = $_POST["jsonString"];

if (isset($_SESSION["uName"])) {
//    build file name
    $filename = "list_" . $_SESSION["uName"] . ".json";
    // write to file
    file_put_contents($filename, $jsonStr);
    echo "OK";
} else {
    echo "ERROR";
}
?>
