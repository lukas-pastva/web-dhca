<?php
/*********************************************************************************************/
if (! userGetAccess($_SESSION['meno_uzivatela'], "clanok") ) {
	header("location: index.php");
	die;
}
if(!isset($_REQUEST['limit'])){
	$_REQUEST['limit'] = 999;
}

/*********************************************************************************************/

echo '
<h3>Správa článkov</h3>
    <div class="clanok_autor">
     Vyber článok na upravenie, potom uprav potrebné hodnoty.
    </div><br />
';

/*********************************************************************************************/
if(echoErrors($_REQUEST)){
	echo echoErrors($_REQUEST);
	$_REQUEST['state'] = 'detail';
	// vlozit clanok
} else {
	if($_REQUEST['action']=='update'){
		if($_FILES['foto']['size'] > 307200){
			echo '<span style="color: #bb0000;">Súbor nesmie byť väčší ako 300kb.</span><br />';
			$_REQUEST['state'] = 'detail';
		} else {
			if($_FILES['foto']['tmp_name']){
				$takeFile = fopen($_FILES['foto']['tmp_name'], "r");
				$file = fread($takeFile, filesize($_FILES['foto']['tmp_name']));
				fclose($takeFile);
				$uploadedImage = chunk_split(base64_encode($file));
				psw_mysql_query($sql = '
				UPDATE clanok SET 
				 main_section_id = '.$_REQUEST['_sekcia'].',
				 nazov = "'.($_REQUEST['_nazov']).'", 
				 short_text = "'.($_REQUEST['kratky_text']).'", 
				 big_text = "'.($_REQUEST['big_text']).'", 
				 section_id = "'.$_REQUEST['section_id'].'", 
				 datetime = "'.$_REQUEST['_datetime'].'", 
				 '.(userGetAccess($_SESSION['meno_uzivatela'], "uzivatelia")?'user = "'.$_REQUEST['_author'].'",':'').' 
				 comments = '.($_REQUEST['_comments']=='on'?'1':'0').', 
				 home = '.($_REQUEST['home']=='on'?'1':'0').',
				 image = "'.$uploadedImage.'" WHERE clanok_id = '.$_REQUEST['id'].'
				');
			} else {
				psw_mysql_query($sql = '
				UPDATE clanok SET 
				 main_section_id = '.$_REQUEST['_sekcia'].',
				 nazov = "'.($_REQUEST['_nazov']).'", 
				 short_text = "'.($_REQUEST['kratky_text']).'", 
				 big_text = "'.($_REQUEST['big_text']).'", 
				 section_id = "'.$_REQUEST['section_id'].'", 
				 datetime = "'.$_REQUEST['_datetime'].'", 
				 '.(userGetAccess($_SESSION['meno_uzivatela'], "uzivatelia")?'user = "'.$_REQUEST['_author'].'",':'').' 
				 comments = '.($_REQUEST['_comments']=='on'?'1':'0').',
				 home = '.($_REQUEST['home']=='on'?'1':'0').'
          WHERE clanok_id = '.$_REQUEST['id'].'
				');
			}
			if(mysql_error()){echo mysql_error();} else {echo '<span style="color: #bb0000;">Článok úspešne upravený.</span><br /><br />';}
		}
	}
	if($_REQUEST['action']=='delete_comment'){
		if(0 < ($_REQUEST['del_all'])){
			psw_mysql_query($sql = 'DELETE FROM comment WHERE clanok_id = '.$_REQUEST['del_all']);
		} else {
			foreach($_REQUEST['comment_id'] as $key => $comment_id){
				psw_mysql_query($sql = 'DELETE FROM comment WHERE comment_id = '.$key);
			}
		}
		if(mysql_error()){echo mysql_error();} else {
			echo '<span style="color: #bb0000;">Komentár vymazaný</span><br /><br />';
			$_REQUEST['state']='detail';
			$_REQUEST['id']=$_REQUEST['id'];
		}
	}
	if($_REQUEST['action']=='delete'){
		psw_mysql_query($sql = 'DELETE FROM comment WHERE clanok_id = '.$_REQUEST['id']);
		psw_mysql_query($sql = 'DELETE FROM clanok WHERE clanok_id = '.$_REQUEST['id']);
	}
	if(mysql_error()){echo mysql_error();}
}
/*********************************************************************************************/
// LIST
if($_REQUEST['state']!='detail'){

	//getall rows dla prav
	$clanky = getClankyByUser($_SESSION['meno_uzivatela'], $_REQUEST['order_by'], $_REQUEST['from'], $_REQUEST['limit'], $_REQUEST['ordering']);
	echo '<table class="data_table">';
	echo ' <tr class="even">
          <td><a href="'.$_SERVER['PHP_SELF'].'?limit='.$_REQUEST['limit'].'&from='.$_REQUEST['from'].'&file=admin_clanok_list.php&order_by=nazov&ordering=1"><b>Názov</b></a></td>
          <td style="width:120px;"><a href="'.$_SERVER['PHP_SELF'].'?limit='.$_REQUEST['limit'].'&from='.$_REQUEST['from'].'&file=admin_clanok_list.php&order_by=datetime&ordering=1"><b>Dátum</b></a></td>
          <td style="width:100px;"><a href="'.$_SERVER['PHP_SELF'].'?limit='.$_REQUEST['limit'].'&from='.$_REQUEST['from'].'&file=admin_clanok_list.php&order_by=main_section_id&ordering=1"><b>Sekcia</b></a></td>
          <td style="width:62px;"><a href="'.$_SERVER['PHP_SELF'].'?limit='.$_REQUEST['limit'].'&from='.$_REQUEST['from'].'&file=admin_clanok_list.php&order_by=user&ordering=1"><b>Vložil</b></a></td>
          <td style="width:62px;"><b>Komentáre</b></td>
         </tr>';
	$even = false;
	foreach($clanky as $key => $clanok){
		$commentsNr = getCommentsNr($clanok['clanok_id']);

		echo '<tr'.($even?' class="even"':'').'>';
		echo '<td>'.($clanok['clanok_id']).': <a href="'.$_SERVER['PHP_SELF'].'?file=admin_clanok_list.php&state=detail&id='.$clanok['clanok_id'].'" title="Upraviť"><b>'.$clanok['nazov'].'</b></a></td><td>'.$clanok['datetime'].'</td><td>'.getSectionNameFromId($clanok['main_section_id']).'</td><td>'.$clanok['user'].'</td><td align="center">'.$commentsNr.'</td>';
		echo '</tr>';
		if($even){$even=false;}else{$even=true;}
	}
	echo '</table>';

	echoPaging('clanok', '', $_REQUEST['from'], $_REQUEST['limit'], 'file=admin_clanok_list.php&order_by='.$_REQUEST['order_by']);

	// DETAIL
} else {

	//load
	$data = getTableRow('clanok', 'clanok_id', $_REQUEST['id']);
	$data = $data[0];

	echo '
<br />
<form action="' .$_SERVER['PHP_SELF']. '?file=admin_clanok_list.php" method="post" enctype="multipart/form-data">
<table>
 <tr>
  <td>
   <b>Sekcia:</b>
  </td>
  <td>
   ';

	$sections = getSections();
	echo '<select name="_sekcia"><option></option>';
	foreach($sections as $section){
		echo '<option value="' .$section['id']. '" '.($data['main_section_id']==$section['id']?' selected="selected" ':'').'>' .$section['name']. '</option>';
	}
	echo '</select>';

	echo '
  </td>
 </tr> 
 <tr>
  <td>
   <b>Názov článku:</b>
  </td>
  <td>
   <input type="text" name="_nazov" value="' .validateForm($data['nazov']). '" size="80" />
  </td>
 </tr> 
 <tr>
  <td>
   <b>Fotka článku<br />(šírka 100px):</b>
  </td>
  <td>
   <br />
   Ak žiadny súbor nevyberieš, ostane pôvodný.<br />
   <input type="file" name="foto" size="63" /><br />   
   '.($data['image']?'<img src="../image/1/'.$_REQUEST['id'].'.jpg" style="border: 1px solid #666666;" /><br /><br />':'<b>Žiadny obrázok</b><br /><br />').'
  </td>
 </tr> 
 <tr>
  <td>
   <b>Krátky text:</b>
  </td>
  <td>
   <table><tr>
    <td>
     <textarea id="kratky_text" name="kratky_text" cols="70" rows="8">' .validateForm($data['short_text']). '</textarea>
    </td>
    <td align="center">
     <span style="cursor: pointer;" onClick="window.open(\'admin_upload_file.php\', \'_blank\', \'toolbar=0,location=0,directories=0,status=1,menubar=0,scrollbars=1,resizable=1,width=400 ,height=500,left=10,titlebar=0\');"><b>Nahraj súbor</b></span><br /><br /><br />
    <b>Smajlíky:</b><br />
     <script>
	    function insertext(text,area){
	     if(area=="kratky_text"){document.getElementById(\'kratky_text\').focus(); document.getElementById(\'kratky_text\').value=document.getElementById(\'kratky_text\').value +" "+ text; document.getElementById(\'kratky_text\').focus() }
	     if(area=="_big_text")    {document.getElementById(\'_big_text\').focus(); document.getElementById(\'_big_text\').value=document.getElementById(\'_big_text\').value +" "+ text; document.getElementById(\'_big_text\').focus()}
	    }
     </script><a href="javascript:insertext(\':smile:\',\'kratky_text\')"><img style="border: none;" alt="smile" src="../pics/smiles/smile.gif" /></a>&nbsp;<a href="javascript:insertext(\':wink:\',\'kratky_text\')"><img style="border: none;" alt="wink" src="../pics/smiles/wink.gif" /></a>&nbsp;<a href="javascript:insertext(\':wassat:\',\'kratky_text\')"><img style="border: none;" alt="wassat" src="../pics/smiles/wassat.gif" /></a>&nbsp;<a href="javascript:insertext(\':tongue:\',\'kratky_text\')"><img style="border: none;" alt="tongue" src="../pics/smiles/tongue.gif" /></a><br /><a href="javascript:insertext(\':laughing:\',\'kratky_text\')"><img style="border: none;" alt="laughing" src="../pics/smiles/laughing.gif" /></a>&nbsp;<a href="javascript:insertext(\':sad:\',\'kratky_text\')"><img style="border: none;" alt="sad" src="../pics/smiles/sad.gif" /></a>&nbsp;<a href="javascript:insertext(\':angry:\',\'kratky_text\')"><img style="border: none;" alt="angry" src="../pics/smiles/angry.gif" /></a>&nbsp;<a href="javascript:insertext(\':crying:\',\'kratky_text\')"><img style="border: none;" alt="crying" src="../pics/smiles/crying.gif" /></a><br />
    </td>
   </tr></table>
  </td>
 </tr>
 <tr>
  <td>
   <b>Dlhý text:</b>
  </td>
  <td>
   <table><tr>
    <td>
     <textarea id="_big_text" name="big_text" cols="70" rows="15">' .validateForm($data['big_text']). '</textarea>
    </td>
    <td align="center">
     <span style="cursor: pointer;" onClick="window.open(\'admin_upload_file.php\', \'_blank\', \'toolbar=0,location=0,directories=0,status=1,menubar=0,scrollbars=1,resizable=1,width=400 ,height=500,left=10,titlebar=0\');"><b>Nahraj súbor</b></span><br /><br /><br />
     <b>Smajlíky:</b><br />
     <a href="javascript:insertext(\':smile:\',\'_big_text\')"><img style="border: none;" alt="smile" src="../pics/smiles/smile.gif" /></a>&nbsp;<a href="javascript:insertext(\':wink:\',\'_big_text\')"><img style="border: none;" alt="wink" src="../pics/smiles/wink.gif" /></a>&nbsp;<a href="javascript:insertext(\':wassat:\',\'_big_text\')"><img style="border: none;" alt="wassat" src="../pics/smiles/wassat.gif" /></a>&nbsp;<a href="javascript:insertext(\':tongue:\',\'_big_text\')"><img style="border: none;" alt="tongue" src="../pics/smiles/tongue.gif" /></a><br /><a href="javascript:insertext(\':laughing:\',\'_big_text\')"><img style="border: none;" alt="laughing" src="../pics/smiles/laughing.gif" /></a>&nbsp;<a href="javascript:insertext(\':sad:\',\'_big_text\')"><img style="border: none;" alt="sad" src="../pics/smiles/sad.gif" /></a>&nbsp;<a href="javascript:insertext(\':angry:\',\'_big_text\')"><img style="border: none;" alt="angry" src="../pics/smiles/angry.gif" /></a>&nbsp;<a href="javascript:insertext(\':crying:\',\'_big_text\')"><img style="border: none;" alt="crying" src="../pics/smiles/crying.gif" /></a><br />
    </td>
   </tr></table>
  </td>
 </tr>
 <tr>
  <td>
   <b>Fotoalbum:</b>
  </td>
  <td>
   ', printFotoAlbum($data['section_id']), '
  </td>
 </tr>
 <tr>
  <td>
   <b>Dátum</b>(RRRR-MM-DD HH:MM:SS):
  </td>
  <td>
   <input type="text" name="_datetime" value="' .($data['datetime']?$data['datetime']:date('Y-m-d G:i:s')). '" size="30" />
  </td>
 </tr>
 <tr>
  <td>
   <b>Autor:</b>
  </td>
  <td>';
	if(userGetAccess($_SESSION['meno_uzivatela'], "uzivatelia")){
		echo '<input type="text" name="_author" value="' .($data['user']?$data['user']:$_SESSION['meno_uzivatela']). '" size="30" />';
	} else {
		echo '<b>'.$data['user'].'</b>';
	}

	echo '
  </td>
 </tr>
 <tr>
  <td>
   <b>Povoliť komentáre:</b>
  </td>
  <td>
   <input type="checkbox" name="_comments" '.($data['comments']=='1'?'checked="checked"':'').' />
  </td>
 </tr>
 <tr>
  <td>
   <b>Zobraziť na Home:</b>
  </td>
  <td>
   <input type="checkbox" name="home" '.($data['home']=='1'?'checked="checked"':'').' />
  </td>
 </tr>
 <tr>
  <td colspan="2">
   <input type="submit" value="Uložiť článok" />
  </td>
 </tr>
<input type="hidden" name="id" value="'.$_REQUEST['id'].'" />
<input type="hidden" name="action" value="update" />
<table>
</form>
<br />
<form action="' .$_SERVER['PHP_SELF']. '?file=admin_clanok_list.php" method="post" enctype="multipart/form-data">
 <input type="submit" value="Vymazať článok" onClick="if(!confirm(\'Ste si istý, že chcete zmazať celý článok aj s komentármi?\')){return false;}" />
 <input type="hidden" name="id" value="'.$_REQUEST['id'].'" />
 <input type="hidden" name="action" value="delete" />
</form> 
<br /><br />
<hr />
<h3>Komentáre:</h3>
';

	$comments = getCommentsForArticle($_REQUEST['id']);

	echo '
	     <form action="' .$_SERVER['PHP_SELF']. '?file=admin_clanok_list.php" method="post" enctype="multipart/form-data"> 
	      <table class="data_table">
         <tr class="even">
          <td><b>Nick</b></td>
          <td><b>Dátum a čas</b></td>
          <td><b>Mail</b></td>
          <td><b>IP</b></td>
          <td colspan="2"><b>Text</b></td>
         </tr>';
	$even = false;
	if(count($comments)>0){
		foreach($comments as $key => $comment){
			echo '<tr'.($even?' class="even"':'').'>
	        	 <td>'.($key+1).': <span style="cursor: pointer;" onClick="window.open(\'admin_comment.php?id='.$comment['comment_id'].'\', \'_blank\', \'toolbar=0,location=0,directories=0,status=1,menubar=0,scrollbars=1,resizable=1,width=400 ,height=300,left=10,titlebar=0\');" title="Upravi�"><b>'.$comment['nick'].'</b></span></td>
		         <td>'.$comment['datetime'].'</td>
		         <td>'.$comment['mail'].'</td>
		         <td>'.$comment['ip'].'</td>
		         <td title="'.$comment['text'].'">'.mb_substr($comment['text'], 0, 20).'...</td>
		         <td><input style="margin: 0;" type="checkbox" name="comment_id['.$comment['comment_id'].']" value="1" /></td>
			      </tr>';
			if($even){$even=false;}else{$even=true;}
		}
		echo '<tr><td  style="border: none;" colspan="5"></td><td style="border: none;" ><input style="margin: 0;" type="checkbox" name="del_all" value="'.$_REQUEST['id'].'" />&nbsp;<b>Zmazať všetky?</b></td></tr>';
		echo '<tr><td colspan="6"><input type="submit" value="Zmazať označené" onClick="if(!confirm(\'Ste si istý, že chcete zmazať komentár?\')){return false;}" /></td></tr>';
	}	else {
		echo '<tr><td colspan="6">Žiadne komentáre</td></tr>';
	}
	echo '</table>
	      <input type="hidden" name="action" value="delete_comment" />
	      <input type="hidden" name="id" value="'.$_REQUEST['id'].'" />
	     </form>';

}
?>