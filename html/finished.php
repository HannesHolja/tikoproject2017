<?php    
    require_once("../resources/config.php");
     
    require_once(TEMPLATES_PATH . "/header.php");


    session_start();

    //If user has not logged in or exercisesession has not been started redirect them to index.php
    if(!isset($_SESSION['k_id'])){
        header('Location: index.php');
        exit;
    }

    if(!isset($_SESSION['s_id']))
    {
    	header('Location: menu.php');
    	exit;
    }

    ?>

    <div class="container container-table">
        <div id="content">
            <h2>Tehtävät on nyt tehty</h2>
            <br>
            <p>Viimeisen tehtävän tulos:
            <?php 

                if(isset($_SESSION['exercise_result'])){
                    echo $_SESSION['exercise_result'];
                }

             ?>

            </p>

            <a href="logout.php">Lopeta</a>

        </div>
    </div>


<?php

    /* Exercise session finished, save finish time to database */

    if(!$con = pg_connect($config['dbconnection'])){
        die("Tietokantayhteyden luominen epäonnistui.");
    }

    pg_query("BEGIN");

    $result = pg_query("UPDATE sessio SET lopetushetki=current_timestamp(0) WHERE s_id=" . $_SESSION['s_id']);

    if(!$result)
    {
        echo "Virhe tulosten tallentamisessa tietokantaan: " . pg_last_error();
        pg_query("ROLLBACK");
        exit;
    }

    pg_query("COMMIT");

    pg_close($con);
    
    session_unset();

    require_once(TEMPLATES_PATH . "/footer.php");
?>