<?php
ob_start();

session_start();
session_register('meno_uzivatela');
/*********************************************************************************************/
if ( ! $_SESSION['meno_uzivatela'] ) {
	if (! userGetAccess($_SESSION['meno_uzivatela'], "playlists") ) {
		header("location: index.php");
		die;
	}
}
/*********************************************************************************************/
include_once("admin_functions.php");

/*********************************************************************************************/
if(echoErrors($_REQUEST)){
	echo echoErrors($_REQUEST);
} else {
	if($_REQUEST['action']=='upload_file'){
		//if($_FILES['_subor']['size']<2000000){
			if((mb_substr($_FILES['_subor']['name'], -4)!='.php')&&(mb_substr($_FILES['_subor']['name'], -5)!='.phtml')&&(mb_substr($_FILES['_subor']['name'], -3)!='.js')){
				if(copy($_FILES['_subor']['tmp_name'], '../mp3/'.strtolower($_FILES['_subor']['name']) )){
					$err = 'Súbor úspešne nahraný';
				}
			} else {
				$err = 'Súbor nesmie mať príponu .php, .phtml, .js';
			}
		//} else {
			//$err = 'Súbor nesmie byť väčší ako 2 MB';
		//}
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
 <body style="background-image: none; background-color: #999999;">
 <h3>Zoznam nahraných súborov</h3>
';
echo '<span style="color: #bf0000;">'.$err.'</span><br />';
//readnem vsetky subory, potom vypisem.
$handle = opendir('../mp3');
while (($file = readdir($handle))!==false) {
	if(!(($file=='.')||($file=='..'))){
		if(substr($file,-4)=='.mp3'){
		  echo '<a href="../mp3/'.$file.'" target="_blank">mp3/'.$file.'</a><br />';
		}
	}
}

echo '
	<form action="'.$_SERVER['PHP_SELF'].'" method="post" enctype="multipart/form-data">
	<br /><br />
	<table>
	 <tr>
	  <td><b>Súbor (max:'.ini_get('post_max_size').'):</b></td>
	  <td><input type="file" name="_subor" size="40" /></td>
	 </tr>
	 <tr>
	  <td><br /><input type="hidden" name="action" value="upload_file" /><input type="submit" value="Nahraj súbor" /></td>
	 </tr>
	</table>
	</form>
 </body>
</html>';

ob_end_flush();
?>