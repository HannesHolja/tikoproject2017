<?php    
    require_once("../resources/config.php");
     
    require_once(TEMPLATES_PATH . "/header.php");

    //	Try to connect to database

   	if (!$connection = pg_connect($config['dbconnection']))
   	die("Tietokantayhteyden luominen epäonnistui.");

   //	Get users from database
   $result = pg_query("SELECT * FROM kayttaja");

   if (!$result) {
	  echo "Virhe kyselyssä.\n";
	  exit;
	}


   //	Print out users from database
   while ($row = pg_fetch_row($result)) {
  	echo "Opiskelijan $row[0]  numero on $row[1]";
  	echo "<br />\n";
	}

pg_close($yhteys);

?>
<div id="container">
    <div id="content">
        
        

    </div>

</div>
<?php
    require_once(TEMPLATES_PATH . "/footer.php");
?>