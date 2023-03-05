<?php
/*********************************************************************************************/
if (! userGetAccess($_SESSION['meno_uzivatela'], "odkazy") ) {
	header("location: index.php");
	die;
}
/*********************************************************************************************/

echo '
<h3>Správa partnerov</h3>
    <div class="clanok_autor">
     Vlož, alebo uprav odkaz - ikonku partnerského webu.<br />
     Ikonu nieje nutné vkladať v prípade, že sa na stránke partneri vypisujú textovo.
    </div><br />
';

/*********************************************************************************************/
if(echoErrors($_REQUEST)){
	echo echoErrors($_REQUEST);
	// vlozit odkaz
} else {
	/*********************************************************************************************/
	if($_REQUEST['action']=='insert'){
		if($_FILES['foto']['size'] > 307200){
			echo '<span style="color: #bb0000;">Súbor nesmie byť väčší ako 300kb.</span><br />';
		} else {
			if($_FILES['foto']['tmp_name']){
				if(mb_substr($_FILES['foto']['name'],-4)=='.jpg'){
					$takeFile = fopen($_FILES['foto']['tmp_name'], "r");
					$file = fread($takeFile, filesize($_FILES['foto']['tmp_name']));
					fclose($takeFile);
					$uploadedImage = chunk_split(base64_encode($file));
					psw_mysql_query($sql='
						INSERT INTO odkaz (alt, image, link) VALUES 
						("'.$_REQUEST['_alt'].'", "'.$uploadedImage.'", "'.$_REQUEST['_link'].'") ');
					if(mysql_error()){echo mysql_error();} else {$_REQUEST = null; echo '<span style="color: #bb0000;">Odkaz pridaný.</span><br />';}
				} else {
					echo '<span style="font-size: 11px; color: #bb0000;">Súbor musí mať príponu .jpg</span><br /><br />';
				}
			} else {
				psw_mysql_query($sql='
						INSERT INTO odkaz (alt, image, link) VALUES 
						("'.$_REQUEST['_alt'].'", "no image", "'.$_REQUEST['_link'].'") ');
			}
		}
	}
	/*********************************************************************************************/
	if($_REQUEST['action']=='update'){

		if($_FILES['foto']['size'] > 307200){
			echo '<span style="color: #bb0000;">Súbor nesmie byť väčší ako 300kb.</span><br />';
			$_REQUEST['state'] = 'detail';
		} else {
			if($_FILES['foto']['tmp_name']){
				if(mb_substr($_FILES['foto']['name'],-4)=='.jpg'){
					$takeFile = fopen($_FILES['foto']['tmp_name'], "r");
					$file = fread($takeFile, filesize($_FILES['foto']['tmp_name']));
					fclose($takeFile);
					$uploadedImage = chunk_split(base64_encode($file));
					psw_mysql_query($sql = '
						UPDATE odkaz SET 
						 alt = "'.$_REQUEST['_alt'].'",
						 link = "'.$_REQUEST['_link'].'", 
						 image = "'.$uploadedImage.'" WHERE odkaz_id = '.$_REQUEST['odkaz_id'].'
						');	
				} else {
					echo '<span style="font-size: 11px; color: #bb0000;">Súbor musí mať príponu .jpg</span><br /><br />';
				}
			} else {
				psw_mysql_query($sql = '
				UPDATE odkaz SET 
				 alt = "'.$_REQUEST['_alt'].'",
				 link = "'.$_REQUEST['_link'].'"
				 WHERE odkaz_id = '.$_REQUEST['odkaz_id'].'
				');
			}
			if(mysql_error()){echo mysql_error();} else {echo '<span style="color: #bb0000;">Odkaz upravený.</span><br /><br />';$_REQUEST = NULL; $_FILE = NULL;}
		}
	}
	/*********************************************************************************************/
	if($_REQUEST['action']=='delete'){
		psw_mysql_query($sql = 'DELETE FROM odkaz WHERE odkaz_id = '.$_REQUEST['odkaz_id']);
		if(mysql_error()){echo mysql_error();} else {
			echo '<span style="color: #bb0000;">Odkaz vymazaný</span><br /><br />';
		}
	}
}
/*********************************************************************************************/
if($_REQUEST['state']!='detail'){
	echo '
<br />
<form action="' .$_SERVER['PHP_SELF']. '?file=admin_odkazy.php" method="post" enctype="multipart/form-data">
<table>
 <tr>
  <td>
   <b>Ikona:<br />(približne 90px x 30px):</b>
  </td>
  <td>
   <input type="file" name="foto" size="63" />
  </td>
 </tr> 
 <tr>
  <td>
   <b>Alternatívny text:</b>
  </td>
  <td>
   <input type="text" name="_alt" value="' .validateForm($_REQUEST['_alt']). '" size="80" />
  </td>
 </tr> 
 <tr>
  <td>
   <b>Link:</b>
  </td>
  <td>
   <input type="text" name="_link" value="' .validateForm($_REQUEST['_link']). '" size="80" />
  </td>
 </tr>
 <tr>
  <td colspan="2">
   <input type="submit" value="Vlož odkaz" />
  </td>
 </tr>
<input type="hidden" name="action" value="insert" />
<table>
</form>
<br /><br />
';

	//getall rows
	$odkazy = getTableRows('odkaz','','odkaz_id ASC');
	echo '<table class="data_table">';
	echo ' <tr class="even">
          <td><b>Obázok</b></td>
          <td><b>Alternatívny text</b></td>
          <td><b>Link</b></td>
         </tr>';
	$even = false;
	foreach($odkazy as $key => $odkaz){
		echo '<tr'.($even?' class="even"':'').'>';
		echo '<td>'.($odkaz['odkaz_id']).': <a href="'.$_SERVER['PHP_SELF'].'?file=admin_odkazy.php&state=detail&odkaz_id='.$odkaz['odkaz_id'].'" title="Upraviť"><img src="../image/2/'.$odkaz['odkaz_id'].'.jpg" style="border: 1px solid #666666;" /></a></td>
		      <td>'.$odkaz['alt'].'</td>
		      <td>'.$odkaz['link'].'</td>';
		echo '</tr>';
		if($even){$even=false;}else{$even=true;}
	}
	echo '</table>';



} else {

	//load
	$data = getTableRow('odkaz', 'odkaz_id', $_REQUEST['odkaz_id']);
	$data = $data[0];

	echo '
<br />
<form action="' .$_SERVER['PHP_SELF']. '?file=admin_odkazy.php" method="post" enctype="multipart/form-data">
<table>
 <tr>
  <td>
   <b>Ikona:<br />(približne 90px x 30px):</b>
  </td>
  <td>
   Ak žiadny súbor nevyberieš, ostane pôvodný, prípadne žiadny.<br />
   <input type="file" name="foto" size="63" /><br />   
   '.($data['image']?'<img src="../image/2/'.$_REQUEST['odkaz_id'].'.jpg" style="border: 1px solid #666666;" /><br /><br />':'<b>Žiadna ikona</b><br /><br />').'
  </td>
 </tr> 
 <tr>
  <td>
   <b>Alternatívny text:</b>
  </td>
  <td>
   <input type="text" name="_alt" value="' .validateForm($data['alt']). '" size="80" />
  </td>
 </tr> 
 <tr>
  <td>
   <b>Link:</b>
  </td>
  <td>
   <input type="text" name="_link" value="' .validateForm($data['link']). '" size="80" />
  </td>
 </tr>
 <tr>
  <td colspan="2">
   <input type="submit" value="Upraviť odkaz" />
  </td>
 </tr>
<input type="hidden" name="odkaz_id" value="'.$_REQUEST['odkaz_id'].'" />
<input type="hidden" name="action" value="update" />
<table>
</form>
<br />
<form action="' .$_SERVER['PHP_SELF']. '?file=admin_odkazy.php" method="post" enctype="multipart/form-data">
 <input type="submit" value="Vymazať odkaz" onClick="if(!confirm(\'Ste si istý, že chcete zmazať odkaz?\')){return false;}" />
 <input type="hidden" name="odkaz_id" value="' .$_REQUEST['odkaz_id']. '" />
 <input type="hidden" name="action" value="delete" />
</form> 
<br /><br />';
}
?>