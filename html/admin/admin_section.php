<?php
/*********************************************************************************************/
if (! userGetAccess($_SESSION['meno_uzivatela'], "foto") ) {
	header("location: index.php");
	die;
}
/*********************************************************************************************/
echo '<h3>Spáva fotoalbumov</h3><center>';
/*****************************************************************************************************************/

//Ak sa ide vkladat nova podsekcia
if ($_GET['x'] == "2"){
	$main_section = strtolower( strip_tags($_POST['main_section'], '') );
	$sub_section  = normalizeFilename($_POST['name']);
	$name         = strip_tags($_POST['name'],  '');

	//Ak maju posielane premenne hodnoty.
	if ( ($main_section != "") && ($sub_section != "") && ($name != "") && $main_section && $sub_section && $name ){
		//Ci uz nahodou dana sekcia neexistuje
		if ( ! psw_mysql_fetch_array( psw_mysql_query('SELECT section_id FROM section WHERE sub_section = "'.$sub_section.'" ') ) ) {
			//Vytvori sa sub proecinok
			if( ! mkdir("../foto/".$main_section."/".$sub_section, 0777) ){
				alert("Sekcia sa neda vytvorit!");
				die;
			} else {
				chmod("../foto/".$main_section."/".$sub_section, 0777);
			}

			//Vytvori sa thumb priecinok
			if( ! mkdir("../foto/".$main_section."/".$sub_section."/thumbs", 0777) ){
				alert("Sekcia sa neda vytvorit!");
				die;
			} else {
				chmod("../foto/".$main_section."/".$sub_section."/thumbs", 0777);
			}

			//Vlozenie sekcie do DB
			if ( psw_mysql_query('INSERT INTO section (main_section, sub_section, section_name) VALUES ("'.$main_section.'", "'.$sub_section.'", "' .$name. '")') ){
				alert("Fotoalbum uspesne vytvoreny");
			}
		} else {
			alert("Fotoalbum s rovnakým názvom už existuje (Ak si si istý že nie, kontaktuj pastwoša)!");
		}
	} else {
		alert("Bud si nezadal nazov fotoalbumu, alebo si zadal nespravnu hodnotu!");
	}
}
/*****************************************************************************************************************/
//Ak sa ide mazat podsekcia
if ($_GET['x'] == "4"){

	$secion_id    = $_REQUEST['section_id'];
	$main_section = $_REQUEST['main_section'];
	$sub_section  = $_REQUEST['sub_section'];

	//Ak v sekcii niesu ziadne obrazky
	if ( ! psw_mysql_fetch_array( psw_mysql_query('SELECT * FROM picture WHERE section_id = "' .$secion_id. '" ') ) ){

		psw_mysql_query('DELETE FROM section WHERE section_id = "'. $secion_id .'" ');
		rmdir("../foto/".$main_section."/".$sub_section."/thumbs");
		rmdir("../foto/".$main_section."/".$sub_section);

	} else {
		alert("V danej sekcii su fotky, ktore musis najprv vymazat!");
	}
}
/*****************************************************************************************************************/
//Ak sa ide upravovat podsekcia
if ($_GET['x'] == "6"){

	$secion_id    = $_REQUEST['section_id'];
	$section_name = $_REQUEST['section_name'];
	$main_section = $_REQUEST['main_section'];
	$old_main_section = $_REQUEST['old_main_section'];

	if(strlen($section_name)>1){
		//ak sa zmenil nazov sekcie
		if($old_main_section != $main_section){
			//zistim si meno adresara albumu
			$section_dir_name = psw_mysql_fetch_array(psw_mysql_query($sql = 'SELECT sub_section FROM section WHERE section_id = "'.$secion_id.'" '));
			$section_dir_name = $section_dir_name['sub_section'];
			
			mkdir($d = '../foto/'.$main_section.'/'.$section_dir_name.'/', 0777);
			mkdir('../foto/'.$main_section.'/'.$section_dir_name.'/thumbs/', 0777);
			movedir('../foto/'.$old_main_section.'/'.$section_dir_name.'/thumbs/', '../foto/'.$main_section.'/'.$section_dir_name.'/thumbs/');
			movedir('../foto/'.$old_main_section.'/'.$section_dir_name.'/', '../foto/'.$main_section.'/'.$section_dir_name.'/');
			rmdir('../foto/'.$old_main_section.'/'.$section_dir_name.'/thumbs/');
			rmdir('../foto/'.$old_main_section.'/'.$section_dir_name.'/');
			
		}		
		if(!psw_mysql_query($sql = 'UPDATE section SET section_name = "'.$section_name.'", main_section = "'.$main_section.'"  WHERE section_id = '.$secion_id.'  ')){
			alert(mysql_error());
		}
	} else {
		alert("Vlož aspom jeden znak.");
	}
}
/*****************************************************************************************************************/

