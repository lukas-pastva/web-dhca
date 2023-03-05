<?php
/*********************************************************************************************/
if(! userGetAccess($_SESSION['meno_uzivatela'], "sekcie") ){
	header("location: index.php");
	die;
}
/*********************************************************************************************/
echo '<h3>Sekcie</h3><center>';

/*****************************************************************************************************************/
//Ak sa ide vytvarat nova hlavna sekcia
if ($_GET['x'] == "1"){
	$main_section = strtolower( strip_tags($_POST['main_section'], '') );
	$main_section = str_replace(" " , "-" , $main_section);
	$main_section = str_replace("_" , "-" , $main_section);

	//Ak maju posielane premenne hodnoty.
	if ( ($main_section != "") && $main_section ){

		//Ci uz nahodou dana hlavna sekcia neexistuje
		if ( ! psw_mysql_fetch_array( psw_mysql_query('SELECT section_id FROM section WHERE main_section = "'.$main_section.'" ') ) ) {

			if( ! mkdir("../foto/".$main_section, 0777) ){
				alert("Sekcia sa neda vytvorit!");
				die;
			} else {
				chmod("../foto/".$main_section, 0777);
			}

			//Vlozenie sekcie do DB
			if ( psw_mysql_query('ALTER TABLE `user` ADD `' .$main_section. '` TINYINT NOT NULL DEFAULT "0" ;') ){
				psw_mysql_query('INSERT INTO main_section (main_section) VALUES ("'. $main_section .'")');
				alert("Sekcia uspesne vytvorena");
			}

		} else {
			alert("Takato sekcia uz existuje!!!");
		}

	} else {
		alert("Bud si nezadal nazov sekcie, alebo si zadal nespravnu hodnotu!");
	}
}
/*****************************************************************************************************************/

/*****************************************************************************************************************/
//Ak sa ide mazat nadsekcia
if ($_GET['x'] == "3"){

	$main_section = $_REQUEST['main_section'];
	//Ak v nadsekcii niesu podsekcie
	if ( ! psw_mysql_fetch_array( psw_mysql_query('SELECT * FROM section WHERE main_section = "' .$main_section. '" ') ) ){
		psw_mysql_query('ALTER TABLE user DROP ' .$main_section. ' ');
		psw_mysql_query('DELETE FROM main_section WHERE main_section = "' .$main_section. '" ');
		rmdir("../foto/".$main_section);
	} else {
		alert("Sekcia obsahuje podsekcie, ktore treba najprv vymazat!");
	}
}
/*****************************************************************************************************************/


echo '
<div class="clanok_autor">
	V tejto casti sa daju  vytvarat tzv sekcie(sk8, graffiti...).<br />
</div>
<br /><br />
<div class="admin_msg">
 <form action="index.php?file=admin_main_section.php&amp;x=1" method="post">
	<b>Nova sekcia:</b><br />
  Meno sekcie: <input type="text" name="main_section" size="45">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <input type="submit" value="Vytvor sekciu"></form>
</div>
<br />
<b>Existujuce sekcie:</b><br />';

$query = psw_mysql_query('SELECT * FROM main_section');
while ($fetch = psw_mysql_fetch_array($query)){
	if( userGetAccess($_SESSION['meno_uzivatela'], $fetch['main_section']) ){

		echo '
        <form action="index.php?file=admin_main_section.php&amp;x=3" method="post">
         <div class="admin_msg_left">
          <input type="submit" value="Vymaz sekciu">&nbsp;&nbsp;
          <input type="hidden" name="main_section" value="' .$fetch['main_section']. '">
          Meno sekcie: <b>' .$fetch['main_section']. '</b>
				 </div>
        </form>';
	}
}

echo '<br /></center>'

?>