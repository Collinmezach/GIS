<?php
session_start();					//sessie starten
include_once("functions.php"); 		//functions.php invoegen
// afhandelen inloggen
if(!empty($_POST)) {
	if(empty($_POST['user'])) { $error[0] = '- U heeft geen gebruikersnaam ingevuld <br />'; } // als gebruikersnaam veld leeg is maak errorvariable aan
	if(empty($_POST['pass'])) { $error[1] = '- U heeft geen wachtwoord ingevuld'; } // als wachtwoord veld leeg is maak errorvariable aan


if (empty($error)) {
	$dbh = connectDB(); // maak verbinding met de database
	$sth = $dbh->prepare("SELECT * FROM user WHERE U_voornaam = :user AND U_wachtwoord = :pass");
	$sth->bindValue(':user',$_POST['user'], PDO::PARAM_STR);
	$sth->bindValue(':pass',md5($_POST['pass']), PDO::PARAM_STR);
	$suc = $sth->execute();
	$count = $sth->rowCount();
	$result = $sth->fetchAll(PDO::FETCH_ASSOC);	
	foreach($result as $row) { $_SESSION['userid'] = $row['U_id'];
							   $_SESSION['functie'] = $row['U_functie'];
							   $_SESSION['user'] = $row['U_voornaam'];
							    $_SESSION['fuser'] = $row['U_voornaam']." ".$row['U_achternaam']; } 
							   	
	
		if(!$suc){ $mysqlerror = $sth->errorInfo(); 
				   $error[2] = $mysqlerror[2];
		}
		else {
			if($count == 1) {
				$_SESSION['check'] = 1;
			
				$datum = date("d-m-Y H:i:s");  
				$ip = $_SERVER['REMOTE_ADDR'];
				$sth = $dbh->prepare("INSERT INTO login (L_id, L_user, L_date, L_ip) VALUES (NULL, :user, '$datum', '$ip' )");
				$sth->bindValue(':user', $_POST['user'], PDO::PARAM_STR);					// beveilig gebruikersnaam tegen sql injectie
				$suc = $sth->execute();
				
				if(!$suc){ $mysqlerror = $sth->errorInfo(); 
				   $error[2] = $mysqlerror[2]; }
				else {   																		// voer de query uit
				header("Location:/index.php");
				}
			}
		else {
			$error[3] = '- De combinatie van gebruikersnaam <br /> en wachtwoord is onjuist'; 
			}
		}
	}
}
$dbh = 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<title>Login | DMS</title>
</head>

<body OnLoad="document.inloggen.user.focus();">
<center>
<form name="inloggen" method="post" action="<?php echo($_SERVER['PHP_SELF']); ?>" >
<table border="0">
	<tr>
    	<td colspan="2"><img src="img/logo.jpg" alt="logo" width="400px" /> <br /><br /></td>
    <tr>
       	<td>Gebruikersnaam: </td>
        <td align="right"><input  class="text" type="text" name="user" /></td>
    </tr>
        <tr>
         	<td>Wachtwoord</td>
            <td align="right"><input class="text" type="password" name="pass" /></td>
        </tr>
        <tr>
          	<td>&nbsp;</td>
            <td align="right"><input class="submit" type="submit" name="login" value="inloggen" /></td>
        </tr>    
        <?php
if(!empty($error)) {
	echo("<tr>
			<td colspan=\"2\"><font color=\"#FF0000\">");
	if(isset($error[0])) { echo($error[0]); }
	if(isset($error[1])) { echo($error[1]); }
	if(isset($error[2])) { echo($error[2]); }
	if(isset($error[3])) { echo($error[3]); }
	echo("</font></td></tr>");
	
} 
?>
</table>
</form>

</center>
</body>
</html>
