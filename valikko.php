<?php

	require_once('sql_handler.php');
	include('session.php');

	getNames();

	echo "Tervetuloa " .$_SESSION['username'];
	

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Valikko</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="css/test.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
  
</head>
<body style= "background: url('img/bg.jpg') no-repeat center center fixed;background-size: cover;">
<div class ="mb-2 bg-transparent text-white" >
<div class="jumbotron text-center" style="background-color:black; opacity:0.7;">
  <h1 class="mb-2 bg-transparent text-white">Timanttityö Lindh Oy</h1>
  
</div>
  
<div class="row">
  <div class="col-sm-4 text-center"><h3>Valitse toiminto:</h3>
  <form action="logout.php" method="post">

 		<div class="btn-group-vertical">
		<button type="button" class="btn btn-success" onclick="openKayttajat()">Muokkaa ja lisää henkilöitä</button>
		<button type="button" class="btn btn-success" onclick="openRaportit()">Raporttien haku ja tulostus</button>
		<button type="button" class="btn btn-success" onclick="openSeuranta()">Tuntiseuranta ja ajopäiväkirja</button>
		<button type="submit" name="logout" class="btn btn-danger">Kirjaudu ulos ja sulje</button>	
		</div>


	</form>
  </div>

  <div class="col-sm-4">
  <img src="img/hole.jpg" alt="hole" style="width:100%; border:solid 4px white; border-radius:250px">
  </div>

  <div class="col-sm-8"></div>

  
</div> 
</div>
</body>
<script type='text/javascript' src="js/menu.js"></script>
</html>
