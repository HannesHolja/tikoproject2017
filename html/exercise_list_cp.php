<?php
    session_start();
    require_once("../resources/config.php");
     
    require_once(TEMPLATES_PATH . "/header.php");

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

    if(!$con = pg_connect($config['dbconnection'])){
        die("Tietokantayhteyden luominen epäonnistui.");
    }

    if($_SESSION['rooli'] == "opettaja")
    {
        $result1 = pg_query("SELECT * FROM tehtava WHERE k_id=" . $_SESSION['k_id'] . " ORDER BY tehtava.t_id ASC");
        $result2 = pg_query("SELECT * FROM tehtavalista WHERE k_id=" . $_SESSION['k_id'] . " ORDER BY tehtavalista.tl_id ASC");
    }
    elseif($_SESSION['rooli'] == "admin")
    {
        $result1 = pg_query("SELECT * FROM tehtava ORDER BY tehtava.t_id ASC");
        $result2 = pg_query("SELECT * FROM tehtavalista ORDER BY tehtavalista.tl_id ASC");
    }

    if(!$result1)
    {
        echo "Virhe tehtävien haussa tietokannasta";
        exit;
    }
    if(!$result2)
    {
        echo "Virhe tehtävälistojen haussa tietokannasta";
        exit;
    }



    $arr1 = pg_fetch_all($result1);
    $arr2 = pg_fetch_all($result2);

    function print_exercise_exerciselist_cp($result_array, $type)
    {

        if(!empty($result_array)){

            echo "<table class='table table-hover'>";
            echo "<thead>";
            echo "<tr class='query_table_header'>";
            foreach ($result_array[0] as $key => $value) {
                echo "<td>" . $key . "</td>";
            }
            echo "<td>Toiminnot</td>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

            

            for ($i=0; $i < count($result_array); $i++) {

                echo "<tr class='query_table_rows'>";

                foreach ($result_array[$i] as $value) {
                   echo "<td>" . $value . "</td>";
                }
                if($type == "tehtävä"){
                    echo "<td>";
                    echo "<form action='exercise_form.php' method='post'>
                            <input type='hidden' name='toiminto' value='muokkaa'>
                            <input type='hidden' name='id' value='" . $result_array[$i]['t_id'] . "'>
                            <input type='hidden' name='kuvaus' value='" . htmlentities($result_array[$i]['kuvaus'], ENT_QUOTES) . "'>
                            <input type='hidden' name='kyselytyyppi' value='" . $result_array[$i]['kyselytyyppi'] . "'>
                            <input type='hidden' name='tehtavanluoja' value='" . $result_array[$i]['tehtavanluoja'] . "'>
                            <input type='hidden' name='luontipvm' value='" . $result_array[$i]['luontipvm'] . "'>
                            <input type='hidden' name='esimvastaus' value='" . htmlentities($result_array[$i]['esimvastaus'], ENT_QUOTES) . "'>
                            <input type='hidden' name='k_id' value='" . $result_array[$i]['k_id'] . "'>
                            <input type='submit' value='muokkaa'>
                            </form>";
                    
                    echo "</td>";

                    echo "</tr>";
                }
                else{
                    echo "<td>";
                    echo "<form action='exerciselist_form.php' method='post'>
                            <input type='hidden' name='toiminto' value='muokkaa'>
                            <input type='hidden' name='id' value='" . $result_array[$i]['tl_id'] . "'>
                            <input type='hidden' name='k_id' value='" . $result_array[$i]['k_id'] . "'>
                            <input type='hidden' name='kuvaus' value='" . htmlentities($result_array[$i]['kuvaus'], ENT_QUOTES) . "'>
                            <input type='submit' value='muokkaa'>
                            </form>";
                            
                    echo "</td>";

                    echo "</tr>";
                }



            }          
            echo "</tbody>";

            echo "</table>";
        }
        

        echo "<br>";
    }

?>
<div class="container container-table">
    <div id="content">
        
        <h1>Tehtävät ja tehtävälistat</h1>

        <br>

        <h2>Tehtävät</h2>
        <?php 
        /* PRINT TEHTÄVÄT */

        print_exercise_exerciselist_cp($arr1, "tehtävä");


        ?>
        <form action="exercise_form.php" method="POST">
            <input type="hidden" name="toiminto" value="luo">
            <input type="submit" value="Luo uusi">
        </form>


        <br>
        <h2>Tehtävälistat</h2>
        <?php
        /* PRINT TEHTÄVÄLISTAT */

        print_exercise_exerciselist_cp($arr2, "tehtävälista");

        ?>

        <form action="exerciselist_form.php" method="POST">
            <input type="hidden" name="toiminto" value="luo">
            <input type="submit" value="Luo uusi">
        </form>

        <br>

    </div>

</div>
<?php
    require_once(TEMPLATES_PATH . "/footer.php");
?>