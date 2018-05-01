<?php

	require_once('sql_handler.php');
	require_once('validation.php');
	include('session.php');
	
	$nameErr = $bdErr = $nroErr = $emailErr = $pwErr= $phoneErr = $zipErr = $salErr ="";
	$state="";
	//populate drop downs
	getNames();

	//user wants to modify.. get user data from sql and populate form fields:
	//set variables

	if ($_SERVER['REQUEST_METHOD'] == 'POST'){	
	    if(isset($_POST['modifyperson'])){
	        $result=getPerson($_POST["persons"]);
	        $id = $result['idhenkilo'];
	        $lastName = $result['sukunimi'];
	        $firstName = $result['etunimi'];
	        $birthdate = $result['syntaika'];
	        $mdf_address = $result['lahiosoite'];
	        $zcode = $result['postinro'];
	        $mdf_city = $result['kaupunki'];
	        $phoneNro = $result['puhnro'];
	        $taxNro = $result['veronro'];
	        $user = $result['email'];
	        $bdate = $result['syntaika'];
	        $salary = $result['tuntipalkka'];
	        $password = $result['salasana'];
	    }
	    
	    //checks if user is adding, modifying or removing information:
	    //remove.. removes and populates dropdown so removed user is not visible there anymore
	    if (isset($_POST['remove'])){
	        remove_person($_POST["personid"]);
	        getNames();
	    }
	    
	    //user wants to insert or sending update.. set varibles and validate
	    if (isset($_POST['check'])||isset($_POST['modify'])){
	        if (isset($_POST['modify'])){
	            $state="update";
	        }else{
	            $state="insert";
	        }
		    if(empty($_POST["sukunimi"])||empty($_POST["etunimi"])){
		        $nameErr="Nimi on pakollinen tieto";
		    }
		    else{
		        $lname = filter_var($_POST['sukunimi'], FILTER_SANITIZE_STRING);
		        $fname = filter_var($_POST['etunimi'], FILTER_SANITIZE_STRING);
		        }
		        
		        if(empty($_POST["saika"])){
		            $bdErr = "Syntymäaika on pakollinen tieto";
		        }else{
		            $bdate = $_POST['saika'];
		        }
		        if(empty($_POST["palkka"])){
		            $salErr = "Tarkista syöte";
		        }elseif(check_if_float($_POST["palkka"]) == false){
		            $salErr = "Tarkista syöte";
		            
		        }else{
		            $salary = str_replace(",", ".",$_POST["palkka"]);
		        }

		        if(empty($_POST['veronro'])){
		            $nroErr = "Veronumero puuttuu!";
		        }else {
		            $veroNro=$_POST['veronro'];
		        }

		        if(empty($_POST['lahiosoite'])){
		            $address="Ei lahiosoitetietoja";
		        }else{
		            $address=$_POST['lahiosoite'];
		        }
		        
		        if(!preg_match('#[0-9]{5}#',$_POST['postinro'])){
		            $zipErr = "Virheellinen postinro";
		        }elseif(empty($_POST['postinro'])){
		            $zipcode = "Ei lahiosoitetietoja";
		        }else{
		            $zipcode = $_POST['postinro'];
		        }
		        
		        if(empty($_POST['kaupunki'])){
		            $city="Ei lahiosoitetietoja";
		        }else{
		            $city=$_POST['kaupunki'];
		        }

		        if(empty($_POST["puhnro"])){
		            $phoneErr="Puhelinnumero puuttuu!";
		        }elseif(!is_numeric($_POST['puhnro'])){
		            $phoneErr="Virheellinen puhelinnumero!";
		        }else{
		            $phone=filter_var($_POST['puhnro'], FILTER_SANITIZE_NUMBER_INT);
		        }
		        if(empty($_POST['email'])){
		            $emailErr = "Sähköpostisoite on pakollinen!";
		        }elseif(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)===false){
		            $emailErr="Sähköposti lahiosoite on virheellinen!";
		        }else{
		            $email=filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
		            }
		        
		        if(empty($_POST['salasana'])){
		            $pwErr="Salasana puuttuu!";
		        }else{
		            $pass= password_hash($_POST['salasana'], PASSWORD_BCRYPT);
		        }
		        $admin=$_POST['admin'];
		       
		        if ($state=="update"){
		            $personid=$_POST["personid"];
		            $message= update_personinfo($fname, $lname,$bdate,$salary,$address,$zipcode,$city,$phone,$veroNro,$pass,$email, $admin,$personid);
		            echo $message;
		        }
		    }
	}



