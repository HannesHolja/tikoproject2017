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

    /* Redirect out of the page if form is not submited */
    if($_SERVER["REQUEST_METHOD"] != "POST")
    {
        header('Location: exercise_list_cp.php');
        exit;
    }

    $tl_id = $_POST['id'];



    $kuvaus = $_POST['kuvaus'];
    $pvm = $_POST['pvm'];
    $k_id = $_POST['k_id'];
    $toiminto = $_POST['toiminto'];

    if(!$con = pg_connect($config['dbconnection'])){
        die("Tietokantayhteyden luominen epäonnistui.");
    }

    if($_SESSION['rooli'] == "opettaja")
    {
        $result1 = pg_query("SELECT * FROM tehtava WHERE k_id=" . $_SESSION['k_id'] . " ORDER BY tehtava.t_id ASC");
        if(isset($tl_id)){
            $exercises_in_list = pg_query("SELECT t_id FROM tehtava_tehtavalista WHERE tl_id=$tl_id");
            if(!$exercises_in_list)
            {
                echo "Virhe tehtävä_tehtävälista haussa tietokannasta";
                exit;
            }
        }
    }
    elseif($_SESSION['rooli'] == "admin")
    {
        $result1 = pg_query("SELECT * FROM tehtava ORDER BY tehtava.t_id ASC");
        if(isset($tl_id)){
            $exercises_in_list = pg_query("SELECT t_id FROM tehtava_tehtavalista WHERE tl_id=$tl_id");
            if(!$exercises_in_list)
            {
                echo "Virhe tehtävä_tehtävälista haussa tietokannasta";
                exit;
            }
        }
    }

    if(!$result1)
    {
        echo "Virhe tehtävien haussa tietokannasta";
        exit;
    }
 

    $arr1 = pg_fetch_all($result1);
    $arr2 = pg_fetch_all_columns($exercises_in_list);



    function print_query_result($result_array, $arr2)
    {

        if(!empty($result_array)){

            echo "<table class='table table-hover'>";
            echo "<thead>";
            echo "<tr class='query_table_header'>";
            foreach ($result_array[0] as $key => $value) {
                echo "<td>" . $key . "</td>";
            }
                echo "<td>Lisää</td>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

            

            for ($i=0; $i < count($result_array); $i++) {

                echo "<tr class='query_table_rows'>";

                foreach ($result_array[$i] as $value) {
                   echo "<td>" . $value . "</td>";
                }
                if(in_array($result_array[$i]['t_id'], $arr2))
                    echo '<td><input type="checkbox" name="tehtavat[]" value="' . $result_array[$i]['t_id'] . '" checked /></td>';
                else
                    echo '<td><input type="checkbox" name="tehtavat[]" value="' . $result_array[$i]['t_id'] . '" /></td>';


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
        
        <h1>Tehtävälistan muokkaaminen/luominen</h1>

        
        <br><br>

        <form action="exerciselist_save.php" method="POST">
            <input type="hidden" name="tl_id" <?php echo "value='$tl_id'"; ?> >
            <input type="hidden" name="toiminto" <?php echo "value='$toiminto'"; ?> >
            Tehtävälistan kuvaus:<br>
            <textarea name="kuvaus" rows="5" cols="40" ><?php echo htmlentities($kuvaus, ENT_QUOTES); ?></textarea><br>
            Lisättävissä olevat tehtävät:<br>

            <?php

                print_query_result($arr1, $arr2);

            ?>

            
            <input type="submit">

        </form>

        
    </div>

</div>
<?php
    require_once(TEMPLATES_PATH . "/footer.php");
?>