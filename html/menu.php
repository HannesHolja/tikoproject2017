<?php    
    require_once("../resources/config.php");
     
    require_once(TEMPLATES_PATH . "/header.php");

    session_start();

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
<div id="container">
    <div id="content">
        
        <h1>Menu</h1>
        <br><br>
        <?php echo "Tervetuloa ". $_SESSION['nimi'];?>

        <br>
        <h2>Tehtävälistat</h2><br>

        <?php
        echo "<table>";
        echo "<tr>";
        echo "<th>Kuvaus</th>";
        echo "<th>Tehtävien lukumäärä</th>";
        echo "<th>pvm</th>";
        echo "<th>Aloita</th>";
        echo "</tr>";
            while($row = pg_fetch_array($result)){
                echo "<tr>";
                echo "<td>". $row['kuvaus'] . "</td>";
                echo "<td>". $row['tehtavien_lkm'] . "</td>";
                echo "<td>" . $row['pvm'] . "</td>";
                echo "<td><a href='start_exerciselist.php?listId={$row[tl_id]}'>Aloita tehtäväsarja</a></td>";
                echo "</tr>";
            }
        echo "</table>";

        ?>

        <br>


        <a href="logout.php">Kirjaudu ulos</a>
        

    </div>

</div>
<?php
    require_once(TEMPLATES_PATH . "/footer.php");
?>