<?php
session_save_path("php_tmp");
session_start();

if (isset($_SESSION["uName"])) {
    //    build file name
    $filename = "list_" . $_SESSION["uName"] . ".json";
// read from file
    if (file_exists($filename)) {
        $text = file_get_contents($filename);
        echo $text;
    } else {
        // create initial json obj
        $initialText = '{ "items": [ ] }';
        file_put_contents($filename, $initialText);
        echo "File created.";
    }
}
?>