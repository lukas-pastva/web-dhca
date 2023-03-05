<?php
/*********************************************************************************************/
if (! userGetAccess($_SESSION['meno_uzivatela'], "partylist") ) {
	header("location: index.php");
	die;
}
/*********************************************************************************************/

echo '
<h3>Správa partylistu</h3>
    <div class="clanok_autor">
     Akcie sa vkladaju cez verejne rozhranie. Tu máš možnosť akcie len zmazať.<br />
     Akciu je potrebné pre zobrazenie schváliť.<br />
     Akcia sa automaticky vymaže, keď uplnynie deň v ktorý sa koná.
    </div><br />
';

/*********************************************************************************************/
if(echoErrors($_REQUEST)){
	echo echoErrors($_REQUEST);
	$_REQUEST['state']='detail';
	// vlozit odkaz
} else {
	if($_REQUEST['action']=='update'){
		if($_FILES['foto']['size'] > 2000000){
			echo '<span style="color: #bb0000;">Súbor nesmie byť väčší ako 2MB.</span><br />';
			$_REQUEST['state'] = 'detail';
		} else {
			if($_FILES['foto']['tmp_name']){
				$takeFile = fopen($_FILES['foto']['tmp_name'], "r");
				$file = fread($takeFile, filesize($_FILES['foto']['tmp_name']));
				fclose($takeFile);
				$uploadedImage = chunk_split(base64_encode($file));

				//Praca s thumbnailom
				$src_img  = imagecreatefromjpeg($_FILES['foto']['tmp_name']);
				$size_img = getimagesize($_FILES['foto']['tmp_name']);
				$thumb_width = 100;
				$thumb_height = $size_img[1] / ( $size_img[0] / 100 );
				$dst_img_thumb = imageCreateTrueColor($thumb_width,$thumb_height);
				imagecopyresampled($dst_img_thumb, $src_img, 0, 0, 0, 0, $thumb_width, $thumb_height, $size_img[0], $size_img[1]);
				unlink($_FILES['foto']['tmp_name']);
				imagejpeg($dst_img_thumb, $_FILES['foto']['tmp_name'], 99);
				$thumb_file = fopen($_FILES['foto']['tmp_name'], "r");
				$thumb = fread($thumb_file, filesize($_FILES['foto']['tmp_name']));
				fclose($thumb_file);
				$thumbnail = chunk_split(base64_encode($thumb));

				psw_mysql_query($sql = '
				UPDATE partylist SET 
				 title = "'.$_REQUEST['_title'].'",
				 text = "'.$_REQUEST['_text'].'",
				 link = "'.$_REQUEST['link'].'",		
				 ordering = '.$_REQUEST['_ordering'].',			 
				 datetime = "'.$_REQUEST['_datetime'].'", 
				 schvalene = "'.(($_REQUEST['schvalene']=='on'?'1':'0')).'",
				 thumb = "'.$thumbnail.'",
				 poster = "'.$uploadedImage.'"
				  WHERE partylist_id = '.$_REQUEST['partylist_id'].'
				');
			} else {
				psw_mysql_query($sql = '
				UPDATE partylist SET 
				 title = "'.$_REQUEST['_title'].'",
				 text = "'.$_REQUEST['_text'].'",
				 link = "'.$_REQUEST['link'].'",
				 ordering = '.$_REQUEST['_ordering'].',	
				 datetime = "'.$_REQUEST['_datetime'].'", 
				 schvalene = "'.(($_REQUEST['schvalene']=='on'?'1':'0')).'"
				  WHERE partylist_id = '.$_REQUEST['partylist_id'].'
				');
			}
			//debug($sql);
			if(mysql_error()){echo mysql_error();} else {echo '<span style="color: #bb0000;">Odkaz upravený.</span><br /><br />';}
		}
	}
	if($_REQUEST['action']=='delete'){
		psw_mysql_query($sql = 'DELETE FROM partylist WHERE partylist_id = '.$_REQUEST['partylist_id']);
		if(mysql_error()){echo mysql_error();} else {
			echo '<span style="color: #bb0000;">Akcia vymazanť</span><br /><br />';
		}
	}
}
/*********************************************************************************************/
if($_REQUEST['state']!='detail'){


	//getall rows
	$akcie = getTableRows('partylist','','ordering ASC');
	echo '<table class="data_table">';
	echo ' <tr class="even">
          <td><b>Dátum a čas konania akcie</b></td>
          <td><b>Názov akcie</b></td>
          <td><b>Schvalene</b></td>
          <td><b>Poradie</b></td>
         </tr>';
	$even = false;
	foreach($akcie as $key => $akcia){
		echo '<tr'.($even?' class="even"':'').'>
		       <td><a href="'.$_SERVER['PHP_SELF'].'?file=admin_partylist.php&state=detail&partylist_id='.$akcia['partylist_id'].'" title="Upraviť">'.mb_substr($akcia['datetime'],0,-3).'</a></td>
		       <td><a href="'.$_SERVER['PHP_SELF'].'?file=admin_partylist.php&state=detail&partylist_id='.$akcia['partylist_id'].'" title="Upraviť">'.$akcia['title'].'</a></td>
		       <td>'.(($akcia['schvalene']=='1')?'Áno':'Nie').'</td>
		       <td>'.$akcia['ordering'].'</td>
		      </tr>';
		if($even){$even=false;}else{$even=true;}
	}
	echo '</table>';



} else {

	//load
	$data = getTableRow('partylist', 'partylist_id', $_REQUEST['partylist_id']);
	$data = $data[0];

	echo '
<br />
<form action="' .$_SERVER['PHP_SELF']. '?file=admin_partylist.php" method="post" enctype="multipart/form-data">
<table>
 <tr>
  <td>
   <b>Dátum a čas konania:</b><br />(RRRR-MM-DD hh:mm)
  </td>
  <td>
   <input type="text" name="_datetime" value="' .mb_substr($data['datetime'],0,-3). '" size="80" /><br /><br />
  </td>
 </tr> 
 <tr>
  <td>
   <b>Názov akcie:</b>
  </td>
  <td>
   <br />
   <input type="text" name="_title" value="' .validateForm($data['title']). '" size="100" />
   <br /><br />
  </td>
 </tr> 
 <tr>
  <td>
   <b>Popis akcie:</b>
  </td>
  <td>
   <br />
   <input type="text" name="_text" value="' .validateForm($data['text']). '" size="100" />
   <br /><br />
  </td>
 </tr>
 <tr>
  <td>
   <b>Link:</b><br />(V prípade, že je vyplnený,<br /> po kliknutí na thumbnail<br /> sa otvorí stránka tohto linku)
  </td>
  <td>
   <input type="text" name="link" value="' .validateForm($data['link']). '" size="100" />
  </td>
 </tr>
 <tr>
  <td>
   <b>Obrázok:</b><br />(veľkosť plagátu, <br />thumbnail sa zmení <br />automaticky):
  </td>
  <td>
  <br /><br />
   Ak žiadny súbor nevyberieš, ostane pôvodný.<br />
   <input type="file" name="foto" size="63" /><br />   
   '.($data['thumb']?'<img src="../image/3/'.$_REQUEST['partylist_id'].'.jpg" style="border: 1px solid #666666;" />':'<b>Žiadny obrázok</b><br /><br />').'<br />
   '.($data['poster']?'<a href="../image/4/'.$_REQUEST['partylist_id'].'.jpg" target="_blank" /><b>Zobraz plagát</b></a><br /><br /><br />':'<b>Žiadny obrázok</b><br /><br />').'
  </td>
 </tr> 
 <tr>
  <td>
   <b>Schvalene?:</b>
  </td>
  <td>
   <input type="checkbox" name="schvalene" ' .($data['schvalene']?' checked="checked"':''). '" />
  </td>
 </tr>
 <tr>
  <td>
   <b>Poradie:</b>
  </td>
  <td>
   <br />
   <input type="text" name="_ordering" value="' .validateForm($data['ordering']). '" size="100" />
   <br /><br />
  </td>
 </tr> 
 <tr>
  <td colspan="2">
   <input type="submit" value="Upraviť akciu" />
  </td>
 </tr>
<input type="hidden" name="partylist_id" value="'.$_REQUEST['partylist_id'].'" />
<input type="hidden" name="action" value="update" />
<table>
</form>
<br />
<form action="' .$_SERVER['PHP_SELF']. '?file=admin_partylist.php" method="post" enctype="multipart/form-data">
 <input type="submit" value="Vymazať akciu" onClick="if(!confirm(\'Si si istý, že chceš zmazať akciu?\')){return false;}" />
 <input type="hidden" name="partylist_id" value="' .$_REQUEST['partylist_id']. '" />
 <input type="hidden" name="action" value="delete" />
</form> 
<br /><br />';
}
?>