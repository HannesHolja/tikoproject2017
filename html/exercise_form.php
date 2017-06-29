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

    $t_id = $_POST['id'];



    $kuvaus = $_POST['kuvaus'];
    $kyselytyyppi = $_POST['kyselytyyppi'];
    $tehtavanluoja = $_POST['tehtavanluoja'];
    $luontipvm = $_POST['luontipvm'];
    $esimvastaus = $_POST['esimvastaus'];
    $k_id = $_POST['k_id'];
    $toiminto = $_POST['toiminto'];

    
?>
<div class="container container-table">
    <div id="content">
        
        <h1>Tehtävän muokkaaminen/luominen</h1>

        <h2>Tietokannan rakenne:</h2><br>
                    <img src="img/tehtavakanta.jpeg">
<br><br>

        <form action="exercise_save.php" method="POST">
            <input type="hidden" name="t_id" <?php echo "value='$t_id'"; ?> >
            <input type="hidden" name="toiminto" <?php echo "value='$toiminto'"; ?> >
            Kyselyn kuvaus:<br>
            <textarea name="kuvaus" rows="5" cols="40" ><?php echo htmlentities($kuvaus, ENT_QUOTES); ?></textarea><br>
            Kyselyn tyyppi:
            <input type="radio" name="kyselytyyppi" value="select" <?php if($kyselytyyppi=="select") echo "checked='checked'"; ?> > select
            <input type="radio" name="kyselytyyppi" value="insert" <?php if($kyselytyyppi=="insert") echo "checked='checked'"; ?> > insert
            <input type="radio" name="kyselytyyppi" value="delete" <?php if($kyselytyyppi=="delete") echo "checked='checked'"; ?> > delete
            <input type="radio" name="kyselytyyppi" value="update" <?php if($kyselytyyppi=="update") echo "checked='checked'"; ?> > update
            <br>
            Esimerkki vastaus:<br>
            <textarea name="esimvastaus" rows="5" cols="40" ><?php echo htmlentities($esimvastaus, ENT_QUOTES); ?></textarea><br>
            <input type="submit">

        </form>

        
    </div>

</div>
<?php
    require_once(TEMPLATES_PATH . "/footer.php");
?>