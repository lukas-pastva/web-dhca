<?php
ob_start();

echo '
<!doctype html public "-//w3c//dtd html 4.01 transitional//en">
<html>
 <head>
  <link rel="stylesheet" type="text/css" href="../style/style.css">
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <title>Vlož akciu</title>
 </head>
 <body style="background-color: #dddddd; padding: 4px;"> 
';
/*********************************************************************************************/
include_once("../admin/admin_functions.php");
/*********************************************************************************************/

if(echoErrors($_REQUEST)){
	echo '<span style="font-size: 11px;">'.echoErrors($_REQUEST).'</span><br />';
	// vlozit clanok
} else {
	if($_REQUEST['action']=='insert'){
		if($_FILES['_plagat']['size'] > 2000000){
			echo '<span style="font-size: 11px; color: #bb0000;">Súbor nesmie byť väčší ako 2MB.</span><br />';
		} else {
			if($_FILES['_plagat']['tmp_name']){
				if(mb_substr($_FILES['_plagat']['name'],-4)=='.jpg'){
					if($_nadpis = ($_REQUEST['_nadpis'])){
						if($_popis = ($_REQUEST['_popis'])){
							if($datum_cas = $_REQUEST['datum_cas']){
								if(strtotime($_REQUEST['datum_cas'])){

									$takeFile = fopen($_FILES['_plagat']['tmp_name'], "r");
									$file = fread($takeFile, filesize($_FILES['_plagat']['tmp_name']));
									fclose($takeFile);
									$plagat = chunk_split(base64_encode($file));

									//Praca s thumbnailom
									$src_img  = imagecreatefromjpeg($_FILES['_plagat']['tmp_name']);
									$size_img = getimagesize($_FILES['_plagat']['tmp_name']);
									$thumb_width = 100;
									$thumb_height = $size_img[1] / ( $size_img[0] / 100 );
									$dst_img_thumb = imageCreateTrueColor($thumb_width,$thumb_height);
									imagecopyresampled($dst_img_thumb, $src_img, 0, 0, 0, 0, $thumb_width, $thumb_height, $size_img[0], $size_img[1]);
									unlink($_FILES['_plagat']['tmp_name']);
									imagejpeg($dst_img_thumb, $_FILES['_plagat']['tmp_name'], 99);
									$thumb_file = fopen($_FILES['_plagat']['tmp_name'], "r");
									$thumb = fread($thumb_file, filesize($_FILES['_plagat']['tmp_name']));
									fclose($thumb_file);
									$thumbnail = chunk_split(base64_encode($thumb));

									psw_mysql_query($sql='
										INSERT INTO partylist 
										(title, text, datetime, link, thumb, poster) 
										VALUES 
										("'.$_nadpis.'", "'.$_popis.'", "'.$datum_cas.':00", "'.$_REQUEST['link'].'", "'.$thumbnail.'", "'.$plagat.'") ');
									//echo mysql_error();
									//debug($sql);
									$state='end';
								} else {
									echo '<span style="font-size: 11px; color: #bb0000;">Zadajte dátum a čas v správnom formáte <br />(RRRR-MM-DD hh:mm)</span><br /><br />';
								}
							}
						}
					}
				} else {
					echo '<span style="font-size: 11px; color: #bb0000;">Súbor musí mať príponu .jpg</span><br /><br />';
				}
			}
		}
	}
}
/*********************************************************************************************/

if($state != 'end'){
	echo '
	<h4>Vložiť akciu:</h4>
	Ak chceš informovať o neakej akcii, ktorá sa koná v tvojom okolí, alebo ktorú organizujež, tak vyplň a odošli tento formulár. 
	<form action="'.$_SERVER['PHP_SELF'].'" method="post" enctype="multipart/form-data">
	<table class="akcie">
	 <tr>
	  <td><br /><b>Názov akcie:*</b></td>
	  <td><br /><input type="text" name="_nadpis" value="'.validateForm($_REQUEST['_nadpis']).'" size="50" /></td>
	 </tr>
	 <tr>
	  <td><br /><b>Popis akcie:*</b></td>
	  <td><br /><input type="text" name="_popis" value="'.validateForm($_REQUEST['_popis']).'" size="50" /></td>
	 </tr>
	 <tr>
	  <td><br /><b>Dátum a čas konania akcie:*</b></td>
	  <td><br /><input type="text" name="datum_cas" value="'.($_REQUEST['datum_cas']?validateForm($_REQUEST['datum_cas']):date('Y-m-d G:i')).'" size="50" /></td>
	 </tr>
	 <tr>
	  <td colspan="2"><br /><b>Odkaz:</b><br />(Ak ho vložíš, po kliknutí na obrázok, sa naň presmeruje.)</td>
	 </tr>
	 <tr>
	  <td>&nbsp;</td><td><input type="text" name="link" value="'.validateForm($_REQUEST['link']).'" size="50" /></td>
	 </tr>
	 <tr>
	  <td><br /><b>Plagát:*</b></td>
	  <td><br /><input type="file" name="_plagat" size="33" /></td>
	 </tr>
	 <tr>
	  <td colspan="2">
	   <br /><br />
	   Položky označené * sú povinné.<br />
	   <input type="hidden" name="action" value="insert" />
	   <input type="submit" value="Vlož akciu" />
	  </td>
	 </tr>
	</table>
	</form>
	';
} else {
	echo '
	   <br /><br /><br />
		 <center>
		  <span style="font-size: 11px;">
		   Akcia úspešne pridaná.<br />
		   Bude zobrazená na webe akonáhle bude potvrdená administrátorom.<br />
		   <span style="cursor: pointer;" onClick="window.close();"><b>Zatvor okno</b></a></span>
		  </span>		   
		 </center>
		';
}
echo '
 </body>
</html>';

ob_end_flush();
?>