<?php
session_start();
if(!$_SESSION['check']) {
	header("location:/login.php");
}
else {
	include_once("functions.php"); 		//functions.php invoegen
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Home | Gcom Informatie Systeem</title>
<link rel="stylesheet" type="text/css" href="css/main.css" />
<script type="text/javascript">function submitform(){  document.info.submit();}</script>
</head>

<body>
<center>
<div id="container">
	<div id="header">
    	<h1>G-com Informatie Systeem</h1>
    </div>
    <div id="header-right">
    <p>
    <a href="/"><img src="img/reload.png" alt="" border="0" width="14" height="14" align="absmiddle" /></a>
<?php
	echo(nldate());
	echo("</p><p>".$_SESSION['fuser']);
?>
</p>
<p><a href="logoff.php"><img src="img/logoff.png" alt="" border="0" /></a></p>
</div>
<div id="menu"><p>
	<a href="/"> HOME</a>
    <a href="nieuwincident.php">NIEUW INCIDENT</a>
    <a href="nieuwreparatie.php">NIEUWE REPARATIE</a>
    <a href="klanten.php">KLANTEN</a>
    <?php 
	if ($_SESSION['functie'] != "TD") {
		echo("<a href=\"beheer.php\">BEHEER</a>");
	}
	?>
</p></div>    
<div id="content"><br /><br />    
    <form name="info" action="<?php echo($_SERVER['PHP_SELF']) ?>" method="post">
    Gebruiker:<select name="user" onchange="submitform();">
    <?php if($_SESSION['functie'] != "TD") {  ?>
    <option value="0"<?php if($_POST['user'] == 0 ) { echo("selected"); } ?>>&lt;Alle&gt;</option>
    
    <?php 
	}
	$dbh = connectDB(); 				//maak verbinding met de database
	if($_SESSION['functie'] == "TD") {
		$sth = $dbh->prepare("SELECT U_id, U_voornaam FROM user WHERE U_id =".$_SESSION['userid']);
	}
	else {
		$sth = $dbh->prepare("SELECT U_id, U_voornaam FROM user");
	}
	$suc = $sth->execute();
	$result = $sth->fetchAll(PDO::FETCH_ASSOC);

	if(!$suc){ 
		$mysqlerror = $sth->errorInfo(); 
	}
	else {
		foreach($result as $row) {
			echo ("<option value=\"".$row['U_id']."\"");
				if(!isset($_POST['user'])) { if($_SESSION['userid'] == $row['U_id']) { echo ("selected"); }}
				if($_POST['user'] == $row['U_id']) { echo("Selected"); }
			echo(">".$row['U_voornaam']."</option>"); 
			}
		}
			
	?>
    </select>
    <br /><br />
 
   	Open of gesloten incidenten:
    <input type="radio" name="periode" value="0" <?php if (!isset($_POST['periode']) or $_POST['periode'] == 0) { echo ("checked"); } ?> onclick="submitform();"/>					open&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  
    <input name="periode" type="radio" value="1" <?php if ($_POST['periode'] == 1) { echo("checked"); } ?> onclick="submitform();"/>gesloten &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <input name="periode" type="radio" value="2" <?php if ($_POST['periode'] == 2) { echo("checked"); } ?> onclick="submitform();"/>beide
    </form>
    <br /><br />
    <table border="0">
    
    </tr>
	<tr>
    	<th width="5%">ID</td>
        <th width="15%">Bedrijf</td>
        <th width="30%">melding</td>
        <th width="12%">meld datum</td>
        <th width="8%">Melder</td>
        <th width="5%">Aangenomen</td>
        <th width="5%">Toewijzing</td>
    </tr>
<?php
	$dbh = connectDB(); 				//maak verbinding met de database
	if (!isset($_POST['user'])) {
		$_POST['user'] = $_SESSION['userid']; 
	}
	if($_POST['user']==0) {
		if(!isset($_POST['periode']) or $_POST['periode'] == 0) { 
			$sth = $dbh->prepare("SELECT I_id, I_prioriteit, I_titel, I_omschrijving, I_datumtijd, I_melder, I_status, U1.U_voornaam AS U_toewijzing, 
								U2.U_voornaam AS
		 						U_aangenomen, B_bedrijfsnaam
								FROM incident
								LEFT JOIN incident_regel ON I_id = IR_id
								LEFT JOIN user U1 ON I_toewijzing = U1.U_id
								LEFT JOIN user U2 ON I_aangenomen = U2.U_id
								LEFT JOIN bedrijf ON I_bedrijf = B_id
								WHERE I_status =0
								ORDER BY I_datumtijd DESC");
			$suc = $sth->execute();
			$result = $sth->fetchAll(PDO::FETCH_ASSOC);	
		}
		if($_POST['periode'] == 1) {
			$sth = $dbh->prepare("SELECT I_id, I_prioriteit, I_titel, I_omschrijving, I_datumtijd, I_melder, I_status, U1.U_voornaam AS U_toewijzing, 
								U2.U_voornaam AS
		 						U_aangenomen, B_bedrijfsnaam
								FROM incident
								LEFT JOIN incident_regel ON I_id = IR_id
								LEFT JOIN user U1 ON I_toewijzing = U1.U_id
								LEFT JOIN user U2 ON I_aangenomen = U2.U_id
								LEFT JOIN bedrijf ON I_bedrijf = B_id
								WHERE I_status =1
								ORDER BY I_datumtijd DESC");
		$suc = $sth->execute();
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);		
		}
		if($_POST['periode'] == 2) {
			$sth = $dbh->prepare("SELECT I_id, I_prioriteit, I_titel, I_omschrijving, I_datumtijd, I_melder, I_status, U1.U_voornaam AS U_toewijzing, 
									U2.U_voornaam AS
		 							U_aangenomen, B_bedrijfsnaam
									FROM incident
									LEFT JOIN incident_regel ON I_id = IR_id
									LEFT JOIN user U1 ON I_toewijzing = U1.U_id
									LEFT JOIN user U2 ON I_aangenomen = U2.U_id
									LEFT JOIN bedrijf ON I_bedrijf = B_id
									ORDER BY I_datumtijd DESC");
			$suc = $sth->execute();
			$result = $sth->fetchAll(PDO::FETCH_ASSOC);	
		}
	}
		else {
			if (!isset($_POST['periode']) or $_POST['periode'] == 0 ){
			$sth = $dbh->prepare("SELECT I_id, I_prioriteit, I_titel, I_omschrijving, I_datumtijd, I_melder, I_status, U1.U_id, U1.U_voornaam AS 
								U_toewijzing, U2.U_voornaam AS
		 						U_aangenomen, B_bedrijfsnaam
								FROM incident
								LEFT JOIN incident_regel ON I_id = IR_id
								LEFT JOIN user U1 ON I_toewijzing = U1.U_id
								LEFT JOIN user U2 ON I_aangenomen = U2.U_id
								LEFT JOIN bedrijf ON I_bedrijf = B_id
								WHERE U1.U_id = :user AND I_status =0
								ORDER BY I_datumtijd DESC");
			$sth->bindValue(':user', $_POST['user'], PDO::PARAM_INT);
			$suc = $sth->execute();
			$result = $sth->fetchAll(PDO::FETCH_ASSOC);	
		}
		if ($_POST['periode'] == 1 ) {
			$sth = $dbh->prepare("SELECT I_id, I_prioriteit, I_titel, I_omschrijving, I_datumtijd, I_melder, I_status, U1.U_voornaam AS U_toewijzing, 
								U2.U_voornaam AS
		 						U_aangenomen, B_bedrijfsnaam
								FROM incident
								LEFT JOIN incident_regel ON I_id = IR_id
								LEFT JOIN user U1 ON I_toewijzing = U1.U_id
								LEFT JOIN user U2 ON I_aangenomen = U2.U_id
								LEFT JOIN bedrijf ON I_bedrijf = B_id
								WHERE U1.U_id = :user AND I_status =1
								ORDER BY I_datumtijd DESC");
			$sth->bindValue(':user', $_POST['user'], PDO::PARAM_INT);
			$suc = $sth->execute();
			$result = $sth->fetchAll(PDO::FETCH_ASSOC);	
		}
		if ($_POST['periode'] == 2) {
			$sth = $dbh->prepare("SELECT I_id, I_prioriteit, I_titel, I_omschrijving, I_datumtijd, I_melder, I_status, U1.U_voornaam AS U_toewijzing, U2.U_voornaam AS
		 		U_aangenomen, B_bedrijfsnaam
				FROM incident
				LEFT JOIN incident_regel ON I_id = IR_id
				LEFT JOIN user U1 ON I_toewijzing = U1.U_id
				LEFT JOIN user U2 ON I_aangenomen = U2.U_id
				LEFT JOIN bedrijf ON I_bedrijf = B_id
				WHERE U1.U_id = :user
				ORDER BY I_datumtijd DESC");
			$sth->bindValue(':user', $_POST['user'], PDO::PARAM_INT);
			$suc = $sth->execute();
			$result = $sth->fetchAll(PDO::FETCH_ASSOC);	
		}
		
}
	

if(!$suc){ 
	$mysqlerror = $sth->errorInfo(); 
}
else {
	foreach($result as $row) {
		
		$phpdate = strtotime( $row['I_datumtijd'] );
		$mysqldate = date( 'd-m-Y H:i', $phpdate );
		$rij_kleur = ($a++ % 2) ? '#FFF' : '#999999';

		echo("<tr bgcolor=\"".$rij_kleur."\">
				<td><a href=\"bekijkincident.php?id=".$row['I_id']."\">".$row['I_id']."</a></td>
				<td>".$row['B_bedrijfsnaam']."</td>
				<td>".$row['I_titel']."</td>
				<td>".$mysqldate."</td>
				<td>".$row['I_melder']."</td>
				<td>".$row['U_aangenomen']."</td>	
				<td>".$row['U_toewijzing']."</td>				
			  </tr>");
		
	}
}
	
?>
</table>
</div>
</div>	
</center>
</body>
</html>
<?php
if(isset($mysqlerror)) { print_r($mysqlerror); }
} 
?>