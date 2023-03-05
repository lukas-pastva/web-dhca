<?php
/*********************************************************************************************/
if (! userGetAccess($_SESSION['meno_uzivatela'], "clanok") ) {
	header("location: index.php");
	die;
}
/*********************************************************************************************/

echo '
<h3>Správa článkov</h3>
    <div class="clanok_autor">
     V tejto časti môžeš vkladať články do sekcií ku ktorým máš prístup.
    </div><br />
';

/*********************************************************************************************/
if(echoErrors($_REQUEST)){
	echo echoErrors($_REQUEST);
	// vlozit clanok
} else {
	if($_REQUEST['action']=='insert'){

		//nevlozi usera ak enma prava pri uzivateloch
		if(! userGetAccess($_SESSION['meno_uzivatela'], "uzivatelia")){
			$_REQUEST['_author'] = $_SESSION['meno_uzivatela'];
		}

		if($_FILES['foto']['size'] > 307200){
			echo '<span style="color: #bb0000;">Súbor nesmie byť väčší ako 300kb.</span><br />';
		} else {
			if($_FILES['foto']['tmp_name']){
				$takeFile = fopen($_FILES['foto']['tmp_name'], "r");
				$file = fread($takeFile, filesize($_FILES['foto']['tmp_name']));
				fclose($takeFile);
				$uploadedImage = chunk_split(base64_encode($file));
				psw_mysql_query($sql='
				INSERT INTO clanok 
				(main_section_id, nazov, short_text, big_text, section_id, datetime, user, comments, home, image) 
				VALUES 
				('.$_REQUEST['_sekcia'].', "'.($_REQUEST['_nazov']).'", "'.($_REQUEST['kratky_text']).'", "'.($_REQUEST['big_text']).'", "'.$_REQUEST['section_id'].'", "'.$_REQUEST['_datetime'].'", "'.($_REQUEST['_author']).'", '.($_REQUEST['_comments']=='on'?'1':'0').', '.($_REQUEST['home']=='on'?'1':'0').', "'.$uploadedImage.'") ');
				if(mysql_error()){echo mysql_error();} else {$_REQUEST = null; echo '<span style="color: #bb0000;">Článok úspešne pridaný.</span><br />';}
			} else {
				psw_mysql_query($sql='
				INSERT INTO clanok 
				(main_section_id, nazov, short_text, big_text, section_id, datetime, user, comments, home) 
				VALUES 
				('.$_REQUEST['_sekcia'].', "'.($_REQUEST['_nazov']).'", "'.($_REQUEST['kratky_text']).'", "'.($_REQUEST['big_text']).'", "'.$_REQUEST['section_id'].'", "'.$_REQUEST['_datetime'].'", "'.($_REQUEST['_author']).'", '.($_REQUEST['_comments']=='on'?'1':'0').', '.($_REQUEST['home']=='on'?'1':'0').') ');
				if(mysql_error()){echo mysql_error();} else {$_REQUEST = null; echo '<span style="color: #bb0000;">Článok úspešne pridaný.</span><br />';}
			}
			//debug($sql);
		}
	}
}
/*********************************************************************************************/

echo '
<br />
<form action="' .$_SERVER['PHP_SELF']. '?file=admin_clanok_insert.php" method="post" enctype="multipart/form-data">
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
	echo '<option value="' .$section['id']. '" '.($_REQUEST['_sekcia']['id']==$section['id']?' selected="selected" ':'').'>' .$section['name']. '</option>';
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
   <input type="text" name="_nazov" value="' .validateForm($_REQUEST['_nazov']). '" size="80" />
  </td>
 </tr> 
 <tr>
  <td>
   <b>Fotografia článku<br />(šírka 100px):</b>
  </td>
  <td>
   <input type="file" name="foto" size="63" />
  </td>
 </tr> 
 <tr>
  <td>
   <b>Krátky text:</b><br />
   <span style="cursor: pointer;" onClick="tinyMCE.init({mode : \'textareas\'});">Zapnúť zjednodušené<br /> vkladanie textu - TinyMCE</span>
  </td>
  <td>
   <table><tr>
    <td>
     <textarea id="kratky_text" name="kratky_text" cols="70" rows="8">' .validateForm($_REQUEST['kratky_text']). '</textarea>
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
     <textarea id="_big_text" name="big_text" cols="70" rows="15">' .validateForm($_REQUEST['big_text']). '</textarea>
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
   ', printFotoAlbum(), '
  </td>
 </tr>
 <tr>
  <td>
   <b>Dátum</b>(RRRR-MM-DD HH:MM:SS):
  </td>
  <td>
   <input type="text" name="_datetime" value="' .($_REQUEST['_datetime']?$_REQUEST['_datetime']:date('Y-m-d G:i:s')). '" size="30" />
  </td>
 </tr>
 <tr>
  <td>
   <b>Autor:</b>
  </td>
  <td>';
if(userGetAccess($_SESSION['meno_uzivatela'], "uzivatelia")){
	echo '<input type="text" name="_author" value="' .($_REQUEST['_author']?$_REQUEST['_author']:$_SESSION['meno_uzivatela']). '" size="30" />';
} else {
	echo '<b>'.$_SESSION['meno_uzivatela'].'</b>';
}

echo '
  </td>
 </tr>
 <tr>
  <td>
   <b>Povoliť komentáre:</b>
  </td>
  <td>
   <input type="checkbox" name="_comments" '.($_REQUEST['_comments']?($_REQUEST['_comments']=='on'?'checked="checked"':''):'checked="checked"').' />
  </td>
 </tr>
 <tr>
  <td>
   <b>Zobraziť na Home:</b>
  </td>
  <td>
   <input type="checkbox" name="home" '.($_REQUEST['home']=='on'?'checked="checked"':'').' />
  </td>
 </tr>
 <tr>
  <td colspan="2">
   <input type="submit" value="Vlož článok" />
  </td>
 </tr>
<input type="hidden" name="action" value="insert" />
<table>
</form>
<br /><br />
';

?>