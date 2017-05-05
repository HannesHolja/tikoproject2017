<?php    
    require_once("../resources/config.php");
     
    require_once(TEMPLATES_PATH . "/header.php");
?>
<div id="container">
    <div id="content">
        
        <form action="tarkista.php" method="post">
        	Kysely: <textarea rows="5" cols="40"></textarea>
        	<input type="submit">
        </form>
        

    </div>

</div>
<?php
    require_once(TEMPLATES_PATH . "/footer.php");
?>