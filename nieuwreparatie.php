<?php
session_start();
if(!$_SESSION['check']) {
	header("location:/login.php");
}
else {
include_once("functions.php");

if(isset($_POST)) {
	if(!isset($_POST['klantnaam'])) { $error['klantnaam'] = true; }
	if(!isset($_POST['telefoonnummer'])) { $error['telefoonnummer'] = true; }
	if(!isset($_POST['serie'])) { $error['serie	'] = true; }
	if(!isset($_POST['actie'])) { $error['actie'] = true; }
	if(!isset($_POST['backup'])) { $error['backup'] = true; }
	if(!isset($_POST['omschrijving'])) { $error['omschrijving'] = true; }
	
	if(empty($error)) {
		
	}
}
	

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Nieuwe Reparatie | Gcom Informatie systeem</title>
<link rel="stylesheet" type="text/css" href="css/main.css" />
</head>

<body>
<center>
<form name="reparatie" action="<?php echo($_SERVER['PHP_SELF']) ?>" method="post">
<table>
	<tr><td width="150"> Type*</td>
    <td width="400"><input type="radio" name="type" value="Computer" />Computer  &nbsp;&nbsp;&nbsp; 
        			<input type="radio" name="type" value="Laptop" />Laptop
    </td><td width="150">Naam Klant*</td><td width="200"><input type="text" name="klantnaam" /></td>
    </tr>
    <tr><td>Merk*</td><td><input type="text" name="merk" /></td>
    	<td>Telefoonnummer*</td><td><input type="text" name="telefoon" /></td></tr>
    <tr><td>Type*</td><td><input type="text" name="serie" /></td>
    	<td>Emailadres</td><td><input type="text" name="email" /></td></tr>
    <tr><td>Serienr</td><td><input type="text" name="serienr" /></td>
    	<td>&nbsp;</td><td>&nbsp;</td></tr>
    <tr><td>&nbsp;</td><td>&nbsp;</td><td>prijsafspraak</td><td><input type="text" name="prijsafspraak" /></td></tr>
    <tr><td>Type actie*</td><td><input type="radio" name="actie" value="herinstallatie" />Herinstallatie &nbsp;&nbsp;&nbsp;&nbsp;
    					<input type="radio" name="actie" value="viruscontrole" />Viruscontrole &nbsp;&nbsp;&nbsp; 
                        <input type="radio" name="actie" / value="anders">Anders</td>
         <td>verwachte datum klaar</td><td><input type="text" name="datum" /></td></tr>
  	<tr><td>Gegevens backup?*<br /><br /><br /></td><td colspan="3"> <input type="radio" name="backup" value="1" />Ja &nbsp;&nbsp;&nbsp;&nbsp;
    						<input type="radio" name="backup" value="0" />Nee<br /><br /><br /></td></tr>
    <tr><td colspan="2">Probleemomschrijving* ( zo concreet mogelijk!!)</td><td colspan="2">&nbsp;</td></tr>
    <tr><td colspan="2"><textarea cols="40" name="omschrijving" rows="5"></textarea></td><td colspan="2">&nbsp;</td></tr>  
    <tr><td colspan="2">Te Installeren software</td><td colspan="2">&nbsp;</td></tr>
    <tr><td colspan="2"><textarea cols="40" name="software" rows="3"></textarea></td><td colspan="2">&nbsp;</td></tr> 
    <tr><td>Toewijzen aan*:</td><td colspan="3"><select name="toewijzing">
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
			echo ("<option value=\"".$row['U_id']."\">".$row['U_voornaam']."</option>");	
		}
	}

	?>
    </select>
                          
            
       
</table>
</form>
</center>
</body>
</html>
<?php } ?>