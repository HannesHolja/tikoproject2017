<?php    
    require_once("../resources/config.php");
     
    require_once(TEMPLATES_PATH . "/header.php");
?>
<div id="container">
    <div id="content">
        
        <h1>Tunnistatuminen</h1>

        <form action="login.php" method="post">
        	OpiskelijaNro: <input type="text" name="opNro"><br>
        	<input type="submit">

        </form>
        

    </div>

</div>
<?php
    require_once(TEMPLATES_PATH . "/footer.php");
?>