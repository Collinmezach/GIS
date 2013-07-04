<?php
function connectDB() {
	$user = "root";
	$pass = "hoihoi9";
	$dbh = new PDO("mysql:host=localhost; dbname=dms", $user, $pass );
	$error = $dbh->errorInfo();
	return $dbh;
}
function ftime($time) {
	$phpdate = strtotime($time);
	$mysqldate = date( 'd-m-Y H:i', $phpdate );
	return $mysqldate;	
}
	
function nldate () {
 $datum = date("j F Y");
    $dagvanweek = date("l");
    $arraydag = array(
    "Zondag",
    "Maandag",
    "Dinsdag",
    "Woensdag",
    "Donderdag",
    "Vrijdag",
    "Zaterdag"
    );
    $dagvanweek = $arraydag[date("w")];
    $arraymaand = array(
    "januari",
    "februari",
    "maart",
    "april",
    "mei",
    "juni",
    "juli",
    "augustus",
    "september",
    "oktober",
    "november",
    "december"
    );
    $datum = date("j ") . $arraymaand
    [date("n") - 1];
	$tijd = date("H:i");
    $return  = $dagvanweek." ".$datum." ".$tijd;
	return $return; 	
}

function lock($id) {
	$user = "root";
	$pass = "hoihoi9";
	$dbh = new PDO("mysql:host=localhost; dbname=dms", $user, $pass);
	$error = $dbh->errorInfo();
	$sth = $dbh->prepare("UPDATE incident SET I_lock = '1' WHERE I_id =:id ");
	$sth->bindValue(':id', $id, PDO::PARAM_INT);
	$suc = $sth->execute();
	return $suc;			
	}

function unlock($id) {
	$user = "root";
	$pass = "hoihoi9";
	$dbh = new PDO("mysql:host=localhost; dbname=dms", $user, $pass);
	$error = $dbh->errorInfo();
	$sth = $dbh->prepare("UPDATE incident SET I_lock = '0' WHERE I_id =:id ");
	$sth->bindValue(':id', $id, PDO::PARAM_INT);
	$suc = $sth->execute();
	return $suc;			
	}
			

?>
