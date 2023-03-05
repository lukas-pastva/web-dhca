<?php
/*********************************************************************************************/
if (! userGetAccess($_SESSION['meno_uzivatela'], "foto") ) {
	header("location: index.php");
	die;
}
/*********************************************************************************************/
?>
<h3>Uprav fotografiu</h3>
<center>
<div class="clanok_autor">V tejto casti mozes upravovať fotografie v albumoch ku ktorým máš prístup.</div>
<br>
<?
/***************************************************************************************************************/

//Ak sa ide upravovat obrazok
if ($_GET['x'] == 2){

	//Osejpovanie premennych
	$text            = ($_POST['text']);
	$date                 = strtotime($_POST['date']);
	$filename             = strtolower($_FILES['file']['name']);
	$picture_id           = strip_tags($_POST['picture_id']);
	$section_id           = strip_tags($_POST['section_id']);

	//Ak je vybrany obrazok
	if ( $filename ){

		//Ak nieje obrazok v jpeg
		if ( ($_FILES['file']['type'] != "image/jpeg" ) && ($_FILES['file']['type'] != "image/pjpeg" ) ) {
		 alert ("Fotografia nie je vo formate jpeg, alebo ani nie je obrazok!");
		} else {

			$section = psw_mysql_fetch_array(psw_mysql_query('SELECT * FROM section WHERE section_id = "' .$section_id. '" '));
			$stara_foto = psw_mysql_fetch_array(psw_mysql_query('SELECT * FROM picture WHERE picture_id = "' .$picture_id. '" '));
			$destination = "../foto/".$section['main_section']."/".$section['sub_section']."/";

			unlink($destination.$stara_foto['filename']);
			unlink($destination."thumbs/".$stara_foto['filename']);

			//Nakopirovanie obrazka
			$filename_norm  = $destination.$filename;
			$filename_thumb = $destination."thumbs/".$filename;

			//Ak sa nepodari nahrat subor
			if ( ! (move_uploaded_file($_FILES['file']['tmp_name'],$filename_norm) || (copy($filename_norm,$filename_thumb)) ) ){
				alert("Chyba v nahravani obrazku!");
				die;
			}

			//Zmena rozlisenia obrazku
			$src_img  = imagecreatefromjpeg($filename_norm);
			$size_img = getimagesize($filename_norm);

			//Praca s thumbnailom
			$thumb_height = THUMB_PICTURE_HEIGHT;
			$thumb_width = $size_img[0] / ( $size_img[1] /THUMB_PICTURE_HEIGHT );
			$dst_img_thumb = imageCreateTrueColor($thumb_width,$thumb_height);
			imagecopyresampled($dst_img_thumb, $src_img, 0, 0, 0, 0, $thumb_width, $thumb_height, $size_img[0], $size_img[1]);
			imagejpeg($dst_img_thumb, $filename_thumb, THUMB_PICTURE_QUALITY);

			//Praca s velkym obrazkom (len v pripade, ze je vacsi ako BIG_PICTURE_WIDTH v ose x)
			$big_width = BIG_PICTURE_WIDTH;
			/*
			 if ( $size_img[0] > $big_width ){


			 //Praca s tymto obrazkom..musim rozhodnut, ci sa jedna o sirokouhly, ci nie....
			 //Ak je sirokouhly
			 if(( ($size_img[0] / $size_img[1]) > 3 ) && ( $size_img[0] > BIG_WIDE_PICTURE_WIDTH ) ){

			 $big_width = BIG_WIDE_PICTURE_WIDTH;
			 $big_height = $size_img[1] / ( $size_img[0] / $big_width );
			 $dst_img_big = imageCreateTrueColor($big_width, $big_height);
			 imagecopyresampled($dst_img_big, $src_img, 0, 0, 0, 0, $big_width, $big_height, $size_img[0], $size_img[1]);
			 imagejpeg($dst_img_big, $filename_norm, BIG_PICTURE_QUALITY);

			 //Ak nieje sirokouhly
			 } else {

			 $big_height = $size_img[1] / ( $size_img[0] / $big_width );
			 $dst_img_big = imageCreateTrueColor($big_width, $big_height);
			 imagecopyresampled($dst_img_big, $src_img, 0, 0, 0, 0, $big_width, $big_height, $size_img[0], $size_img[1]);
			 imagejpeg($dst_img_big, $filename_norm, BIG_PICTURE_QUALITY);
			 }

			 } else {
			 */
			$dst_img_big = imageCreateTrueColor($size_img[0], $size_img[1]);
			imagecopyresampled($dst_img_big, $src_img, 0, 0, 0, 0, $size_img[0], $size_img[1], $size_img[0], $size_img[1]);
			imagejpeg($dst_img_big, $filename_norm, BIG_PICTURE_QUALITY);
			//}

			//Upravia sa vlastnosti v DB
			psw_mysql_query('UPDATE picture SET filename = "'. $filename .'" WHERE picture_id = "' .$picture_id. '" ');

		}
	}

	//Ak sa nezada datum
	if ( ! $_POST['date'] ){
		alert("Nevlozil si datum!");
	} else {

		//Ak je datum v nespravnom formate
		if ( !strtotime($_POST['date']) ){
			alert("Nezadal si datum v spravnom formate!");
		} else {
			//Upravia sa vlastnosti v DB
			psw_mysql_query('UPDATE picture SET text = "' .$text. '", date = "'. $date .'" WHERE picture_id = "' .$picture_id. '" ');
		}
	}
}

