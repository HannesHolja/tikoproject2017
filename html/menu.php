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

            //Query to fetch tehtavalistat
            $result = pg_query("SELECT * FROM tehtavalista");

            if (!$result) {
              echo "Virhe kyselyssä.\n";
              exit;
            }



?>
<div class="container container-table">
    <div id="content">
        
        <h1>Menu</h1>
        <br>
        <?php echo "Tervetuloa ". $_SESSION['nimi'];?>

        <br>
        <h2>Tehtävälistat</h2><br>

        <?php
        echo "<table class='table table-hover'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Kuvaus</th>";
        echo "<th>Tehtävien lukumäärä</th>";
        echo "<th>pvm</th>";
        echo "<th>Aloita</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
            while($row = pg_fetch_array($result)){
                echo "<tr>";
                echo "<td>". $row['kuvaus'] . "</td>";
                echo "<td>". $row['tehtavien_lkm'] . "</td>";
                echo "<td>" . $row['pvm'] . "</td>";
                echo "<td><a href='start_exerciselist.php?listId={$row[tl_id]}'>Aloita tehtäväsarja</a></td>";
                echo "</tr>";
            }
        echo "</table>";
        echo "</tbody>";

        ?>

        <br>


     

    </div>

</div>
<?php
    require_once(TEMPLATES_PATH . "/footer.php");
?>