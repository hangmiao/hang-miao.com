<!--
4 - Kevin Bacon
Hang Miao


Assignment Description: Accessing a database through PHP and manipulating database data using an HTML form

This page is to show search results for all films with the given actor and Kevin Bacon
-->


<?php
include("top.html");
include("common.php");

//get database
$db = openDB();

// check if the second form is set
if (isset($_REQUEST['moviesWithBacon'])) {
//get inputs from the form
    $firstname = htmlspecialchars($_REQUEST['firstname']);
    $lastname = htmlspecialchars($_REQUEST['lastname']);

    // get actor id
    $id = getActorIDByNames($firstname, $lastname, $db);
    ?>

    <h1>Results for <?php print $firstname . " " . $lastname ?></h1>

    <?php
    if ($id != -1) {
        $moviesRows = getConnectedMoviesByActorID($db, $id, $firstname, $lastname);
        // given actor is in some films with Kevin Bacon
        if ($moviesRows != NULL) {
            printTable($db, $moviesRows, 1, $firstname, $lastname);
        }
    }
}
// end of isset
include("bottom.html");
?>