?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Henkilon lisäys</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
  
</head>
<body onload="changeView()" style= "background: url('img/bg.jpg') no-repeat center center fixed;background-size: cover;" >

<div class="jumbotron text-center" style="background-color:transparent; background-color:black; opacity:0.7;">
  <h1 class="mb-2 bg-transparent text-white">Timanttityö Lindh Oy</h1>
  
</div>
<div class="row">

	<div class="col-sm-3 text-center">
    <form action="logout.php" method="post">
    <div class="btn-group-vertical">
		<button type="button" class="btn btn-success" onclick="openKayttajat()">Muokkaa ja lisää henkilöitä</button>
		<button type="button" class="btn btn-success" onclick="openRaportit()">Raporttien haku ja tulostus</button>
		<button type="button" class="btn btn-success" onclick="openSeuranta()">Tuntiseuranta ja ajopäiväkirja</button>
		<button type="submit" name="logout" class="btn btn-danger">Kirjaudu ulos ja sulje</button>
		
	</div> </form></div>
	
    <div class="col-md-6 text-primary" style="background-color:#f2f2f2">
    <h2> Valitse henkilö jonka tietoja haluat muokata:</h2>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post" onSubmit="return changeView()">
    <div class="form-row">
 	
 	<div class="form-group col-md-4">
		<?php
		echo "<label for='persons' id='personslabel'>Valitse työntekijä:</label>";
		echo "<select class='form-control' name='persons' id='persons'>";
		echo "<option></option>";
		echo $_SESSION['populate_drop_down'];
		echo	"</select>";
		?>
	</div>
	<div class="form-group col-md-3">
	<button name="modifyperson" class="btn btn-primary btn-block" style="height:40px; margin-top:30px;">Muokkaa henkilöä</button></div>
	
	</div>
	</form>
    <h2> Lisää uusi työntekijä: </h2>

<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
<div class="form-row">
    <div class="form-group col-md-2">
     <input readonly type="text" class="form-control" id="personid" name="personid" style="display:none;" value="<?php echo (isset($id)) ? $id: ''?>">
     </div>
     <div class="form-group col-md-10"></div>
     <div class="form-group col-md-12"></div>
 </div>
