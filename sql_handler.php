<?php 

require_once('connection.php');

//Login.. if login is ok, redirect user to another page.. Admin to valikko and basic user to seuranta.. Error msg if login fails
function login($email, $pwd){
    
    $sql = ("SELECT idhenkilo, sukunimi , etunimi, email, salasana, admin FROM henkilo WHERE email ='$email'");
    $result = execute_query($sql);
    if ($result->num_rows > 0) {
        
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $current_userid=$row["idhenkilo"];
            $current_user = $row["sukunimi"]. ", " . $row["etunimi"];
            $sql_username=$row["email"];
            $sql_pwd=$row["salasana"];
            $_SESSION['admin']=$row["admin"];
        }
        if($sql_username==$email && password_verify($pwd, $sql_pwd) == true){
            //"Kirjautuminen onnistui"
            $_SESSION['userid'] = $current_userid;
            $_SESSION['username'] = $current_user;
            $_SESSION['addedRows'] = "";
            
            if($_SESSION['admin']!=1){
                header("Location: seuranta.php"); /* Redirect browser */
                exit;
            }else{
                header("Location: valikko.php"); /* Redirect browser */
                exit;
            }
        }else if($sql_username==$email && $pwd == $sql_pwd){
            $pass= password_hash($sql_pwd, PASSWORD_BCRYPT);
            $sql = "UPDATE henkilo SET salasana = '$pass' WHERE idhenkilo ='$current_userid';";
            $result = execute_query($sql);
            return "Salasana suojattu. Yritä uudelleen";
        }else{
            //header("Location: index.html");
            return "Tarkista käyttäjänimi ja salasana";
        }
    } else {
        return auth_failed();
    }
    
}

//remove user from henkilo -table
function remove_person($id){
    $mysqli = get_database();
    if ($stmt = $mysqli ->prepare("DELETE FROM henkilo WHERE idhenkilo=?")){
        $stmt->bind_param("i", $id );
        $stmt->execute();
        $stmt->close();
        return "Rivi on poistettu";
    }else{
        return $mysqli->errno . ' ' . $mysqli->error;
        $stmt->close();
    }
      
}

//remove working hours and driving diary -row from tuntiseuranta table
function remove_hoursRow($eventId){
    $mysqli = get_database();
    if ($stmt = $mysqli ->prepare("DELETE FROM tuntiseuranta WHERE idtuntiseuranta=?")){
        $stmt->bind_param("i", $eventId );
        $stmt->execute();
        $stmt->close();
        $_SESSION['addedRows']="";
        return "Rivi on poistettu";
    }else{
        return $mysqli->errno . ' ' . $mysqli->error;
        $stmt->close();
    }
}

//update row to tuntiseuranta
function update_hoursrow($eventId, $date, $hours, $overtime, $place, $kilometers, $km_description, $userid){
    $mysqli = get_database();
    if($stmt = $mysqli->prepare("UPDATE tuntiseuranta SET pvm=?, tyokohde=?, tunnit=?, ylityo=?, km=?, kmselite=? WHERE idtuntiseuranta=?")){
        $stmt->bind_param("ssssssi", $date, $place, $hours, $overtime, $kilometers, $km_description, $eventId);
        $stmt->execute();
        $stmt->close();
        $_SESSION['addedRows'] = "<tr id='$eventId'><td>" .$eventId . "</td><td>" .$userid . "</td><td>". $date . "</td><td>" .$place ."</td><td>" .$hours . "</td><td>
        " .$overtime ."</td><td>" .$kilometers ."</td><td>" . $km_description ."</td>
        <td><button type=\"button\" class=\"btn btn-success\"onClick=\"modifyRow('$eventId')\">Muokkaa</button></td>
        <td><button type=\"button\" class=\"btn btn-danger\" onclick=\"removeRow('$eventId')\">Poista</button></td></tr>";
        return "Rivi on päivitetty";
        
    }else{
        return $mysqli->errno . ' ' . $mysqli->error;
        $stmt->close();
    }
}