/***************************************************************************************************************/

//Ak sa ide mazat obrazok
if ($_GET['x'] == 3){

	$picture_id = strip_tags($_POST['picture_id']);

	$picture_info1 = psw_mysql_fetch_array(psw_mysql_query('SELECT * FROM picture WHERE picture_id = "' .$picture_id. '" '));
	$picture_info2 = psw_mysql_fetch_array(psw_mysql_query('SELECT * FROM section  WHERE section_id = "' .$picture_info1['section_id']. '" '));
	unlink("../foto/".$picture_info2['main_section']."/".$picture_info2['sub_section']."/".$picture_info1['filename']);
	unlink("../foto/".$picture_info2['main_section']."/".$picture_info2['sub_section']."/thumbs/".$picture_info1['filename']);

	if ( psw_mysql_query('DELETE FROM picture WHERE picture_id = "' .$picture_id. '" ') );

}

/***************************************************************************************************************/

?>

<form action="index.php?file=admin_foto_edit.php" method="post">
 Vyber fotoalbum ktorého fotografie chceš zobraziť. 
 <select onchange="submit();"	name="section_id" size="1">
	<option value=""></option>
	<?
	$query = psw_mysql_query('SELECT main_section FROM section GROUP BY main_section');
	while ($fetch = psw_mysql_fetch_array($query) ){
		if( userGetAccess($_SESSION['meno_uzivatela'], $fetch['main_section']) ){
			echo "\r\n\t<optgroup label=\"" .$fetch['main_section']. "\">";
			$query2 = psw_mysql_query('SELECT * FROM section WHERE main_section = "'.$fetch['main_section'].'" ORDER BY sub_section ASC');
			while ($fetch2 = psw_mysql_fetch_array($query2) ){
				//Vypiseme pocet obrazkov do zatvoriek
				$pocet_obrazkov = psw_mysql_fetch_array(psw_mysql_query('SELECT count(*) AS pocet_obrazkov FROM picture WHERE section_id = "' .$fetch2['section_id']. '" '));
				echo "\r\n\t<option ";
				if($section_id == $fetch2['section_id']) {echo "selected";}
				echo " value=\"" .$fetch2['section_id']. "\">" .$fetch2['section_name']. " (" .$pocet_obrazkov['pocet_obrazkov']. ")";
			}
			echo "\r\n\t</optgroup>\n";
		}
	}
	?>
