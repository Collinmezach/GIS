<?php
session_start();
if(!$_SESSION['check']) {
	header("location:/login.php");
}
else {
	include_once("functions.php");

	if(isset($_POST['incident_regel'])) {
		if(!isset($_POST['IR_omschrijving'])) { $error['IR_omschrijving'] = true; }
		if(!isset($_POST['IR_tijd'])) { $error['IR_tijd'] = true; }
	
		if (empty($error)) {
			$datumtijd = date("Y-m-d H:i:s");
			$dbh = connectDB();
			$sth = $dbh->prepare("INSERT INTO incident_regel (IR_id, IR_incident_id, IR_datumtijd, IR_omschrijving, IR_auteur, IR_tijd) 
								  VALUES(NULL, :id, :datumtijd, :omschrijving, :userid, :tijd)");
			$sth->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
			$sth->bindValue(':omschrijving', $_POST['IR_omschrijving'], PDO::PARAM_STR);
			$sth->bindValue(':datumtijd', $datumtijd, PDO::PARAM_STR);
			$sth->bindValue(':userid', $_SESSION['userid'], PDO::PARAM_INT);
			$sth->bindValue(':tijd', $_POST['IR_tijd'], PDO::PARAM_INT);
			$suc = $sth->execute();
		}
		if(!$suc){ 
			$mysqlerror = $sth->errorInfo(); 
		}
		else {
			header("location:/bekijkincident.php?id=".$_POST['id']);
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>G-com Informatie Systeem - <?php echo ($_GET['id']) ?> </title>
<link rel="stylesheet" type="text/css" href="css/styles.css" />
</head>

<body>
<center>
<table>
<form name="incident" action="" method="post">
<?php
	$dbh = connectDB();
	$sth = $dbh->prepare("	SELECT * FROM incident JOIN bedrijf ON I_bedrijf = B_id JOIN user U1 ON I_aangenomen = U1.U_id JOIN user U2 ON I_toewijzing = U2.U_id 
					  		WHERE I_id = :id");
	$sth->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
	$suc = $sth->execute();
	$result = $sth->fetchAll(PDO::FETCH_ASSOC);	

	if(!$suc){ 
		$mysqlerror = $sth->errorInfo(); 
	}
	else {
		foreach($result as $row) {
			if($row['I_lock']) {
				echo("er word momenteel door iemand anders aan dit incident gewerkt... <a href=\"index.php\">Keer terug</a>");
			}
			else {
				lock($_GET['id']);
				$time  = ftime($row['I_datumtijd']) ;
				echo("	<tr>
							<td width=\"400\">
								<h1> Incident ".$row['I_id']."<br />
					  			<h3> ".$row['I_titel']."
							</td>
							<td></td>
						</tr>
					  	<tr>
							<td>".$row['B_bedrijfsnaam']."</td><td>".$row['B_telefoonnummer']."</td>
						</tr>
					  	<tr>
							<td>".$row['B_straat']." ".$row['B_huisnr']."</td><td>email: ".$row['B_email']."</td>
						</tr>
			  			<tr>
							<td>".$row['B_postcode']." ".$row['B_plaats']."</td><td>Technisch contactpersoon: ".$row['B_tech_contact']."</td>
						</tr>
			  			<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
			   			<tr>
							<td>Melder: ".$row['I_melder']."</td>
							<td>&nbsp;<td>
						</tr>
			  			<tr>
							<td>meld datum: ".$time."</td>
							<td></td>
						</tr>
			  			<tr>
							<td colspan=\"2\"><textarea name=\"omschrijving\" cols=\"100\" rows=\"5\">".$row['I_omschrijving']."</textarea></td>
						</tr>
					");
					echo ("</form>
					</table>
					<br /><br />
	   				<table border=\"\1\">
	   	  				<tr>
	   						<td width=\"130\">datum</td>
							<td width=\"520\">voortgangstekst</td>
							<td width=\"75\">door:</td>
							<td width=\"75\">tijd (min)</td>
						</tr>
	   				");
					$sth = $dbh->prepare("SELECT * FROM incident_regel JOIN user ON IR_auteur = U_id WHERE IR_incident_id = :id ORDER BY IR_id DESC"  );
					$sth->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
					$suc = $sth->execute();
					$result = $sth->fetchAll(PDO::FETCH_ASSOC);	

					if(!$suc){ 
						$mysqlerror = $sth->errorInfo(); 
					}
					else { 
						foreach($result as $row) {
						$phpdate = strtotime($row['IR_datumtijd']);
						$datum = date("d-m-Y H:i",$phpdate);
						echo("<tr><td>".$datum."</td><td>".$row['IR_omschrijving']."</td><td>".$row['U_voornaam']."</td><td>".$row['IR_tijd']."");
					}
				}
			}
		}
	}

	if (isset($mysqlerror)) { print_r($mysqlerror); } 
?>
<form name="incidentregel" action="<?php echo($_SERVER['PHP_SELF']) ?>" method="post">
<tr><td>(nieuw)</td><td><input type="text" name="IR_omschrijving" size="83"/></td><td><?php echo($_SESSION['user']) ?></td><td>
<input type="number" size="9" name="IR_tijd" /></td></tr>
<tr><td colspan="4"><input type="submit" value="Opslaan" name="incident_regel" /></td></tr>
<input type="hidden" name="id" value="<?php echo($_GET['id']); ?>" />
</form>

</table>
<a href="unlock.php?id=<?php echo($_GET['id']) ?>">Keer terug</a>
</center>
</body>
</html>
<?php 
} 
?>