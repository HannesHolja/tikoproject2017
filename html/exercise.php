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
    	header('Location: index.php');
    	exit;
    }

    $tehttaulu = $_SESSION['tehtava_taulukko'];
    $tehtnro = $_SESSION['tehtnro'];

	/*if(!$con = pg_connect($config['dbconnection'])){
	    	die("Tietokantayhteyden luominen epäonnistui.");
	}   */ 
?>
<div id="container">
    <div id="content">

    	<div id="mistakes">
    	<?php
    	if(isset($_SESSION['mis']))
        {
            echo $_SESSION['mis'];
        }
    	?>
    	</div>

    	<div id="teht_kuvaus">
    	<?php echo "<h2>Tehtävä: " . ($tehtnro + 1) . "</h2><br>";
    		echo "<p>" . $tehttaulu[$tehtnro]['kuvaus'] . "</p><br>";?>
    		<h2>Tietokannan rakenne</h2>
    		<img src="img/tehtavakanta.jpeg">
    		<br>
            <?php echo "Yrityksiä jäljellä: " . $_SESSION['yrityksia']; ?>
    	</div>
        
        <form action="check_answer.php" method="post">
        	Kysely: <textarea rows="5" cols="40" name="user_query"></textarea><br>
        	<input type="submit">
        </form>
        

    </div>

</div>
<?php
    require_once(TEMPLATES_PATH . "/footer.php");
?>