//update user..
function update_personinfo($fname, $lname,$bdate,$salary,$address,$zipcode,$city,$phone,$veroNro,$pass,$email, $admin,$personid){
    $mysqli = get_database();
    if($stmt = $mysqli->prepare("UPDATE henkilo SET etunimi=?, sukunimi=?, syntaika=?, tuntipalkka=?, lahiosoite=?, postinro=?, kaupunki=?, puhnro=?, veronro=?, salasana=?, email=?, admin=? WHERE idhenkilo=?")){
        $stmt->bind_param("sssdsssssssii", $fname, $lname,$bdate,$salary,$address,$zipcode,$city,$phone,$veroNro,$pass,$email, $admin,$personid);
        $stmt->execute();
        $stmt->close();
        return "Rivi on päivitetty";
        
    }else{
        return $mysqli->errno . ' ' . $mysqli->error;
        $stmt->close();
    }
}
//add user
function insert_person($lname,$fname,$bdate,$salary,$veroNro,$address,$zipcode,$city,$phone,$email,$pass, $admin) {
    $mysqli = get_database();
    if($stmt = $mysqli->prepare("INSERT INTO henkilo (sukunimi, etunimi, syntaika, tuntipalkka, veronro, lahiosoite, postinro, kaupunki, puhnro, email, salasana, admin)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?)")){
        $stmt->bind_param("sssdsssssssi",$lname, $fname, $bdate,$salary,$veroNro, $address, $zipcode, $city, $phone, $email, $pass, $admin);
        //$result = execute_prepared_query($stmt);
        $stmt->execute();
        $stmt->close();
        return "Käyttäjä tallennettu tietokantaan";
    
    }else{
        return $mysqli->errno . ' ' . $mysqli->error;
        $stmt->close();
    }
   
    
}
//add working hours and kilometers to tuntiseuranta table.. Creates html row with modify and remove buttons.
function insert_hours($date, $hours, $overtime, $place, $kilometers, $km_description, $userid){
    $mysqli = get_database();
    
    if($stmt = $mysqli->prepare("INSERT INTO tuntiseuranta (pvm, tyokohde, tunnit, ylityo, km, kmselite, henkilo_idhenkilo)
	VALUES (?,?,?,?,?,?,?);")){
        $stmt->bind_param("ssssssi",$date, $place, $hours, $overtime, $kilometers, $km_description, $userid);
        $stmt->execute();
        $id= $mysqli->insert_id;
        $stmt->close();
        $_SESSION['addedRows'] = "<tr id='$id'><td>" .$userid . "</td><td>" .$id . "</td><td>". $date . "</td><td>" .$place ."</td><td>" .$hours . "</td><td>
        " .$overtime ."</td><td>" .$kilometers ."</td><td>" . $km_description ."</td>
        <td><button type=\"button\" class=\"btn btn-success\" style=\"width:90px;\" onClick=\"modifyRow('$id')\">Muokkaa</button></td>
        <td> <button type=\"button\" name=\"remove\" class=\"btn btn-danger\" style=\"width:90px;\" onClick=\"removeRow('$id')\">Poista</button></form></td></tr>";
        return "Rivi tallennettiin tietokantaan onnistuneesti: ";
    }else{
        return $mysqli->errno . ' ' . $mysqli->error;
        $stmt->close();
    }
    
}

//returns information from certain time period.. Returns html -table and writes text file.. Stupid solution. Files and tables
//will be create in target file later on and this only return result, but there wasnt time to fix this now..
function get_personal_and_working_info($arguments, $names, $start_date, $end_date, $removable){

    $table_header="<table class='table table-sm table-dark'><thead>
    <tr><th class='thead-dark' scope='col'>henkiloId";
    $file_header="**************************************************************************************************************\r\n" .
        "Raportti ajalta: " .$start_date ." - " . $end_date . "\t\t\t TTL Oy \r\nHenkiloID\t";
    for ($i=0; $i < count($arguments); $i++){
        
       
        if($removable==1){
            if($arguments[$i] != 'sukunimi' && $arguments[$i] != 'etunimi'){
                $table_header .= "<th scope='col'>".str_replace("'", "", $arguments[$i]) ."</th>";
            }
        }else{
            $table_header .= "<th scope='col'>".str_replace("'", "", $arguments[$i]) ."</th>";
        }
        $file_header .= str_replace("'", "", $arguments[$i]) ."\t";
    }
    $table_header .= "</tr></thead>";
    $file_header .= "\r\n**************************************************************************************************************\r\n";
    $sql = ("SELECT henkilo_idhenkilo,tuntipalkka,".str_replace("'","",implode(",", $arguments))." FROM henkilo join tuntiseuranta ON
        henkilo.idhenkilo = tuntiseuranta.henkilo_idhenkilo WHERE henkilo.idhenkilo IN (".implode(',',$names).")
        AND pvm BETWEEN '" .$start_date."' AND '".$end_date. "' ORDER BY henkilo.idhenkilo,pvm ");
    $result = execute_query($sql);
    
    $table_row="";
    $file_row="";
    $lastname="";
    $firstname="";
    $salary=0;
    $sumHours=0;
    $sumOvertime=0;
    $sumKm=0;
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            if (isset($row['sukunimi'])){
                $lastname = $row['sukunimi'];
            }
            if (isset($row['etunimi'])){
                $firstname = $row['etunimi'];
            }
            if (isset($row['tuntipalkka'])){
                $salary = $row['tuntipalkka'];
            }
            if (isset($row['tunnit'])){
                $sumHours += $row['tunnit'];
            }
            if (isset($row['ylityo'])){
                $sumOvertime += $row['ylityo'];
            }
            if (isset($row['km'])){
                $sumKm += $row['km'];
            }
            
            

            if($removable==1){
                $id=$row['idtuntiseuranta'];
                $table_row .= "<tr id='$id'>";
            }else{
                $table_row .= "<tr>";
            }
            foreach ($row as $item) {
                if($removable==1){
                    if($item != $row['sukunimi'] && $item != $row['etunimi']){
                        $table_row .= "<td>". $item."</td>";
                    }
                }else {
                    if($item != $row['tuntipalkka']){
                        $table_row .= "<td>". $item."</td>";
                        $file_row .= $item . "\t";
                    }
                }
            }
            if($removable==1){
                $table_row .= "<td><button type=\"button\" class=\"btn btn-success\" style=\"width:90px;\" onClick=\"modifyRow('$id')\">Muokkaa</button></td>
                                <td> <button type=\"button\" name=\"remove\" class=\"btn btn-danger\" style=\"width:90px;\" onClick=\"removeRow('$id')\">Poista</button></form></td></tr>";
            }else{
                $table_row .= "</tr>";
            }
            $file_row .= "\r\n";
        }
       if($removable==1){
            $table_row .="<tr><td>Yhteenveto:<br>" . str_replace("'","", $lastname).", ".str_replace("'","", $firstname) . "</td>
            <td></td><td></td><td>Aikajakso: <br>". str_replace("'","", $start_date) ."-<br>".str_replace("'","", $end_date)."</td><td></td>
            <td></td><td></td><td>Perustunnit yht: ".str_replace("'","", $sumHours)."h, Ylityö: ".str_replace("'","", $sumOvertime)."h, Kilometrit: ".str_replace("'","", $sumKm)."km </td></tr>"; 
       }else {
         
           $table_row .= "</td></tr></table><table class='table table-sm table-dark'><thead>
            <tr><th class='thead-dark' scope='col'>Yhteenveto ajalta: </th><th class='thead-dark' scope='col'></th></th>
            <th class='thead-dark' scope='col'>Henkilö: </th><th class='thead-dark' scope='col'>Tunnit: </th> <th class='thead-dark' scope='col'>Ylityö: </th>
            <th class='thead-dark' scope='col'>Kilometrit: </th><th class='thead-dark' scope='col'>Palkkakertymä: </th><th class='thead-dark' scope='col'>Kilometrikorvaus: </th></tr>
            <td>". str_replace("'","", $start_date) ." - ".str_replace("'","", $end_date)."</td><td></td>
            <td>". str_replace("'","", $lastname).", ".str_replace("'","", $firstname) . "</td><td>".str_replace("'","", $sumHours)."h</td><td>".str_replace("'","", $sumOvertime)."h</td>
            <td>".str_replace("'","", $sumKm)."km</td><td>". (($sumHours * $salary) + ($sumOvertime *20)) ." €</td><td>".$sumKm*0.42 ." €</td></tr>";
           
           
       }
      $html_table=$table_header. $table_row . "</table>";
      
      $file = $file_header.$file_row;
      //$myfile = fopen("myfile.txt", "w") or die("Unable to open file!");
      //fwrite($myfile, $file);
      //fclose($myfile);
      //header('Content-type: text/plain');
      //header('Content-Disposition: attachment; filename="myfile.txt"');
      //echo $file;
      
      
      //exec("notepad.exe". "C:\Users\User1\myfile.txt");
      
      return $html_table;
      
    } else {
        return "Antamillasi hakuehdoilla ei löytynyt tuloksia";
        
    }
}

