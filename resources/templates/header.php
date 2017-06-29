<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
 
<html lang="fi">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TIKO 2017 Project Work</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
 
<body>
<nav class="navbar navbar-default">
<div class="container-fluid">
    <div class="navbar-header">
    	<a class="navbar-brand">TIKO 2017 Project Work</a>

    </div>

    <div id="navbar" class="navbar-collapse collapse">
    	<ul class="nav navbar-nav">

<?php 
	if (isset($_SESSION['k_id']))
	{
		if($_SESSION['rooli'] == "opiskelija")
		{
			echo "<li><a href='menu.php'>Menu</a></li>";
			echo "<li><a href='my_records.php'>Tulokseni</a></li>";
		}
		elseif($_SESSION['rooli'] == "opettaja")
		{
			echo "<li><a href='menu.php'>Menu</a></li>";
			echo "<li><a href='reports.php'>Raportit</a></li>";
			echo "<li><a href='exercise_list_cp.php'>Tehtävät ja tehtävälistat</a></li>";
		}
		elseif($_SESSION['rooli'] == "admin")
		{
			echo "<li><a href='menu.php'>Menu</a></li>";
			echo "<li><a href='reports.php'>Raportit</a></li>";
			echo "<li><a href='exercise_list_cp.php'>Tehtävät ja tehtävälistat</a></li>";
		}
		echo "<li><a href='logout.php'>Kirjaudu ulos</a></li>";
	}	
 ?>
 		</ul>

 	</div>

</nav>

<div class="page-header">
</div>