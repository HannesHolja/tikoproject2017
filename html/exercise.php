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

    if($_SESSION['yrityksia'] <= 0)
    {
        header('Location: menu.php');
        exit;
    }

    $tehttaulu = $_SESSION['tehtava_taulukko'];
    $tehtnro = $_SESSION['tehtnro'];


    /* Yritys alkaa */

    if(!$con = pg_connect($config['dbconnection'])){
            die("Tietokantayhteyden luominen epäonnistui kayttaja.");
    }
    pg_query("BEGIN");

    /* INSERT INTO yritys */

    $t_id = $_SESSION['tehtava_taulukko'][$_SESSION['tehtnro']]['t_id'];
    $s_id = $_SESSION['s_id'];

    $result = pg_query("INSERT INTO yritys (t_id, s_id, yrityksen_alku) VALUES ($t_id, $s_id, current_timestamp(0)) RETURNING *");

    if(!$result)
    {
        pg_query("ROLLBACK");
        exit;
    }

    $y_id = pg_fetch_array($result)['y_id'];
    $_SESSION['y_id'] = $y_id;
    /* INSERT INTO yritys_tehtava_sessio */

    $result = pg_query("INSERT INTO yritys_tehtava_sessio VALUES ($y_id, $t_id, $s_id)");

    if(!$result)
    {
        pg_query("ROLLBACK");
        exit;
    }

    else
        pg_query("COMMIT");

    function print_query_result($result_array)
    {

        //print_r($result_array);
        if(!empty($result_array)){

            echo "<table class='table table-hover'>";
            echo "<thead>";
            echo "<tr class='query_table_header'>";
            foreach ($result_array[0] as $key => $value) {
                echo "<td>" . $key . "</td>";
            }
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

            

            for ($i=0; $i < count($result_array); $i++) {

                echo "<tr class='query_table_rows'>";

                foreach ($result_array[$i] as $value) {
                   echo "<td>" . $value . "</td>";
                }

                echo "</tr>";

            }          
            echo "</tbody>";

            echo "</table>";
        }
        

        echo "<br>";
    }

?>
<div class="container container-table">
    <div id="content">

    	<div id="mistakes">
    	<?php
        	if(isset($_SESSION['mis']))
            {
                echo $_SESSION['mis'];
            }
    	?>
    	</div>

        <div id="query_result">
        <?php

            if(isset($_SESSION['exercise_result'])){
                    echo $_SESSION['exercise_result'] . "<br>";
            }

            /* If user query was incorrect lets show results */
            if(isset($_SESSION['user_query_error'])){

                echo $_SESSION['user_query_error'];
            }
            elseif (isset($_SESSION['user_query_result'])) {
                echo "Kyselysi tuotti väärän palautuksen!<br>";

                echo "Kyselysi tuotti seuraavaa: <br>";
                print_query_result($_SESSION['user_query_result']);
                echo "Sen olisi pitänyt tuottaa: <br>";
                print_query_result($_SESSION['correct_query_result']);
            }
            unset($_SESSION['user_query_error']);
            unset($_SESSION['user_query_result']);
            unset($_SESSION['correct_query_result']);
            unset($_SESSION['exercise_result']);
        ?>
        </div>

        <br>

    	<div id="teht_kuvaus">
    	<?php echo "<h2>Tehtävä: " . ($tehtnro + 1) . "</h2><br>";
    		echo "<p>" . $tehttaulu[$tehtnro]['kuvaus'] . "</p><br>";?>
    		<h2>Tietokannan rakenne</h2>
    		<img src="img/tehtavakanta.jpeg">
    		<br>
            <p><?php echo "Yrityksiä jäljellä: {$_SESSION['yrityksia']}"; ?></p>
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