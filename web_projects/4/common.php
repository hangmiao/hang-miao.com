<!--
4 - Kevin Bacon
Hang Miao


Assignment Description: Accessing a database through PHP and manipulating database data using an HTML form

This is the common code shared by pages.
-->
<?php

// get database
function openDB() {
// PHP Initialization
    ini_set('display_errors', 1);
    error_reporting(E_ALL | E_STRICT);

	$host = 'us-cdbr-azure-west-a.cloudapp.net';
    $dbuser = 'b6a3157d8e58b7';
    $dbpass = '9b70ad72';
    $dbname = 'hangAoTRFkVWAJbV';
    try {
	    $db = new PDO( "mysql:host=$host;dbname=$dbname", $dbuser, $dbpass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        header("HTTP/1.1 500 Server Error");
        die("HTTP/1.1 500 Server Error: Database Unavailable ({$e->getMessage()})");
    }
    return $db;
}

function getActorIDByNames($firstnameOriginal, $lastnameOriginal, $db) {
// add ' ' to the name
    $lastname = $db->quote($lastnameOriginal);
// firstname starts with what the user typed
    $firstname = $db->quote($firstnameOriginal . '%');

//search for actor id that the first names starts with the user input 
//and last name are exactly the same as user input 
//ordered by film number and id number
    $sqlMatching = "SELECT id, first_name FROM actors "
            . "WHERE last_name=$lastname "
            . "AND first_name LIKE $firstname "
            . "ORDER BY film_count DESC, id ASC;";
    $rows = $db->prepare($sqlMatching);
    try {
        $rows->execute();
    } catch (PDOException $ex) {
        print ("Error details: <?= $ex->getMessage()?>)");
    }
// only need to get the frist row when there's several matches of the same last name
// also check if the given actor is in the db
    $firstRow = $rows->fetch();

// check if the actor is in the db
    if ($rows->rowCount() != 0) {
        $id = $firstRow["id"];
        return $id;
    } else {
// given actor is not in the db
        print "Actor " . $firstnameOriginal . " " . $lastnameOriginal . " not found.";
        return -1;
    }
}

function getDirectorIDByNames($firstnameOri, $lastnameOri, $db) {
// add ' ' to the name
    $lastname = $db->quote($lastnameOri);
// firstname starts with what the user typed
    $firstname = $db->quote($firstnameOri . '%');

//search for director id that the first names starts with the user input 
//and last name are exactly the same as user input 
    $sqlMatching = "SELECT id FROM directors "
            . "WHERE last_name=$lastname "
            . "AND first_name LIKE $firstname;";
    $rows = $db->prepare($sqlMatching);
    try {
        $rows->execute();
    } catch (PDOException $ex) {
        print ("Error details: <?= $ex->getMessage()?>)");
    }
// only need to get the frist row when there's several matches of the same last name
// also check if the given actor is in the db
    $firstRow = $rows->fetch();

    $rowCount = $rows->rowCount();

    if ($rowCount == 1) {
        $id = $firstRow["id"];
        return $id;
    } else {
//  given actor is not in the db
        print "Director " . $firstnameOri . " " . $lastnameOri . " not found.";
        return -1;
    }
}

function getAllMoviesByActorID($db, $id) {
    $id = $db->quote($id);
// join three tables to get all the movies for a given actor
    $sqlSearchAll = " SELECT name, year FROM actors, movies, roles "
            . "WHERE $id = actors.id "
            . "AND actors.id = roles.actor_id "
            . "AND movies.id = roles.movie_id "
            . "ORDER BY year DESC, name ASC;";

    $rows = $db->prepare($sqlSearchAll);
    try {
        $rows->execute();
    } catch (PDOException $ex) {
        print ("Error details: <?= $ex->getMessage()?>)");
    }
    return $rows;
}

function getConnectedMoviesByActorID($db, $id, $firstname, $lastname) {
    $id = $db->quote($id);

// search movies of the given person that is connected to Kevin Bacon's
    $sqlSearchConnected = "SELECT DISTINCT movies.name, movies.year "
            . "FROM movies, actors AS a0, actors AS a1, roles AS r0, roles AS r1 "
            . "WHERE a0.id=$id "
            . "AND a0.id = r0.actor_id "
            . "AND a1.id = r1.actor_id "
            . "AND movies.id = r0.movie_id  "
            . "AND a1.first_name='Kevin' "
            . "AND a1.last_name= 'Bacon' "
            . "ORDER BY year DESC, name ASC;";

    $rows = $db->prepare($sqlSearchConnected);
    try {
        $rows->execute();
    } catch (PDOException $ex) {
        print ("Error details: <?= $ex->getMessage()?>)");
    }

    $rowCount = $rows->rowCount();
    if ($rowCount != 0) {
        return $rows;
    } else {
// given actor is not in any films with Kevin Bacon
        print $firstname . " " . $lastname . " wasn't in any films with Kevin Bacon";
        return NULL;
    }
}

function printTable($db, $rows, $isAll, $firstname, $lastname) {
    $lineNum = 1;
// get total numbers of the result set

    $rowNum = $rows->rowCount();

// when there's at least one film
    if ($rowNum != 0) {
        ?>
        <table class="tables">
            <!--all films-->
            <?php if ($isAll == 0) { ?>
                <caption>All films</caption>
                <?php
            }
            // films with Kevin Bacon
            if ($isAll == 1) {
                ?>
                <caption>Films with <?php print $firstname . " " . $lastname ?> and Kevin Bacon </caption>
            <?php } ?>

            <tr><th>#</th><th>Title</th><th>Year</th></tr>

            <?php foreach ($rows as $row) { ?>
                <tr>
                    <td><?php print $lineNum; ?></td>
                    <td><?php print $row["name"]; ?></td>
                    <td><?php print $row["year"]; ?></td>
                </tr>
                <?php
                $lineNum = $lineNum + 1;
            }
            ?>
        </table>
        <?php
    }
}

// a new function begins
// check form inputs
function checkInputs($movieName, $movieYear, $actorsFirstName, $actorsLastName, $directorsFirstName, $directorsLastName, $db) {

    if ($movieName == null || $movieYear == null) {
        print "Please complete mandotory fields: movie name, year and genre.";
        return FALSE;
    } else {

// movie year from 1900 to 2014
        if ((!preg_match("/^[a-zA-Z]*/", $movieName)) || (!preg_match("/^(19\d\d|20[01][01234])$/", $movieYear))) {
            print "Movie info invalid, please try again.";
            return FALSE;
        }

// use inputs actorsFirstName or actorsLastName or both of them
        if ($actorsFirstName != "" || $actorsLastName != "") {
            $idActor = getActorIDByNames($actorsFirstName, $actorsLastName, $db);
// actor not in the db
            if ($idActor == -1) {
                exit(0);
            }
        }

        // use inputs directorsFirstName or directorsFirstName or both of them
        if ($directorsFirstName != "" || $directorsFirstName != "") {
            $idDirector = getDirectorIDByNames($directorsFirstName, $directorsLastName, $db);
            // director not in the db
            if ($idDirector == -1) {
                exit(0);
            }
        }
        return TRUE;
    }  //end of movie inputs
}

// get the max movie id in the db for inserting movies because the default id is 0 instead of auto-increment
function getCurrentMovieID($db) {

    $sql = "SELECT MAX(id) AS maxID FROM movies;";

    $rows = $db->prepare($sql);
    try {
        $rows->execute();
    } catch (PDOException $ex) {
        print ("Error details: <?= $ex->getMessage()?>)");
    }

    $firstRow = $rows->fetch();
    $id = $firstRow["maxID"];
    return $id + 1;
}
