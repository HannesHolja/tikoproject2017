<?php
    session_start();
    require_once("../resources/config.php");
     
    /* Check that user has logged in */
    if(!isset($_SESSION['k_id'])){
        header('Location: index.php');
        exit;
    }
    /* Redirect unauthorized users */
    if(!in_array("exercise_list_cp", $_SESSION['oikeudet']))
    {
        header('Location: menu.php');
        exit;
    }

    /* Redirect out of the page if form is not submited */
    if($_SERVER["REQUEST_METHOD"] != "POST")
    {
        header('Location: exercise_list_cp.php');
        exit;
    }

    $t_id = $_POST['t_id'];

    echo "ID: ON " . $t_id . " Prööt<br>";

    $kuvaus = $_POST['kuvaus'];
    $kyselytyyppi = $_POST['kyselytyyppi'];
    $tehtavanluoja = $_POST['tehtavanluoja'];
    $luontipvm = $_POST['luontipvm'];
    $esimvastaus = $_POST['esimvastaus'];
    $k_id = $_POST['k_id'];
    $toiminto = $_POST['toiminto'];

    if($toiminto == "luo")
    {
        if(!$con = pg_connect($config['dbconnection'])){
            die("Tietokantayhteyden luominen epäonnistui kayttaja.");
        }
        pg_query("BEGIN");

        $result = pg_query("INSERT INTO tehtava (kuvaus, kyselytyyppi, tehtavanluoja, luontipvm, esimvastaus, k_id) VALUES('$kuvaus', '$kyselytyyppi', '" . $_SESSION['nimi'] . "', current_date, '$esimvastaus', " . $_SESSION['k_id'] . ")");

        if(!$result)
        {
            echo "Tehtävän luominen epäonnistui: " . pg_last_error();
            pg_query("ROLLBACK");
            exit;
        }

        pg_query("COMMIT");
        pg_close($con);

        header('Location: exercise_list_cp.php');
        exit;

    }
    elseif($toiminto == "muokkaa")
    {
        if(!$con = pg_connect($config['dbconnection'])){
            die("Tietokantayhteyden luominen epäonnistui kayttaja.");
        }
        pg_query("BEGIN");

        $result = pg_query("UPDATE tehtava SET kuvaus='" . $kuvaus . "', kyselytyyppi='" . $kyselytyyppi . "', esimvastaus='" . $esimvastaus . "' WHERE t_id=$t_id");

        if(!$result)
        {
            echo "Tehtävän luominen epäonnistui: " . pg_last_error();
            pg_query("ROLLBACK");
            exit;
        }

        pg_query("COMMIT");
        pg_close($con);

        header('Location: exercise_list_cp.php');
        exit;
    }
    else
    {
        echo "Virhe!";
        exit;
    }


?>