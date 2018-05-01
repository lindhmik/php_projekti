function modifyRow(id){
	
	var row = document.getElementById(id);
	
	
	var personId = row.childNodes[0].innerText;
	var rowId= row.childNodes[1].innerText;
	var date= row.childNodes[2].innerText;
	var project= row.childNodes[3].innerText;
	var hours= row.childNodes[4].innerText;
	var overtime = row.childNodes[5].innerText;
	var km= row.childNodes[6].innerText;
	var kmDes= row.childNodes[7].innerText;
	
	document.getElementById('pvm').defaultValue=date;
	document.getElementById('tunnit').defaultValue=hours;
	document.getElementById('kohde').defaultValue=project;
	document.getElementById('ylityo').defaultValue=overtime;
	document.getElementById('km').defaultValue=km;
	document.getElementById('selite').defaultValue=kmDes;
	document.getElementById('persons').style.display="none";
	document.getElementById('personslabel').style.display="none";
	document.getElementById('tapid').style.display="block";
	document.getElementById('tapidlabel').style.display="block";
	document.getElementById('tapid').value=rowId;
	document.getElementsByTagName('h2')[1].innerText="Muokkaa tapahtumaa:";
	document.getElementsByName('check')[0].innerText= "Tallenna muutokset";
	
};

function removeRow(id){
	var rowId = document.getElementById(id).childNodes[1].innerText;
	if(rowId !=""){
		var formElem=document.getElementsByTagName('form')[1];
		formElem= formElem.querySelectorAll('h4,small,label,input,button,span, select');
		for(var i=0; i<formElem.length; ++i){
	
			if(formElem[i].style.display !="none"){
				formElem[i].style.display = "none";
			}else{
				formElem[i].style.display ="block"
			}
		}
		
		document.getElementById('tapid').value=rowId;
		document.getElementsByTagName('h2')[1].innerText="Vahvista rivin poisto:";
		return true;
	} else{
		return false;
	}
}

function changeView(){
	console.log("Moi");
	var inputElems=document.querySelectorAll('input');
	
	if(inputElems[1].value!='' && inputElems[2].value !='' && inputElems[3].value!='') {
		
		document.getElementsByTagName('h2')[1].innerText="Muokkaa tietoja tai poista henkilö";
		document.getElementsByName('check')[0].style.display="none";
		document.getElementsByName('modify')[0].style.display="block";
		document.getElementsByName('remove')[0].style.display="block";
		document.getElementById('personid').style.display="block";
	}
	if(document.getElementById('persons').value==""){
		return false;
	}else{
		document.getElementById('personid').style.display="block";
		return true;
	}
	
}

function openKayttajat(){
	
	document.location="kayttajat.php";
	
}

function openSeuranta(){
	
	document.location="seuranta.php";
}

function openRaportit(){
	
	document.location="raportit.php";
}

