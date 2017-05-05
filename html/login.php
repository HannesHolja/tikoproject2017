<?php
    require_once("../resources/config.php");

 	if ($_SERVER["REQUEST_METHOD"] == "POST") {

 		if(isset($_POST['opNro']) && !empty($_POST['opNro'])){
			//Connect to database
			if (!$con = pg_connect($config['dbconnection']))
   			die("Tietokantayhteyden luominen epäonnistui.");

			//Query to validate user login
			$result = pg_query("SELECT * FROM kayttaja WHERE opiskelijanumero=$_POST[opNro]");

			if(!$result)
			{
				echo "Virhe kirjautumisessa! " . $_POST['opNro'];
				exit;
			}

			//Fetch data from user table and init session
			$arr = pg_fetch_array($result);
			if(empty($arr)){
				echo "Virhellinen opiskelijanumero.\n";
				exit;
			}

			session_start();
			$_SESSION["k_id"] = $arr["k_id"];
			$_SESSION["opiskelijanumero"] = $arr["opiskelijanumero"];
			$_SESSION["nimi"] = $arr["nimi"];
			$_SESSION["paa_aine"] = $arr["paa_aine"];
			$_SESSION["r_id"] = $arr["r_id"];


			pg_close($con);

			header('Location: menu.php');	
 		}

   


	}
	else
	{
		header('Location: index.php');
		exit;
	}
	

?>