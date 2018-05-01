<?php
	session_start();
	require_once('sql_handler.php');

//if user is already logged while navigate to index.page then redirect..
	if (isset($_SESSION['userid'])) {
        
        if ($_SESSION['admin']!=1){
            	header("Location: seuranta.php");
        }else{
			header("Location: valikko.php"); /* Redirect browser */;
        }
	}


	
	//calling login function..
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		
		if (isset($_POST['check'])){

			$email = $_POST["email"];
			$pwd = $_POST["pwd"];

			//error message if login fails
			$message=login($email, $pwd);
		

		}
	
	 }


	//echo $message

?>



<!DOCTYPE html>
<html lang="fi">
<head>
  <title>Työaikaraportit</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
</head>
<body style= "background: url('img/bg.jpg') no-repeat center center fixed;background-size: cover;">

<div class="jumbotron text-center" style="background-color:black; opacity:0.7;">
  <h1 class="mb-2 bg-transparent text-white">Timanttityö Lindh Oy</h1>
  
</div>
  
<div class="mb-2 bg-transparent text-white">
  <div class="row">
    <div class="col-sm-4">
      
      
    </div>
    <div class="col-sm-4 text-primary" style="background-color:#f2f2f2; opacity: 1; border:solid 5px white; border-radius:5px;  height: 500px;">
      <h2><br>Kirjaudu sisään:</h2><br>
      <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
    <div class="form-group">
      <label for="email">Email:</label>
      <input type="email" class="form-control" id="email" placeholder="Email" name="email">
    </div>
    <div class="form-group">
      <label for="pwd">Password:</label>
      <input type="password" class="form-control" id="pwd" placeholder="Salasana" name="pwd">
    </div>
    <div class="checkbox">
      <label><input type="checkbox" name="remember"> Muista minut</label>
    </div><br>
    <button type="submit" name="check" class="btn btn-success">Kirjaudu sisään</button>
  </form>
    </div>
    <div class="col-sm-4">
      
    </div>
  </div>
</div>
<?php
	if (isset($message)){
		echo "<h3 class='mb-2 bg-transparent text-danger'>";
		echo "<br><br><p align='center'>".$message ."</p>";
		echo "</h3>";
	}
?>

</body>
</html>
