<!--
4 - Kevin Bacon
Hang Miao


Assignment Description: Accessing a database through PHP and manipulating database data using an HTML form

This page is to show search results for all films for a given actor.-->


<?php
include("top.html");
include("common.php");

//get database
$db = openDB();

// check if the first form is set
if (isset($_REQUEST['allMovies'])) {

//get inputs from the form
    $firstname = htmlspecialchars($_REQUEST['firstname']);
    $lastname = htmlspecialchars($_REQUEST['lastname']);

// get actor id
    $id = getActorIDByNames($firstname, $lastname, $db);
    ?>

    <h1>Results for <?php print $firstname . " " . $lastname ?></h1>

    <?php
// when the given actor is in the db
    $moviesRows = getAllMoviesByActorID($db, $id);
    printTable($db, $moviesRows, 0, $firstname, $lastname);
}

include("bottom.html");
?>