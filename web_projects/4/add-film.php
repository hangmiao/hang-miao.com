<!--
4 - Kevin Bacon
Hang Miao


Assignment Description: Accessing a database through PHP and manipulating database data using an HTML form

This is solution to part2, allowing users to enter a new film along with actors appearing in that film, the director and genre and do the validation.

- Actor or director is checked whether in the DB, and movie is not checked because there could be many movie with the same name.
- Movie name and year are checked.
- HTML special characters won't change the layout of the page.
-->


<?php
include("top.html");
include("common.php");

//get database
$db = openDB();

// check if the second form is set
if (isset($_REQUEST['newMovie'])) {
    //get inputs from the form
    $movieName = htmlspecialchars($_REQUEST['movieName']);
    $movieYear = htmlspecialchars($_REQUEST['movieYear']);
    $actorsFirstName = htmlspecialchars($_REQUEST['actorsFirstName']);
    $actorsLastName = htmlspecialchars($_REQUEST['actorsLastName']);
    $directorsFirstName = htmlspecialchars($_REQUEST['directorsFirstName']);
    $directorsLastName = htmlspecialchars($_REQUEST['directorsLastName']);
    $movieGenre = htmlspecialchars($_REQUEST['movieGenre']);

// when user inputs are valid    
    if (checkInputs($movieName, $movieYear, $actorsFirstName, $actorsLastName, $directorsFirstName, $directorsLastName, $db)) {
        try {
            // get largest movie id number to prepare insertion
            $maxMovieID = getCurrentMovieID($db);

            $stmt = $db->prepare("INSERT INTO movies (id, name, year) VALUES (:id, :movieName, :movieYear)");
            $stmt->bindParam(":id", $maxMovieID);
            $stmt->bindParam(":movieName", $movieName);
            $stmt->bindParam(":movieYear", $movieYear);
            $stmt->execute();



            // update table role
            if ($actorsFirstName != "" && $actorsLastName != "") {
                // $idActor is not -1
                $idActor = getActorIDByNames($actorsFirstName, $actorsLastName, $db);

                $stmt = $db->prepare("INSERT INTO roles (actor_id, movie_id) VALUES (:idActor, :movie_id)");
                $stmt->bindParam(":idActor", $idActor);
                $stmt->bindParam(":movie_id", $maxMovieID);
                $stmt->execute();

                // update actor role's film_count column
                $stmt = $db->prepare("UPDATE actors SET film_count=film_count +1 WHERE id = :idActor");
                $stmt->bindParam(":idActor", $idActor);
                $stmt->execute();
            }

            // update table movies_directors
            if ($directorsFirstName != "" && $directorsFirstName != "") {
                $idDirector = getDirectorIDByNames($directorsFirstName, $directorsLastName, $db);
                $stmt = $db->prepare("INSERT INTO movies_directors (director_id, movie_id) VALUES (:idDirector, :movie_id)");
                $stmt->bindParam(":idDirector", $idDirector);
                $stmt->bindParam(":movie_id", $maxMovieID);
                $stmt->execute();
            }
            
        } catch (PDOException $e) {
            die("Error: {$e->getMessage()}");
        }
        print "Movie: " . $movieName . ", " . $movieYear . " added successfully.";
    }
}
?>
<?php include("bottom.html"); ?>