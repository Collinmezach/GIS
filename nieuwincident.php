<?php
session_start();
if(!$_SESSION['check']) {
	header("location:/login.php");
}
else {
include_once("functions.php");

if (isset($_POST['opslaan'])) {
	if(!isset($_POST['titel'])) { $error['titel'] == true; }
	if(!isset($_POST['omschrijving'])) { $error['omschrijving'] == true; }
	if(!isset($_POST['toewijzing'])) { $error['toewijzing'] == true; }
	
	if(empty($error)) {
		$date = date("Y-m-d H:i:s");
		$user = $_SESSION['userid'];
		$date2 = strtotime($date,"+ 3 days");
		
		$dbh = connectDB();
		$sth = $dbh->prepare("INSERT INTO incident (I_id, I_bedrijf, I_prioriteit, I_titel, I_omschrijving, I_datumtijd, I_melder, I_aangenomen,
							  I_toewijzing, I_status, I_opvolgtijd) 
							  VALUES (NULL, :bedrijf, 0,:titel, :omschrijving, '".$date."' ,:melder ,".$user.", :toewijzing, 0 ,'".$date2."') ");
		$sth->bindValue(':bedrijf', $_POST['bedrijf'], PDO::PARAM_INT);
		$sth->bindValue(':titel', $_POST['titel'], PDO::PARAM_STR);
		$sth->bindValue(':omschrijving', $_POST['omschrijving'], PDO::PARAM_STR);
		$sth->bindValue(':melder', $_POST['melder'], PDO::PARAM_STR);
		$sth->bindValue(':toewijzing', $_POST['toewijzing'], PDO::PARAM_INT);
		
		$suc = $sth->execute();
		if(!$suc){ 
			$mysqlerror = $sth->errorInfo(); 
		}
		else {
			$sth = $dbh->prepare("SELECT I_id, I_titel, I_omschrijving, B_bedrijfsnaam, U_email
								  FROM incident
								  JOIN user ON I_toewijzing = U_id
								  JOIN bedrijf ON I_bedrijf = B_id
								  ORDER BY I_id DESC
								  LIMIT 1");
			$suc = $sth->execute();
			$result = $sth->fetchAll(PDO::FETCH_ASSOC);	
			if(!$suc){ 
				$mysqlerror = $sth->errorInfo(); 
			}
			else{
				foreach($result as $row) {
					$adres = $row['U_email'];
				}
			}
			$subject = "GIS - Nieuw incident ".$row['I_id'];
			$message = "Er is een nieuw incident aan jou toegewezen \n
Bedrijf: ".$row['B_bedrijfsnaam']."
Titel: ".$row['I_titel']."
Omschrijving: ".$row['I_omschrijving'];
			$headers = "From: G-com Informatie Systeem <systeem@g-com.nl> \r \n";			
						
			mail($adres, $subject, $message,$headers);
			
			
			header("Location:/index.php");
		}
	}
}
		
		 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Nieuw Incident | G-com Informatie Systeem</title>
<link rel="stylesheet" type="text/css" href="css/main.css"  />
<script type="text/javascript">function submitform(){  document.bedrijf.submit();}</script>
</head>

<body>
<table>
<form name="bedrijf" action="<?php echo($_SERVER['PHP_SELF']) ?>" method="post">
	<tr>
    	<td width="150">Bedrijf</td>
        <td><select name="bedrijf" onchange="submitform();">
        		<option>Maak een keuze</option>
        <?php
		$dbh = connectDB();
		$sth = $dbh->prepare("SELECT B_id, B_bedrijfsnaam FROM bedrijf ORDER BY B_bedrijfsnaam");
		$suc = $sth->execute();
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);

		if(!$suc){ 
			$mysqlerror = $sth->errorInfo(); 
		}
		else {
			foreach($result as $row) {
			echo ("<option value=\"".$row['B_id']."\"");
				if($_POST['bedrijf'] == $row['B_id']) { echo("selected=\"selected\""); }
			echo(">".$row['B_bedrijfsnaam']."</option>");	
			}
		}
		$dbh = 0;
		$sth = 0;
		?>
        	</select>
            </form>
        </td>
     </tr>
     <tr>
     	<td colspan="2">
        <?php
		if(isset($_POST['bedrijf'])) {
			$dbh = connectDB();
			$sth = $dbh->prepare("SELECT * FROM bedrijf WHERE B_id = :bedrijf ");
			$sth->bindValue(':bedrijf', $_POST['bedrijf'], PDO::PARAM_INT);
			$suc = $sth->execute();
			$result = $sth->fetchAll(PDO::FETCH_ASSOC);	
			
			if(!$suc){ 
				$mysqlerror = $sth->errorInfo(); 
			}
			else {
				foreach($result as $row) {
					echo($row['B_bedrijfsnaam']."<br /> \n"
					.$row['B_straat']." ".$row['B_huisnr']."<br /> \n"
					.$row['B_postcode']." ".$row['B_plaats']."<br /> \n"
					.$row['B_telefoonnummer']."<br /> \n"
					.$row['B_email']);
					}
				}
			}
			?></td>
		</tr>			
  </table>
  eerdere incidenten
  <table>
  	<tr>
    	<td width="40">id</td>
        <td width="200">titel</td>
        <td width="150">meld datum</td>
        <td width="120">melder</td>
        <td width="80">toewijzing</td>
        <td width="80">status</td>
    </tr>
        <?php
		if(isset($_POST['bedrijf'])) {
			$sth = $dbh->prepare("SELECT I_id, I_titel, I_datumtijd, I_melder, U_voornaam, I_status FROM incident JOIN user ON I_toewijzing = U_id 
			WHERE I_bedrijf = :bedrijf
			ORDER BY I_datumtijd DESC
			LIMIT 0,2");
			$sth->bindValue(':bedrijf', $_POST['bedrijf'], PDO::PARAM_INT);
			$suc = $sth->execute();
			$result = $sth->fetchAll(PDO::FETCH_ASSOC);
			if(!$suc){ 
				$mysqlerror = $sth->errorInfo(); 
			}
			else {
				foreach($result as $row) {
					$phpdate = strtotime( $row['I_datumtijd'] );
					$mysqldate = date( 'd-m-Y H:i', $phpdate );

					
					echo("<tr><td>".$row['I_id']."</td>
						  <td>".$row['I_titel']."</td>
						  <td>".$mysqldate."</td>
						  <td>".$row['I_melder']."</td>
						  <td>".$row['U_voornaam']."</td>");
					if($row['I_status'] == 1) { echo("<td>opgelost</td>"); }
					else { echo("<td>open</td></tr>"); }
				}
			}
		}
		?>    
    </table>
     Nieuw incident <br />
    <form name="incident" action="<?php echo($_SERVER['PHP_SELF']); ?>" method="post">
    <table>
    	<tr><td width="180">Melder</td><td><input type="text" name="melder"/></td></tr>
        <tr><td>Titel</td><td><input type="text" name="titel" size="80" /></td></tr>
        <tr><td valign="top"> Omschrijving</td><td><textarea name="omschrijving" cols="59" rows="5"></textarea></td></tr>
        <tr><td>Toewijzen aan</td>
        <td><select name="toewijzing">
        	<option>Maak een keuze</option>
        <?php
		$dbh = connectDB();
		$sth = $dbh->prepare("SELECT U_id, U_voornaam FROM user");
		$suc = $sth->execute();
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);

		if(!$suc){ 
			$mysqlerror = $sth->errorInfo(); 
		}
		else {
			foreach($result as $row) {
			echo ("<option value=\"".$row['U_id']."\"");
			echo(">".$row['U_voornaam']."</option>");	
			}
		}
		
		$dbh = 0;
		$sth = 0;
		?>
        </select>
         <input type="hidden" name="bedrijf" value="<?php echo($_POST['bedrijf']) ?>" />
        </td>    
     </tr>
     <tr><td colspan="2"><input type="submit" name="opslaan" value="Opslaan" <?php if(!isset($_POST['bedrijf'])) { echo("disabled=\"disabled\""); } ?> />
  </table>
  </form> 
  <?php print_r($mysqlerror);  ?>
      
    
        
        
 </body>
</html>
<?php } ?>