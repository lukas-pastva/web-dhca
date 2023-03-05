<?php
/*********************************************************************************************/
if (! userGetAccess($_SESSION['meno_uzivatela'], "foto") ) {
	header("location: index.php");
	die;
}
/*********************************************************************************************/

//Ak sa ide vkladat obrazok
if ($_REQUEST['section_id']){
	$section = psw_mysql_fetch_array(psw_mysql_query('SELECT * FROM section WHERE section_id = "' .$section_id. '" '));
	//Osejpovanie premennych
	if ( ! strtolower($_FILES['file']['name']) ){
		alert("Nevybral si foto!");
	} else {
		if($_FILES['file']['size'] > 2000000){
			alert("Subor musi zaberat maximalne 2 MB!");
		} else {
			//Ak uz dany obrazok existuje
			if (psw_mysql_fetch_array(psw_mysql_query('SELECT * FROM picture WHERE filename  = "' .strtolower($_FILES['file']['name']). '" AND section_id = "' .$_REQUEST['section_id']. '" '))){
				alert("Takato foto uz existuje");
			} else {
				//Ak nieje obrazok v jpeg
				if ( ($_FILES['file']['type'] != "image/jpeg" ) && ($_FILES['file']['type'] != "image/pjpeg" ) ) {
					alert ("Obrazok nie je vo formate jpeg, alebo ani nie je obrazok!");
				} else {
					if ( ! $_REQUEST['date'] ){
						alert("Nevlozil si datum!");
					} else {
						if ( !strtotime($_REQUEST['date']) ){
							alert("Nezadal si datum v spravnom formate!");
						} else {

							//Cesta k fotkam
							$destination = "../foto/".$section['main_section']."/".$section['sub_section']."/";
							$filename_norm  = $destination.strtolower($_FILES['file']['name']);
							$filename_thumb = $destination."thumbs/".strtolower($_FILES['file']['name']);

							//Ak sa nepodari nahrat subor
							if ( ! (move_uploaded_file($_FILES['file']['tmp_name'],$filename_norm) || (copy($filename_norm,$filename_thumb)) ) ){
								alert("Chyba v nahravani obrazku!");
								die;
							}

							//Zmena rozlisenia obrazku
							$src_img  = imagecreatefromjpeg($filename_norm);
							$size_img = getimagesize($filename_norm);

							//Praca s thumbnailom
							$thumb_width = $size_img[0] / ( $size_img[1] /THUMB_PICTURE_HEIGHT );
							$dst_img_thumb = imageCreateTrueColor($thumb_width,THUMB_PICTURE_HEIGHT);
							imagecopyresampled($dst_img_thumb, $src_img, 0, 0, 0, 0, $thumb_width, THUMB_PICTURE_HEIGHT, $size_img[0], $size_img[1]);
							imagejpeg($dst_img_thumb, $filename_thumb, THUMB_PICTURE_QUALITY);

							//Praca s velkym obrazkom (len v pripade, ze je vacsi ako BIG_PICTURE_WIDTH v ose x)
							/*
							 if ( $size_img[0] > BIG_PICTURE_WIDTH ){

							 //Praca s tymto obrazkom..musim rozhodnut, ci sa jedna o sirokouhly, ci nie....
							 //Ak je sirokouhly
							 if(( ($size_img[0] / $size_img[1]) > 3 ) && ( $size_img[0] > BIG_WIDE_PICTURE_WIDTH ) ){

							 $big_height = $size_img[1] / ( $size_img[0] / BIG_PICTURE_WIDTH );
							 $dst_img_big = imageCreateTrueColor(BIG_PICTURE_WIDTH, $big_height);
							 imagecopyresampled($dst_img_big, $src_img, 0, 0, 0, 0, BIG_PICTURE_WIDTH, $big_height, $size_img[0], $size_img[1]);
							 imagejpeg($dst_img_big, $filename_norm, BIG_PICTURE_QUALITY);

							 //Ak nieje sirokouhly
							 } else {

							 $big_height = $size_img[1] / ( $size_img[0] / BIG_PICTURE_WIDTH );
							 $dst_img_big = imageCreateTrueColor(BIG_PICTURE_WIDTH, $big_height);
							 imagecopyresampled($dst_img_big, $src_img, 0, 0, 0, 0, BIG_PICTURE_WIDTH, $big_height, $size_img[0], $size_img[1]);
							 imagejpeg($dst_img_big, $filename_norm, BIG_PICTURE_QUALITY);
							 }

							 } else {
							 */
							$dst_img_big = imageCreateTrueColor($size_img[0], $size_img[1]);
							imagecopyresampled($dst_img_big, $src_img, 0, 0, 0, 0, $size_img[0], $size_img[1], $size_img[0], $size_img[1]);
							imagejpeg($dst_img_big, $filename_norm, BIG_PICTURE_QUALITY);
							//}

							psw_mysql_query('INSERT INTO picture (section_id, filename, text, date) VALUES ( "' .$_REQUEST['section_id']. '", "' .strtolower($_FILES['file']['name']). '", "' .$_REQUEST['text']. '", "' .strtotime($date). '" ) ');
						}
					}
				}
			}
		}
	}
}
/***************************************************************************************************************/
echo '
<h3>Vlož fotografiu</h3>
<center>
 <div class="clanok_autor">V tejto casti mozes vkladat obrazky do uz vytvorenych sekcii. <br />
  Postup je jednoduchy, vyber sekciu, foto a napis datum v tomto formate <b>RRRR-MM-DD HH:MM:SS</b>
 </div>
 <br />
 <div class="admin_msg_left">
  <form action="index.php?file=admin_foto2.php" method="post"	enctype="multipart/form-data">
   <table>
    <tr>
     <td>
      Fotoalbum do ktorého sa fotka vloží:
     </td>
     <td>
      <select name="section_id" size="1" style="width: 300px;">
	     <option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>';