<div class="form-row">
    <div class="form-group col-md-3">
      <label for="sukunimi">Sukunimi</label>
      <input type="text" class="form-control" id="sukunimi" name="sukunimi" placeholder="Sukunimi" value="<?php echo (isset($lastName)) ? $lastName: ''?>">
      <span class="error"> <?php echo $nameErr;?></span>
    </div>
    <div class="form-group col-md-2">
      <label for="etunimi">Etunimi</label>
      <input type="text" class="form-control" id="etunimi" name="etunimi" placeholder="Etunimi" value="<?php echo (isset($firstName)) ? $firstName: ''?>">
    </div>
    
    <div class="form-group col-md-3">
      <label for="saika">Syntymäaika</label>
      <input type="date" class="form-control" id="date" name="saika" placeholder="MM/DD/YYYY" value="<?php echo (isset($birthdate)) ? $birthdate: ''?>">
      <span class="error"> <?php echo $bdErr;?></span>
    </div>
    <div class="form-group col-md-2">
      <label for="saika">Tuntipalkka</label>
      <input type="text" class="form-control" id="palkka" name="palkka" value="<?php echo (isset($salary)) ? $salary: ''?>">
      <span class="error"> <?php echo $salErr;?></span>
    </div>
    
    <div class="form-group col-md-2">
      <label for="inputTax">Veronumero</label>
      <input type="text" class="form-control" id="veronro" name="veronro" placeholder="VeroNro" value="<?php echo (isset($taxNro)) ? $taxNro: ''?>">
      <span class="error"> <?php echo $nroErr;?></span>
    </div>
 </div>
 <div class="form-row">
  <div class="form-group col-md-5">
    <label for="inputAddress">Katuosoite</label>
    <input type="text" class="form-control" id="lahiosoite" name="lahiosoite" placeholder="Tiekatu 123" value="<?php echo (isset($mdf_address)) ? $mdf_address: ''?>">
  </div>
  <div class="form-group col-md-2">
      <label for="postiNro">postinro</label>
      <input type="text" class="form-control" id="postiNro" name="postinro" value="<?php echo (isset($zcode)) ? $zcode: ''?>">
      <span class="error"> <?php echo $zipErr;?></span>
  </div>
  <div class="form-group col-md-2">
      <label for="kaupunki">Kaupunki</label>
      <input type="text" class="form-control" id="kaupunki" name="kaupunki" value="<?php echo (isset($mdf_city)) ? $mdf_city: ''?>">
    </div>
    
  
    <div class="form-group col-md-3">
      <label for="puhNro">Puhelinnumero</label>
      <input type="text" class="form-control" id="puhNro" name="puhnro" value="<?php echo (isset($phoneNro)) ? $phoneNro: ''?>">
      <span class="error"> <?php echo $phoneErr;?></span>
     </div> 
   </div>
    
   <div class="form-row">
    <div class="form-group col-md-4">
      <label for="email">Email tai käyttäjätunnus</label>
      <input type="text" id="email" name="email" class="form-control" value="<?php echo (isset($user)) ? $user: ''?>">
      <span class="error"> <?php echo $emailErr;?></span>
    </div>
    <div class="form-group col-md-2">
      <label for="password">Salasana</label>
      <input type="password" id="password" name="salasana" class="form-control" value="<?php echo (isset($password)) ? $password: ''?>">
      <span class="error"> <?php echo $pwErr;?></span>
    </div>
     <div class="form-group col-md-2">
    <label for="admin">Admin</label><br>
    	<label class="radio-inline">
      		<input type="radio" id="no" name="admin" value="0" checked>Ei
    	</label>
    	<label class="radio-inline">
      		<input type="radio" id="yes" name="admin" value="1" >Kyllä
    	</label>
     </div>
     <div class="form-group col-md-4">
 <br>
 <button type="submit" name="check" class="btn btn-success btn-block" style="height:40px">Lisää henkilö</button>
 <button type="submit" name="modify" class="btn btn-success btn-block" style="height:40px;display:none;">Tallenna</button>
 <button type="submit" name="remove" class="btn btn-danger btn-block" style="height:40px; display:none;">Poista henkilö</button>
 </div>
  </div>
 
</form>
</div>
 <div class="col-md-3"></div>
  <div class="col-md-2"></div>
   <div class="col-md-8">
<?php
if ($state=="insert" && isset($pass) && isset($lname) && isset($fname) && isset($bdate) && isset($salary) && isset($veroNro) && isset($address) && isset($zipcode) && isset($city) && isset($phone) && isset($email) && isset($admin))
    echo "<br><br><p align='center'>".$message=insert_person($lname,$fname,$bdate,$salary,$veroNro,$address,$zipcode,$city,$phone,$email,$pass, $admin) ."</p>";
?>
</div>
    <div class="col-md-2"></div>
</div>
 <script type='text/javascript' src="js/menu.js"></script> 
</body>

</html>
