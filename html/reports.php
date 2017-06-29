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
    if(!in_array("reports", $_SESSION['oikeudet']))
    {
        header('Location: menu.php');
        exit;
    }

    /* ROOLIT */
    $OPISKELIJA = 0;
    $OPETTAJA = 1;
    $ADMIN = 2;

   
    if(!$con = pg_connect($config['dbconnection'])){
        die("Tietokantayhteyden luominen epäonnistui.");
    }

    if($_SESSION['rooli'] == "opettaja")
    {
        /* APU KYSELYJÄ */
        
        /* OPETTAJAN TEKEMÄT TEHTÄVÄLISTAT */
        $tehtavalistat = "SELECT * FROM tehtavalista WHERE tehtavalista.k_id=" . $_SESSION['k_id'];
        

        $opiskelijat = "    SELECT * FROM kayttaja WHERE r_id=$OPISKELIJA AND k_id IN(SELECT k_id FROM    ((SELECT sessio.s_id, yritys.s_id, tl_id, k_id FROM sessio, yritys 
                                                WHERE yritys.s_id = sessio.s_id) AS sessio_yritys
                                            NATURAL JOIN 
                                            (SELECT tl_id FROM tehtavalista 
                                                WHERE tehtavalista.k_id=" . $_SESSION['k_id'] . ") AS tlista) GROUP BY k_id)";

        $opiskelijat_opo = pg_query($opiskelijat);

        if(!$opiskelijat_opo)
        {
            echo "Virhe opeskilijoiden haussa: " .pg_last_error();
            exit;
        }
       
        /* R1 */
        $r1ope_result = pg_query("SELECT sessio.s_id, kayttaja.k_id, kayttaja.nimi,
                        COUNT(CASE WHEN yritys.oikein=1 THEN 1 END) as oikein
                        FROM sessio, yritys,
                        ($tehtavalistat) as tlista, kayttaja 
                        WHERE sessio.tl_id = tlista.tl_id AND kayttaja.r_id=$OPISKELIJA 
                        AND sessio.k_id=kayttaja.k_id AND yritys.s_id=sessio.s_id
                        GROUP BY sessio.s_id, kayttaja.k_id ORDER BY s_id ASC");

        if(!$r1ope_result)
        {
            echo "Virhe R1 raportissa: " . pg_last_error();
            exit;
        }

        /* R2 */

        $r2ope_result = pg_query("SELECT tl_id, MIN(aika) AS nopein, MAX(aika) AS hitain, AVG(aika) as keskim 
                                    FROM (SELECT tlista.tl_id, sessio.s_id, sessio.alkamishetki, sessio.lopetushetki, 
                                (EXTRACT(epoch from (sessio.lopetushetki - sessio.alkamishetki)) * interval  '1 sec') AS aika
                                FROM sessio, ($tehtavalistat) as tlista, kayttaja 
                                WHERE sessio.lopetushetki!=sessio.alkamishetki 
                                AND sessio.tl_id=tlista.tl_id AND kayttaja.r_id=$OPISKELIJA 
                                AND sessio.k_id=kayttaja.k_id) AS AJAT GROUP BY tl_id");

        if(!$r2ope_result)
        {
            echo "Virhe R2 raportissa: " . pg_last_error();
            exit;
        }

        /* R3 */
        $stm =
        "SELECT t_id, kuvaus, ROUND(COUNT(CASE WHEN oikein=1 THEN 1 END)::decimal / COUNT(y_id)::decimal, 2) AS onnistumis_prosentti, AVG((EXTRACT(epoch from (yrityksen_loppu - yrityksen_alku)) * interval  '1 sec')) as keskim_aika FROM 
                (SELECT t_id, kuvaus FROM tehtava WHERE k_id=" . $_SESSION['k_id'] . ") AS kuvaus
                NATURAL JOIN(SELECT y_id, s_id, t_id, yrityksen_alku, yrityksen_loppu, oikein FROM yritys WHERE yrityksen_loppu!=yrityksen_alku) AS yhteenvetotiedot GROUP BY t_id, kuvaus.kuvaus";

        $r3ope_result = pg_query("$stm");
        if(!$r3ope_result)
        {
            echo "Virhe R3 raportissa: " . pg_last_error();
            exit;
        }

        /* R5 */
        $r5ope_result = pg_query("SELECT DISTINCT t.kyselytyyppi,
                        COUNT(y0.y_id) AS yritysten_määrä,
                        AVG(((DATE_PART('day', y0.yrityksen_loppu::timestamp - y0.yrityksen_alku::timestamp) * 24 + DATE_PART('hour', y0.yrityksen_loppu::timestamp - y0.yrityksen_alku::timestamp)) * 60 +DATE_PART('minute', y0.yrityksen_loppu::timestamp - y0.yrityksen_alku::timestamp)) * 60 +DATE_PART('second', y0.yrityksen_loppu::timestamp - y0.yrityksen_alku::timestamp)) AS keskim_aika
                        FROM Tehtava t, Yritys y0
                        WHERE y0.t_id = t.t_id AND t.k_id=" . $_SESSION['k_id'] . "
                        GROUP BY t.kyselytyyppi");

        if(!$r5ope_result)
        {
            echo "Virhe R3 raportissa: " . pg_last_error();
            exit;
        }

    }
    
        
        /* HAE OPISKELIJAT */

        $opiskelijat_res = pg_query("SELECT * FROM kayttaja WHERE r_id=$OPISKELIJA");


        /* R1 */
        $r1_result = pg_query("SELECT s.s_id, k.k_id, k.nimi,
                    COUNT(CASE WHEN y.oikein=1 THEN 1 END) as oikein                    
                    FROM kayttaja k, sessio s, yritys y                   
                    WHERE k.k_id = s.k_id AND y.s_id = s.s_id AND k.r_id=$OPISKELIJA                   
                    GROUP BY k.k_id, s.s_id
                    ORDER BY s.s_id");

        if(!$r1_result)
        {
            echo "Virhe R1 raportissa: " .pg_last_error();
            exit;
        }
        /* R2 */

        $r2_result = pg_query("SELECT tl_id, MIN(aika) AS nopein, MAX(aika) AS hitain, AVG(aika) as keskim 
                                    FROM (SELECT tlista.tl_id, sessio.s_id, sessio.alkamishetki, sessio.lopetushetki, 
                                (EXTRACT(epoch from (sessio.lopetushetki - sessio.alkamishetki)) * interval  '1 sec') AS aika
                                FROM sessio, (SELECT * FROM tehtavalista) as tlista, kayttaja 
                                WHERE sessio.lopetushetki!=sessio.alkamishetki 
                                AND sessio.tl_id=tlista.tl_id AND kayttaja.r_id=$OPISKELIJA 
                                AND sessio.k_id=kayttaja.k_id) AS AJAT GROUP BY tl_id");
         if(!$r2_result)
        {
            echo "Virhe R2 raportissa: " . pg_last_error();
            exit;
        }

        /* R3 */

        $stm =
        "SELECT t_id, kuvaus, ROUND(COUNT(CASE WHEN oikein=1 THEN 1 END)::decimal / COUNT(y_id)::decimal, 2) AS onnistumis_prosentti, AVG((EXTRACT(epoch from (yrityksen_loppu - yrityksen_alku)) * interval  '1 sec')) as keskim_aika FROM 
                (SELECT t_id, kuvaus FROM tehtava) AS kuvaus
                NATURAL JOIN(SELECT y_id, s_id, t_id, yrityksen_alku, yrityksen_loppu, oikein FROM yritys WHERE yrityksen_loppu!=yrityksen_alku) AS yhteenvetotiedot GROUP BY t_id, kuvaus.kuvaus";

        $r3_result = pg_query("$stm");
        if(!$r3_result)
        {
            echo "Virhe R3 raportissa: " . pg_last_error();
            exit;
        }

        /* R4 */

        $r4_result = pg_query("SELECT t.t_id,
            Count(CASE WHEN y.oikein=1 THEN 1 END) AS onnistuneet_yritykset,
            ROUND(COUNT(CASE WHEN y.oikein=1 THEN 1 END)::decimal / COUNT(y.y_id)::decimal, 2) AS onnistumis_prosentti 
            FROM Yritys y, (
            SELECT DISTINCT te.t_id
            FROM Tehtava te
            ) t
            WHERE y.t_id = t.t_id
            GROUP BY t.t_id, y.oikein
            ORDER BY t.t_id, MAX(((DATE_PART('day', y.yrityksen_loppu::timestamp - y.yrityksen_alku::timestamp) * 24 + DATE_PART('hour', y.yrityksen_loppu::timestamp - y.yrityksen_alku::timestamp)) * 60 +DATE_PART('minute', y.yrityksen_loppu::timestamp - y.yrityksen_alku::timestamp)) * 60 +DATE_PART('second', y.yrityksen_loppu::timestamp - y.yrityksen_alku::timestamp)) DESC");

        if(!$r4_result)
        {
            echo "Virhe R4 raportissa: " . pg_last_error();
            exit;
        }

        /* R5 */
        $r5_result = pg_query("SELECT DISTINCT t.kyselytyyppi,
                        COUNT(y0.y_id) AS yritysten_määrä,
                        AVG(((DATE_PART('day', y0.yrityksen_loppu::timestamp - y0.yrityksen_alku::timestamp) * 24 + DATE_PART('hour', y0.yrityksen_loppu::timestamp - y0.yrityksen_alku::timestamp)) * 60 +DATE_PART('minute', y0.yrityksen_loppu::timestamp - y0.yrityksen_alku::timestamp)) * 60 +DATE_PART('second', y0.yrityksen_loppu::timestamp - y0.yrityksen_alku::timestamp)) AS keskim_aika
                        FROM Tehtava t, Yritys y0
                        WHERE y0.t_id = t.t_id
                        GROUP BY t.kyselytyyppi");

        if(!$r5_result)
        {
            echo "Virhe R5 raportissa: " . pg_last_error();
            exit;
        }

        $r6_result =pg_query("SELECT DISTINCT k.paa_aine,
Count(CASE WHEN y.oikein=1 THEN 1 END) AS oikeiden_lkm
FROM Kayttaja k, Yritys y, Sessio s
WHERE y.s_id = s.s_id
GROUP BY k.paa_aine");

        if(!$r6_result)
        {
            echo "Virhe R6 raportissa: " . pg_last_error();
            exit;
        }
    

    $opiskelijat_arr = pg_fetch_all($opiskelijat_res);
    $opiskelijatopo_arr = pg_fetch_all($opiskelijat_opo);


    $r1_arr = pg_fetch_all($r1_result);
    $r1_ope = pg_fetch_all($r1ope_result);
    $r2_arr = pg_fetch_all($r2_result);
    $r2_ope = pg_fetch_all($r2ope_result);
    $r3_arr = pg_fetch_all($r3_result);
    $r3_ope = pg_fetch_all($r3ope_result);
    $r4_arr = pg_fetch_all($r4_result);
    $r5_arr = pg_fetch_all($r5_result);
    $r5_ope = pg_fetch_all($r5ope_result);
    $r6_arr = pg_fetch_all($r6_result);


    pg_close($con);
 

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
        
        <h1>Raportit</h1>

        
        <br><br>

        <h2>Opiskelijat</h2>

        <?php
            print_query_result($opiskelijat_arr);
            if($_SESSION['rooli'] == "opettaja")
            {
                echo "Sinun tehtäviäsi tehneet:";
                print_query_result($opiskelijatopo_arr);

            }
        ?>

        <br>

        <h2>R1</h2>
        <?php



            print_query_result($r1_arr);


            if($_SESSION['rooli'] == "opettaja")
            {
                                echo "Sinun tehtäviesi tulokset:";

                print_query_result($r1_ope);
            }

        ?>
        <br>

        <h2>R2</h2>
        <?php
            print_query_result($r2_arr);

            if($_SESSION['rooli'] == "opettaja")
            {
                                echo "Sinun tehtäviesi tulokset:";

                print_query_result($r2_ope);
            }
        ?>
        <br>

        <h2>R3</h2>
        <?php
            print_query_result($r3_arr);

            if($_SESSION['rooli'] == "opettaja")
            {
                                echo "Sinun tehtäviesi tulokset:";
                print_query_result($r3_ope);
            }
        ?>
        <br>

        <h2>R4</h2>
        <?php
            print_query_result($r4_arr);

        ?>
        <br>

        <h2>R5</h2>
        <?php
            print_query_result($r5_arr);

            if($_SESSION['rooli'] == "opettaja")
            {
                                echo "Sinun tehtäviesi tulokset:";

                print_query_result($r5_ope);
            }
        ?>
        <br>

        <h2>R6</h2>
        <?php
            print_query_result($r6_arr);

        ?>
        <br>

        
    </div>

</div>
<?php
    require_once(TEMPLATES_PATH . "/footer.php");
?>