</select><br /><br /></form>
	<?

	/***************************************************************************************************************/

	$section_id = strip_tags($_POST['section_id']);

	$fotos = psw_mysql_query('SELECT * FROM picture WHERE section_id="' .$section_id. '" ORDER BY date DESC');

	while ($foto=psw_mysql_fetch_array($fotos)){
		$section_fetch = psw_mysql_fetch_array(psw_mysql_query('SELECT * FROM section WHERE section_id = "' .$section_id. '" '));
		$destination_norm  = "../foto/".$section_fetch['main_section']."/".$section_fetch['sub_section']."/".$foto['filename'];
		$destination_thumb = "../foto/".$section_fetch['main_section']."/".$section_fetch['sub_section']."/thumbs/".$foto['filename'];

		//Ak sa ide upravovat obrazok
		if (($_GET['x'] == 1) && ($_POST['picture_id'] == $foto['picture_id'])){
			?> <a name="picture"></a>
<div class="admin_msg_left">
<form action="index.php?file=admin_foto_edit.php&amp;x=2"
	enctype="multipart/form-data" method="post"><input type="hidden"
	name="picture_id" value="<? echo $foto['picture_id']; ?>"> <input
	type="hidden" name="section_id" value="<? echo $foto['section_id']; ?>">
<table border="0" align="center">
	<tr>
		<td align="center" width="120"><b>Dátum:</b></td>
		<td align="center" width="280"><b>Fotografia:</b></td>
		<td align="center" width="150"><b>Text:</b></td>
	</tr>
	<tr>
		<td align="center"><input type="text" name="date" size="26"
			value="<? echo date('Y-m-d G:h:s', ($foto['date'])); ?>"></td>
		<td align="center"><a href="<? echo $destination_norm; ?>"
			target="_blank"><img src="<? echo $destination_thumb; ?>" border="1"></a><br>
		</td>
		<td align="center"><textarea cols="30" rows="4" name="text"><? echo $foto['text']; ?></textarea>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="left">Súbor(voliteľné):<input type="file"
			name="file"></td>
		<td colspan="2" align="center"><input type="submit"
			value="Uprav fotografiu"></td>
	</tr>
</table>
</form>
<center>
<form action="index.php?file=admin_foto_edit.php&amp;x=3" method="post">
<input type="hidden" name="section_id"
	value="<? echo $foto['section_id']; ?>"> <input type="hidden"
	name="picture_id" value="<? echo $foto['picture_id']; ?>"> <input
	type="submit" value="Vymaž fotografiu" onClick="if(confirm('Skutočne chcete zmazať fotograiu?')){return true;}else{return false;}" ></form>
</center>
</div>
<br>

			<?
			//Ak sa neide upravovat obrazok
		} else {
			?>
<form action="index.php?file=admin_foto_edit.php&amp;x=1#picture"
	method="post"><input type="hidden" value="<? echo $section_id; ?>"
	name="section_id"> <input type="hidden" name="picture_id"
	value="<? echo $foto['picture_id']; ?>">
<div class="admin_msg_left">
<table border="0" align="center">
	<tr>
		<td align="center" width="120"><b>Dátum:</b></td>
		<td align="center" width="280"><b>Fotografia:</b></td>
		<td align="center" width="150"><b>Text:</b></td>
	</tr>
	<tr>
		<td align="center"><? echo date('Y-m-d G:h:s', ($foto['date'])); ?></td>
		<td align="center"><a href="<? echo $destination_norm; ?>"
			target="_blank"><img src="<? echo $destination_thumb; ?>" border="1"></a>
		</td>
		<td align="center"><? echo $foto['text']; ?></td>
	</tr>
	<tr>
		<td colspan="4" align="center"><input type="submit"
			value="Uprav fotografiu"></td>
	</tr>
</table>
</div>
</form>
			<?
		}
	}
	?></center>
