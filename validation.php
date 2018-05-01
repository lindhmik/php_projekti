<?php 
//some validation here.. this whole structure is still under construction and lots of stuff done in other files already 
//(almost all variable setting work for example..)
    function set_userid(){
		if(isset($_POST['persons']) && $_POST['persons'] !=$_SESSION["userid"]){
			   return $_POST["persons"];
		}else{
	       return $_SESSION["userid"];
		}
    }
    
    function set_date($date){
        $today = date("Y-m-d");
        if($date ==''){
            return $today;
        }else {
            return $date;
        }  
    }
    
    function check_if_float($floatInput){
        $bln= filter_var(str_replace(",", ".", $floatInput), FILTER_VALIDATE_FLOAT);
        return $bln;
    }
    
   
    
    
?>