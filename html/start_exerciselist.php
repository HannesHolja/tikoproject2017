<?php

    require_once("../resources/config.php");

    session_start();

    //If user has not logged in redirect them to index.php or listId is not set
    if(!isset($_SESSION['k_id']) || !isset($_GET['listId'])){
        header('Location: index.php');
        exit;
    }

    //Get exercesises from database and save them to array

    if(!$con = pg_connect($config['dbconnection'])){
    	die("Tietokantayhteyden luominen epäonnistui.");
    }

    $result = pg_query("SELECT * FROM tehtava, tehtava_tehtavalista WHERE tl_id=$_GET[listId] AND tehtava.t_id=tehtava_tehtavalista.t_id ORDER BY tehtava.t_id ASC");

    if(!$result)
    {
    	echo "Virhe kyselyssä.\n";
        exit;
    }



    $exer_arr = pg_fetch_all($result);

    $_SESSION['tehtava_taulukko'] = $exer_arr;

    //Create new record to databases Session table

    /* START NEW SESSION -TRANSACTION */

    pg_query("BEGIN");

    $result = pg_query("INSERT INTO 
    	sessio (k_id ,tl_id, lopetushetki, alkamishetki) 
    	VALUES ($_SESSION[k_id], $_GET[listId], current_timestamp(0), current_timestamp(0)) RETURNING *");

    if(!$result)
    {
    	pg_query("ROLLBACK");
    	echo "Virhe kyselyssä.\n";
    	exit;
    }

    $arra = pg_fetch_array($result);


    pg_query("COMMIT");

    /* UNSET ALL EXERCISE SESSION VARIABLES JUST IN CASE */

    unset($_SESSION['s_id']);

    

    $_SESSION['s_id'] = $arra['s_id'];
    $_SESSION['yrityksia'] = 3;
    $_SESSION['tehtnro'] = 0;
    $_SESSION['mis'] = "";






    pg_close($con);

    header("Location: exercise.php");


?>