echo '
  <div class="clanok_autor">
    V tejto casti sa daju vytvarat tzv. "fotoalbumy". Jedna sa o fotoalbumy, v ktoroých sa nachadzaju fotografie.<br />
  </div>
  <br /><br />
  <div class="admin_msg_left">
   <form action="index.php?file=admin_section.php&amp;x=2" method="post">
    <b>Nový fotoalbum:</b><br />
    Vyber sekciu:  <br />  
    <select name="main_section" size="1">
     <option value=""></option>';

$query = psw_mysql_query('SELECT * FROM main_section ORDER BY main_section');
while ($fetch = psw_mysql_fetch_array($query) ){
	if( userGetAccess($_SESSION['meno_uzivatela'], $fetch['main_section']) ){
		echo '<option value="' .$fetch['main_section']. '">' .$fetch['main_section']. '</option>';
	}
}

echo '
    </select>
    <br />
    Zadaj meno fotoalbumu:<br /><input type="text" size="55" name="name">&nbsp;
    <br /><br /><input type="submit" value="Vytvor fotoalbum">
   </form>
  </div>
  <br /><b>Existujuce fotoalbumy:</b><br />';

$query = psw_mysql_query('SELECT * FROM section ORDER BY section_id DESC');
while ($fetch = psw_mysql_fetch_array($query)){
	if( userGetAccess($_SESSION['meno_uzivatela'], $fetch['main_section']) ){
		if(($_REQUEST['section_id'] == $fetch['section_id'])&&($_REQUEST['x'] == '5')){
			echo '
			  <a name="id' .$fetch['section_id']. '"></a>
				<div class="admin_msg_left">
				 <form action="index.php?file=admin_section.php&amp;x=6" method="post">
				  ID podsekcie: <b>' .$fetch['section_id']. '</b><br /><br />
				  Meno fotoalbumu: <input  type="text" value="' .$fetch['section_name']. '" name="section_name" size="114" /><br /><br />
				  Sekcia: 
    <select name="main_section" size="1">
     <option value=""></option>';

			$query2 = psw_mysql_query('SELECT * FROM main_section ORDER BY main_section');
			while ($fetch2 = psw_mysql_fetch_array($query2) ){
				if( userGetAccess($_SESSION['meno_uzivatela'], $fetch2['main_section']) ){
					//debug($fetch);
					echo '<option '.($fetch['main_section']==$fetch2['main_section']?'selected="selected" ':'').'value="' .$fetch2['main_section']. '">' .$fetch2['main_section']. '</option>';
				}
			}

			echo '
		    </select>
		    <input type="hidden" name="old_main_section" value="'.$fetch['main_section'].'" />
				  <br />
				  <input type="hidden" name="section_id" value="' .$fetch['section_id']. '" /><br />
			    <input type="submit"	value="Uloz zmeny" />
				 </form>
				 <br />
				 <form action="index.php?file=admin_section.php&amp;x=4" method="post">
				  <input type="hidden" name="section_id" value="' .$fetch['section_id']. '" />
		      <input type="hidden" name="main_section" value="' .$fetch['main_section']. '" />
		      <input type="hidden" name="sub_section"	value="' .$fetch['sub_section']. '" />
			    <input type="submit" value="Vymaz fotoalbum" onClick="if(confirm(\'Skutočne chcete zmazať fotoalbum?\')){return true;}else{return false;}" />
				 </form>
				</div>';
		} else {
			echo '
			  <div class="admin_msg_left">
				 <form action="index.php?file=admin_section.php&amp;x=5#id' .$fetch['section_id']. '" method="post">
				  ID fotoalbumu: <b>' .$fetch['section_id']. '</b><br />
				  Meno sekcie: <b>' .$fetch['main_section']. '</b><br />
				  Meno fotoalbumu: <b>' .$fetch['section_name']. '</b><br />
				  <input type="hidden" name="section_id" value="' .$fetch['section_id']. '" />
			    <input type="submit"	value="Uprav fotoalbum" />
				 </form>
				</div>';
		}
	}
}

echo '</center>';

?>