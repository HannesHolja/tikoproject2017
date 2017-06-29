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

    $tl_id = $_POST['tl_id'];


    $kuvaus = $_POST['kuvaus'];
    $luontipvm = $_POST['luontipvm'];
    $k_id = $_POST['k_id'];
    $toiminto = $_POST['toiminto'];
    $valitut = $_POST['tehtavat'];
    $lkm = count($valitut);


    echo "Valitsit: <br>";
    foreach ($valitut as $key => $value) {
        echo $value . " ";
    }



    if($toiminto == "luo")
    {
        if(!$con = pg_connect($config['dbconnection'])){
            die("Tietokantayhteyden luominen epäonnistui kayttaja.");
        }
        pg_query("BEGIN");

        /* CREATE EXERCISELIST */
        $result = pg_query("INSERT INTO tehtavalista (tehtavien_lkm, kuvaus, pvm, k_id) VALUES ($lkm, '$kuvaus', current_date," . $_SESSION['k_id'] . " ) RETURNING tl_id");

        if(!$result)
        {
            echo "Tehtävälistan luominen epäonnistui: " . pg_last_error();
            pg_query("ROLLBACK");
            exit;
        }

        $retTL_ID = pg_fetch_row($result)[0];

        foreach ($valitut as $key => $value) {
            $result = pg_query("INSERT INTO tehtava_tehtavalista (t_id, tl_id) VALUES ($value, $retTL_ID)");
            if(!$result)
            {
                echo "tehtava_tehtavalistan luominen meni pieleen: " . pg_last_error();
                pg_query("ROLLBACK");
                exit;   
            }
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

        $result = pg_query("UPDATE tehtavalista SET tehtavien_lkm=$lkm, kuvaus='$kuvaus' WHERE tl_id=$tl_id");

        if(!$result)
        {
            echo "Tehtävälistan luominen epäonnistui: " . pg_last_error();
            pg_query("ROLLBACK");
            exit;
        }

        /* DELETE ALL TEHTAVA_TEHTAVALISTA CONNECTION BEFORE CREATING NEW ONES */
        $result = pg_query("DELETE FROM tehtava_tehtavalista WHERE tl_id=$tl_id");

        if(!$result)
        {
            echo "Tehtävä ja tehtävälistan linkin poistaminen ei onnistunut " . pg_last_error();
            pg_query("ROLLBACK");
            exit;
        }

        foreach ($valitut as $key => $value) {
            $result = pg_query("INSERT INTO tehtava_tehtavalista (t_id, tl_id) VALUES ($value, $tl_id)");
            if(!$result)
            {
                echo "tehtava_tehtavalistan luominen meni pieleen: " . pg_last_error();
                pg_query("ROLLBACK");
                exit;   
            }
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