$query = psw_mysql_query('SELECT main_section FROM section GROUP BY main_section');
while ($fetch = psw_mysql_fetch_array($query) ){
	if( userGetAccess($_SESSION['meno_uzivatela'], $fetch['main_section']) ){
		echo '<optgroup label="' .$fetch['main_section']. '">';
		$query2 = psw_mysql_query('SELECT * FROM section WHERE main_section = "'.$fetch['main_section'].'" ORDER BY sub_section ASC');
		while ($fetch2 = psw_mysql_fetch_array($query2) ){

			//Vypiseme pocet obrazkov do zatvoriek
			$pocet_obrazkov = psw_mysql_fetch_array(psw_mysql_query('SELECT count(*) AS pocet_obrazkov FROM picture WHERE section_id = "' .$fetch2['section_id']. '" '));
			echo '<option ';
			if($_REQUEST['section_id'] == $fetch2['section_id']) {echo 'selected="selected"';}
			echo ' value="' .$fetch2['section_id']. '">' .$fetch2['sub_section']. ' (' .$pocet_obrazkov['pocet_obrazkov']. ')';
		}
		echo '</optgroup>';
	}
}

echo '
       </select>
      </td>
     </tr>
     <tr>
      <td>      
    	 Subor:
    	</td>
    	<td> 
       <input type="file" name="file" size="40" />
      </td>
     </tr>
     <tr>
      <td>
       Dátum:<b>(RRRR-MM-DD HH:MM:SS)</b>
      </td>
      <td>
       <input type="text" style="width: 150px;" name="date" value="' .date('Y-m-d G:m:s'). '" />
      </td>
     </tr>
     <tr>
      <td>
       Text:
      </td>
      <td>
       <textarea cols="35" rows="4" name="text" style="width: 300px;"></textarea>
      </td>
     </tr>
     <tr>
      <td colspan="2">
       <input type="submit" value="Vlož fotografiu" />
      </td>
     </tr>
    </table>
  </form>
 </div>
</center>';
