<?php    
    session_start();

    require_once("../resources/config.php");
     
    require_once(TEMPLATES_PATH . "/header.php");


    //If user has not logged in redirect them to index.php
    if(!isset($_SESSION['k_id'])){
        header('Location: index.php');
        exit;
    }

            //Connect to database
            if (!$con = pg_connect($config['dbconnection']))
            die("Tietokantayhteyden luominen epäonnistui.");

            /* FETCH DATA FROM DATABASE */
            $result = pg_query("SELECT sessio.s_id, tehtavalista.kuvaus, sessio.alkamishetki, sessio.lopetushetki, 
                COUNT(CASE WHEN yritys.yrityksen_loppu IS NOT NULL THEN 1 END) AS yritysten_maara, 
                COUNT(CASE WHEN yritys.oikein=1 THEN 1 END) AS oikein  
                FROM sessio, yritys, tehtavalista
                WHERE sessio.k_id=" . $_SESSION['k_id'] . " AND 
                sessio.alkamishetki != sessio.lopetushetki AND 
                yritys.s_id=sessio.s_id AND tehtavalista.tl_id=sessio.tl_id
                GROUP BY sessio.s_id, tehtavalista.kuvaus ORDER BY sessio.s_id ASC");

            if (!$result) {
              echo "Virhe kyselyssä.\n" . pg_last_error();
              exit;
            }

            $arr1 = pg_fetch_all($result);

    function print_query_result($result_array)
    {

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
        
        <h1>Tehtäväsessioni</h1>
        <br>

        <?php
        
        print_query_result($arr1);


        ?>

        <br>


     

    </div>

</div>
<?php
    require_once(TEMPLATES_PATH . "/footer.php");
?>