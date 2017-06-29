<?php
	
	require_once("../resources/config.php");

    session_start();

    //Check if user should be relocated to index.php

    if(!isset($_SESSION['k_id'])){
        header('Location: index.php');
        exit;
    }


    if ($_SERVER["REQUEST_METHOD"] == "POST") 
    {
    	if(isset($_POST['user_query']) && !empty($_POST['user_query']))
    	{
    	$user_input = $_POST['user_query'];

    	$_SESSION['mis'] = "";

	    	if($_SESSION['yrityksia'] <= 0)
	    	{
	    		echo "yritykset ovat loppu";
	    	}

    	check_input($user_input);
    	}
    	else
    	{
    		$_SESSION['exercise_result'] = "Yritä nyt edes!";
    		header("Location: exercise.php");
    		exit;
    	}
    }

    else
    {
    	echo "Lomakkeen lähetyksessä tapahtui virhe!";
    	header();
    	exit;
    }


    /* Exercise logic */

    function check_input($input)
    {
    	$puolipiste = true;
    	$sulje = true;
    	//Onko puolipiste lopussa jos ei niin väärin

    	if (substr($input, -1) != ';') 
    	{
			$puolipiste = false;
    		$_SESSION['mis'] = $_SESSION['mis']."Kyselyn täytyy päättyä puolipisteeseen! <br>";
    	}
 
    	//Onko sulkeita parillinen määrä jos ei niin väärin
    	$parent1depth = 0; // ()
    	$parent2depth = 0; // []
    	$parent3depth = 0; // {}
    	for ($i=0; $i < strlen($input); ++$i) { 
    		if($input[$i] == '(') $parent1depth++;
    		if($input[$i] == '[') $parent2depth++;
    		if($input[$i] == '{') $parent3depth++;

    		if($input[$i] == ')') $parent1depth--;
    		if($input[$i] == ']') $parent2depth--;
    		if($input[$i] == '}') $parent3depth--;
    	}

    	if($parent1depth != 0)
    	{
    		//echo "Väärä määrä sulkeita";
    		$sulje = false;
    	}
    	if($parent2depth != 0)
    	{
    		//echo "Väärä määrä sulkeita";
    		$sulje = false;

    	}
    	if($parent3depth != 0)
    	{
    		//echo "Väärä määrä sulkeita";
    		$sulje = false;
    	}

    	if(!$puolipiste OR !$sulje)
    	{
    		if(!$sulje)
    		{
    			$_SESSION['mis'] = $_SESSION['mis'] . "Kyselyssä on pariton määrä sulkeita! <br>";
    		}
    		wrong();
    	}

    	compare_queries($input, $_SESSION['tehtava_taulukko'][$_SESSION['tehtnro']]['esimvastaus']);

    	/* EI TOIMI JOSTAIN SYYSTÄ
    	if(($parent1depth != 0) OR ($parent2depth != 0) OR ($parent3depth != 0))
    	{
    		echo "Väärä määrä sulkeita";
    	}*/

    }

    function compare_queries($arr1, $arr2)
    {

    	/* Remove last semicolon from statements */

    	$user_stm = substr($arr1, 0, -1);
    	$correct_stm = substr($arr2, 0, -1);

    	$user_result = run_user_query($user_stm);
    	$correct_result = run_correct_query($correct_stm);

    	/* COMPARE RESULT ARRAYS */
    	if($user_result == $correct_result)
    	{
    		save_user_statement($arr1, 1);

    		if(isset($_SESSION['user_query_error']))
    			wrong();
    		else
    			correct();
    	}
    	else
    	{
    		/* QUERY WENT WRONG SO WE SHALL SAVE RESULTS TO SESSION ARRAY */

    		$_SESSION['user_query_result'] =  $user_result;
    		$_SESSION['correct_query_result'] = $correct_result;

    		save_user_statement($arr1, 0);
    		wrong();
    	}

    }

    function save_user_statement($stm, $corr)
    {
    	global $config;
    	if(!$con = pg_connect($config['dbconnection'])){
    		die("Tietokantayhteyden luominen epäonnistui kayttaja.");
    	}
   		pg_query("BEGIN");

   		$result = pg_query("UPDATE yritys SET vastaus='" . pg_escape_string($stm) . "', oikein=" . $corr . ", yrityksen_loppu=current_timestamp(0) WHERE y_id=".$_SESSION['y_id']);

		if(!$result)
		{
			echo "Virhe tulosten tallentamisessa tietokantaan: " . pg_last_error();
			pg_query("ROLLBACK");
			exit;
		}

		pg_query("COMMIT");

		pg_close($con);

    }

    function run_user_query($stm)
    {
    	global $config;

		if(!$con = pg_connect($config['dbconnection'])){
    		die("Tietokantayhteyden luominen epäonnistui kayttaja.");
    	}
   		pg_query("BEGIN");

   		pg_query("SET search_path TO esimerkkiaineisto");

		/* RUN PREVIOUS CORRECT QUERIES */
		if($_SESSION['tehtnro'] > 0)
			for ($i=0; $i < $_SESSION['tehtnro']; $i++) { 
				pg_query(substr($_SESSION['tehtava_taulukko'][$i]['esimvastaus']), 0, -1);
			}

		/* RUN QUERY */

		/* IF QUERY IS OTHER THAN SELECT THEN ADD RETURNING CLAUSE TO THE END */

  		if($_SESSION['tehtava_taulukko'][$_SESSION['tehtnro']]['kyselytyyppi'] != "select"){
  			$stm = $stm . " RETURNING *";
  		}

		$result = pg_query($stm);

		if(!$result)
		{
			/* USER MOST LIKELY DID SYNTAX MISTAKE OF SOME KIND 
				SAVE ERROR MESSAGE TO SESSION ARRAY AND SHOW IT TO USER */

			$_SESSION['user_query_error'] = "Kyselyn ajo palautti virheen: " . pg_last_error();
		}
		

		$result_arr = pg_fetch_all($result);

		/* ROLLBACK ALL CHANGES TO TEST DATABASE */

   		pg_query("ROLLBACK");

	 	pg_close($con);

    	return $result_arr;
    }

    function run_correct_query($stm)
    {
    	global $config;

    	if(!$con = pg_connect($config['dbconnection'])){
    		die("Tietokantayhteyden luominen epäonnistui vastaus.");
    	}
   		pg_query("BEGIN");

   		pg_query("SET search_path TO esimerkkiaineisto");

  		/* RUN PREVIOUS CORRECT QUERIES */
  		if($_SESSION['tehtnro'] > 0)
			for ($i=0; $i < $_SESSION['tehtnro']; $i++) { 
				pg_query(substr($_SESSION['tehtava_taulukko'][$i]['esimvastaus']), 0, -1);
		}

  		/* RUN QUERY */

  		/* IF QUERY IS OTHER THAN SELECT THEN ADD RETURNING CLAUSE TO THE END */

  		if($_SESSION['tehtava_taulukko'][$_SESSION['tehtnro']]['kyselytyyppi'] != "select"){
  			$stm = $stm . " RETURNING *";
  		}

		$result = pg_query($stm);

		if(!$result)
		{
			echo "VIRHE";
		}
		

		$result_arr = pg_fetch_all($result);


		/* ROLLBACK ALL CHANGES TO TEST DATABASE */

   		pg_query("ROLLBACK");

   		pg_close($con);



    	return $result_arr;
    }

    function correct()
    {

    	$_SESSION['exercise_result'] = "Edellinen tehtävä oli OIKEIN!";

    	next_exercise();
    }

    function wrong()
    {
    	$_SESSION['yrityksia'] -= 1;
    	if($_SESSION['yrityksia'] <= 0)
    	{
 	    	$_SESSION['exercise_result'] = "Edellinen tehtävä oli VÄÄRIN! ";
    		$_SESSION['exercise_result'] = $_SESSION['exercise_result'] . " Oikea ratkaisu oli: <br>" . $_SESSION['tehtava_taulukko'][$_SESSION['tehtnro']]['esimvastaus'];
    		$_SESSION['exercise_result'];    		
    		next_exercise();

    	}
    	else
    	{
    		header("Location: exercise.php");
    		exit;
    	}

    }

    function next_exercise()
    {
    	if(isset($_SESSION['tehtava_taulukko'][$_SESSION['tehtnro']+1]))
    		{
    			$_SESSION['tehtnro']++;
    			$_SESSION['mis'] = "";
    			$_SESSION['yrityksia'] = 3;
    			header("Location: exercise.php");
    			exit;
    		}
    		else
    		{
    			exercise_list_finished();
    		}
    }

    function exercise_list_finished()
    {
    	header("Location: finished.php");
    }
?>