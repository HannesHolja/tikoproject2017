<?php
	
	require_once("../resources/config.php");

    session_start();

    //Check if user should be relocated to index.php

    if(!isset($_SESSION['k_id'])){
        header('Location: index.php');
        exit;
    }

    /*if(!isset($_SESSION['s_id']))
    {
    	header('Location: index.php');
    	exit;
    }*/

    if ($_SERVER["REQUEST_METHOD"] == "POST") 
    {
    	if(isset($_POST['user_query']) && !empty($_POST['user_query']))
    	{
    	$user_input = $_POST['user_query'];

    	$_SESSION['mis'] = "";

    	check_input($user_input);
    	}
    	else
    	{
    		echo "Virhe";
    	}
    }

    else
    {
    	echo "VIRHE!";
    }


    /* Exercise logic */

    function check_input($input)
    {
    	$puolipiste = true;
    	$sulje = true;
    	//Onko puolipiste lopussa jos ei niin väärin

    	if (substr($input, -1) == ';') 
    	{
    		echo "Puolipiste oikein";

    	}
    	else
    	{
    		$puolipiste = false;
    		$_SESSION['mis'] = $_SESSION['mis']."Kyselyn täytyy päättyä puolipisteeseen!\n";
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
    			$_SESSION['mis'] = $_SESSION['mis'] . "Kyselyssä on pariton määrä sulkeita!\n";
    		}
    		wrong();
    	}

    	/* EI TOIMI if(($parent1depth != 0) OR ($parent2depth != 0) OR ($parent3depth != 0))
    	{
    		echo "Väärä määrä sulkeita";
    	}*/

    }

    function compare_queries($arr1, $arr2)
    {

    }

    function run_user_query()
    {

		if (!$con = pg_connect($config['dbconnection']))
   			die("Tietokantayhteyden luominen epäonnistui.");

   		pg_query("BEGIN");

   		pg_query("SET search_path TO ");

		/* RUN PREVIOUS CORRECT QUERIES */

   		pg_query("ROLLBACK");

		//pg_query("SET search_path TO public");


    	return $result_arr;
    }

    function run_correct_query()
    {
    	if (!$con = pg_connect($config['dbconnection']))
   			die("Tietokantayhteyden luominen epäonnistui.");

   		pg_query("BEGIN");

   		pg_query("SET search_path TO ");

  		/* RUN PREVIOUS CORRECT QUERIES */

   		pg_query("ROLLBACK");

 		//pg_query("SET search_path TO public");


    	return $result_arr;
    }

    function correct()
    {
    
    }

    function wrong()
    {
    	$_SESSION['yrityksia'] -= 1;
    	if($_SESSION['yrityksia'] == 0)
    	{
    		echo "Yritykset loppuivat!";
    	}
    	else
    	{
    		header("Location: exercise.php");
    		exit;
    	}

    }

    function next_exercise()
    {

    }

    function exercise_list_finished()
    {

    }
?>