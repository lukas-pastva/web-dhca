<?php
ob_start();

session_start();
session_register('meno_uzivatela');
/*********************************************************************************************/
if ( ! $_SESSION['meno_uzivatela'] ) {
	if (! userGetAccess($_SESSION['meno_uzivatela'], "clanok") ) {
		header("location: index.php");
		die;
	}
}
/*********************************************************************************************/
include_once("admin_functions.php");

/*********************************************************************************************/
if(echoErrors($_REQUEST)){
	echo echoErrors($_REQUEST);
	// vlozit clanok
} else {
	if($_REQUEST['action']=='update_comment'){
		psw_mysql_query($sql = '
				UPDATE comment SET 
				 text = "'.($_REQUEST['text']).'",
				 nick = "'.($_REQUEST['nick']).'", 
				 mail = "'.($_REQUEST['mail']).'", 
				 ip = "'.$_REQUEST['ip'].'", 
				 datetime = "'.$_REQUEST['datetime'].'"
				 WHERE comment_id = '.$_REQUEST['comment_id'].'
				');
		if(mysql_error()){echo mysql_error();} else {$state = 'end';}
	}
	if($_REQUEST['action']=='disable_access'){
		psw_mysql_query($sql='INSERT INTO banlist (`ip`, `ban_type`, `reason`) VALUES ("'.$_REQUEST['ip'].'", "1", "Porušovanie pravidiel pri vkladaní komentárov!") ');
		if(mysql_error()){echo mysql_error();} else {$state = 'end';}
	}
}
/*********************************************************************************************/
echo '
<!doctype html public "-//w3c//dtd html 4.01 transitional//en">
<html>
 <head>
  <link rel="stylesheet" type="text/css" href="admin_style.css">
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <title>A.D.M.I.N. [Sewer + LZK]</title>
 </head>
 <body>
';
if($state != 'end'){
	//read comment
	if($id = $_REQUEST['id']){

		$row = getTableRow('comment', 'comment_id', $id);
		$row = $row[0];
		echo '
	<form action="'.$_SERVER['PHP_SELF'].'" method="post">
	<table>
	 <tr>
	  <td><b>Komenár k článku:</b></td>
	  <td>'.getClanokNameFromId($row['clanok_id']).'</td>
	 </tr>
	 <tr>
	  <td colspan="2"><b>Text:</b><br />
	  <textarea name="text" cols="51" rows="6">'.validateForm($row['text']).'</textarea></td>
	 </tr>
	 <tr>
	  <td><b>Nick:</b></td>
	  <td><input type="text" name="nick" value="'.validateForm($row['nick']).'" size="40" /></td>
	 </tr>
	 <tr>
	  <td><b>Mail:</b></td>
	  <td><input type="text" name="mail" value="'.validateForm($row['mail']).'" size="40" /></td>
	 </tr>
	 <tr>
	  <td><b>IP: </b>(<a href="http://www.hostip.info/index.html?spip='.$row['ip'].'">zisti viac</a>)</td>
	  <td><input type="text" name="ip" value="'.$row['ip'].'" size="40" /></td>
	 </tr>
	 <tr>
	  <td><b>Dátum a čas:</b></td>
	  <td><input type="text" name="datetime" value="'.($row['datetime']?$row['datetime']:date('Y-m-d G:i:s')).'" size="40" /></td>
	 </tr>
	 <tr>
	  <td><input type="hidden" name="action" value="update_comment" /><input type="hidden" name="comment_id" value="'.$row['comment_id'].'" /><input type="submit" value="Uprav komentár" /></td>
	 </tr>
	</table>
	</form>
	<table>
	 <tr>
	  <td>
	   <form action="'.$_SERVER['PHP_SELF'].'" method="post">
	    <input type="hidden" name="comment_id" value="'.$row['comment_id'].'" />
	    <input type="hidden" name="action" value="disable_access" />
      <input type="hidden" name="ip" value="' .$row['ip']. '" />
	    <input type="submit" value="Zakáž prístup danej IP" />
	   </form>
	  </td>
	 </tr>
	</table>
	';
	}
} else {
	echo '
	   <br /><br /><br />
		 <center>
		  Komentár úspešne upravený.<br />
		  <span style="cursor: pointer;" onClick="window.close();"><b>Zatvor okno</b></a>
		 </center>
		';
}
echo '
 </body>
</html>';

ob_end_flush();
?>