<?php
/*********************************************************************************************/
if (! userGetAccess($_SESSION['meno_uzivatela'], "playlists") ) {
	header("location: index.php");
	die;
}
/*********************************************************************************************/
echo '
<h3>Audio Playlist</h3>
<center>';


/*****************************************************************************************************************/
//Ak sa ide editovat sprava
if ($_GET['x'] == "1"){
	$id    = strip_tags($_POST['id']   , '');
	$href  = $_POST['href'];
	$text  = $_POST['text'];
	$ord  = $_POST['ord'];

	if (! psw_mysql_query('UPDATE audio_playlist SET href="'.$href.'", text="'.$text.'", ord="'.$ord.'" WHERE id = "'.$id.'" ') ){
		alert(mysql_error());
	}
	regenerateAudioPlaylist();
}
/*****************************************************************************************************************/

/*****************************************************************************************************************/
//Ak sa ide mazat sprava
if ($_GET['x'] == "2"){
	$id = strip_tags($_POST['id'], '');
	psw_mysql_query('DELETE FROM audio_playlist WHERE id = "'.$id.'" ');
	regenerateAudioPlaylist();
}
/*****************************************************************************************************************/

/*****************************************************************************************************************/
//Ak sa ide vkladat sprava
if ($_GET['x'] == "3"){
	$href  = $_POST['href'];
	$text  = $_POST['text'];
	$ordt  = $_POST['ord'];

	if ( $href && $text ){
		psw_mysql_query('INSERT INTO audio_playlist ( ord, href, text ) VALUES ('.$ord.', "'.$href.'", "'.$text.'")');
	}
	regenerateAudioPlaylist();
}
/*****************************************************************************************************************/


if (! $_GET['limit']) {
	$limit = PLAYLIST_POCET_NA_STRANU;
} else {
	$limit = $_GET['limit'];
}

$all_text=psw_mysql_query('SELECT * FROM audio_playlist ORDER BY ord ASC');
$pocet_zaznamov = psw_mysql_fetch_array( psw_mysql_query('SELECT count(*) AS x FROM audio_playlist') );
$pocet_zaznamov = $pocet_zaznamov['x'];

echo '  
  <div class="clanok_autor" style="text-align: left;">
    Sprava playlistu audio prehr??va??a.<br />Upload s??borov a vkladanie do playlistu s?? oddelen?? funkcie.<br />
    Vlo?? n??zov piesne, s diakritikou - tak?? ako sa zobraz?? v sekcii AUDIO.<br />
    Vlo?? cestu k s??boru v tvare mp3/nazov-suboru.mp3 (m????e?? ho skop??rova?? pod tla????tkom "Zoznam/upload mp3" )<br />
    Vlo?? poradie s??boru - piesne sa bud?? zoradzova?? od najmen??ieho po najv??????ie ????slo.<br />
    Zoznam a upload(nahr??vanie) piesn?? n??jde?? pod tla????tkom "Zoznam/Upload mp3".
  </div>
  <br />
  <br /><b>Vlo?? nov?? z??znam</b>
   <div class="admin_msg_left">
    
    <form action="index.php?file=admin_audioplaylist.php&amp;x=3" method="post">
     <b>N??zov piesne:</b><input type="text" name="href" size="130"><br /><br />
     <b>Cesta k s??boru:</b><input type="text" name="text" size="130"><br /><br />
     <b>Poradie:</b>(????sluj po desiatkach 10,20..)   <input type="text" name="ord" size="130"><br /><br />
      <input type="submit" value="Vloz zaznam">
    </form>
   </div>
  <div class="admin_msg_left"><input type="button" value="Zoznam/Upload mp3" onClick="window.open(\'admin_upload_mp3.php\', \'_blank\', \'toolbar=0,location=0,directories=0,status=1,menubar=0,scrollbars=1,resizable=1,width=450 ,height=600,left=10,titlebar=0\');"/></div>
  <br />
  <b>Zoznam piesn??</b>
  ';


/*****************************************************************************************************************/
for ($i = 0; $i < $limit; $i++){
	if ($sprava = psw_mysql_fetch_array($all_text)){
		if ($i >= ($limit - PLAYLIST_POCET_NA_STRANU)){
			if ( $_GET['edit'] == $sprava['id'] ){
				echo '
           <a name="message"></a>
           <div class="admin_msg_left">
            <form action="index.php?file=admin_audioplaylist.php&amp;x=1" method="post">
             <b>N??zov piesne:</b><input type="text" name="href" size="130" value="'.$sprava['href'].'"><br /><br />
             <b>Cesta k s??boru:</b><input type="text" name="text" size="130" value="'.$sprava['text'].'"><br /><br />
             <b>Poradie:</b><input type="text" name="ord" size="130" value="'.$sprava['ord'].'"><br /><br />
             <input type="hidden" name="id" value="'.$sprava['id'].'">
             <input type="submit" value="Uprav zaznam">
            </form>
            <br />
            <form action="index.php?file=admin_audioplaylist.php&amp;x=2" method="post">
             <input type="hidden" name="id" value="'.$sprava['id'].'">
             <input type="submit" value="Vymaz zaznam" onClick="if(confirm(\'Skuto??ne chcete zmaza?? z??znam?\')){return true;}else{return false;}" />
            </form>
           </div>
           <br />';
			} else {
				echo '
           <div class="admin_msg_left">
             <b>N??zov piesne:</b>'.$sprava['href'].'<br />
             <b>Cesta k s??boru:</b>'.$sprava['text'].'<br />
             <b>Poradie:</b>'.$sprava['ord'].'<br />
             <input type="submit" value="Uprav zaznam" onclick="window.open(\'index.php?file=admin_audioplaylist.php&amp;limit='.$actual_limit.'&edit='.$sprava['id'].'#message\', \'_self\');">
           </div>
          ';
			}
		}
	}
}

//Vypis zoznamov stranok
echo '
   <br />
   <div class="admin_msg">
    <div class="clanok_autor">
     <center>';

$actual_limit;
for ($x = PLAYLIST_POCET_NA_STRANU; ($x-PLAYLIST_POCET_NA_STRANU) < $pocet_zaznamov; $x+=PLAYLIST_POCET_NA_STRANU){
	$nr+=1;
	if ( $limit != $x ){
		echo "\r\n\t<a href=\"index.php?file=admin_audioplaylist.php&amp;limit=".$x."\"><b>".$nr."</b></a>";
	} else{
		echo "\r\n\t[".$nr."]";
		$actual_limit = $limit;
	}
}
echo '
     </center>
    </div>
   </div>
   <br />';
/*****************************************************************************************************************/

echo '</center>';