//returns user information added to hmtl table..
function get_personal_info($arguments, $names){
    $table_header="<table class='table table-hover table-dark'><thead><tr><th scope='col'>henkiloId";
    
    for ($i=0; $i < count($arguments); $i++){
        $table_header .= "<th scope='col'>".str_replace("'", "", $arguments[$i]) ."</th>";
    }
    $table_header .= "</tr></thead>";
    
    $sql = ("SELECT idhenkilo,".str_replace("'","",implode(",", $arguments))." FROM henkilo WHERE idhenkilo IN (".implode(',',$names).") 
    ORDER BY idhenkilo");
    $result = execute_query($sql);
    $table_row="";
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $table_row .= "<tr>";
            foreach ($row as $item) {
                $table_row .= "<td>". $item."</td>";
            }
            $table_row .= "</tr>";
        }
        $html_table=$table_header. $table_row. "</table>";
        return $html_table;
    } else {
        return "Antamillasi hakuehdoilla ei löytynyt tuloksia";
        //return $sql;
    }
    
}
//returns user info to modified or removed
function getPerson($id){
    $sql = "SELECT * FROM henkilo WHERE idhenkilo = '$id';";
    $result = execute_query($sql);
    $html_row="";
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $html_row=$row;
        }
        return $html_row;
    }
    else {
        return "SQL failure";
    }
}

//populates drop downs for admin user
function getNames(){
    $_SESSION['populate_drop_down']="";
    $sql = "SELECT idhenkilo, sukunimi, etunimi FROM henkilo;";
    $result = execute_query($sql);
    
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $current_userid=$row["idhenkilo"];
            $current_user = $row["sukunimi"]. ", " . $row["etunimi"]; 
            $_SESSION['populate_drop_down'] .= '<option value="'.$current_userid.'">'.$current_user.'</option>';
        }
        return $current_user;
    } 
    else {
        return "SQL failure";
    }
}
function auth_failed(){
    return "Antamillasi hakuehdoilla ei löytynyt tuloksia";
}
function execute_query($sql_query) {
    $mysqli = get_database();
    $sql = $sql_query;
    $result = $mysqli->query($sql);
    
    $mysqli->close();
    return $result;
}



?>