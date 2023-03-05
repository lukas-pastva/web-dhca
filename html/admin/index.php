<?php
//session_cache_limiter('private');
session_cache_expire(60);
session_start();
session_register('meno_uzivatela');

ob_start();
/*********************************************************************************************/
if ( ! $_SESSION['meno_uzivatela'] ) {
	header("location: admin_login.php");
	die;
}
/*********************************************************************************************/
include_once('admin_functions.php');

echo '
<!doctype html public "-//w3c//dtd html 4.01 transitional//en">
<html>
 <head>
  <link rel="stylesheet" type="text/css" href="admin_style.css">
  <script language="javascript" type="text/javascript" src="./tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
     <script language="javascript" type="text/javascript">
     tinyMCE.init({
       mode : "textareas"
     });
   </script>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <title>A.D.M.I.N. ['.SITENAME.']</title>
 </head>
 <body>
  <div class="sajtah">
   <div class="top"></div>
   <div style="position: absolute; top: 170px; left: 300px; color: white; cursor: pointer; width: 140px;" onclick="window.open(\'../index.php\', \'_self\');"><b>Verejne rozhranie</b></div>
    <div class="menu">';


$data = psw_mysql_fetch_array( psw_mysql_query('SELECT * FROM user WHERE nick = "' .$_SESSION['meno_uzivatela']. '" ') );

echo                                '<a href="index.php?file=admin_main.php"                target="_self"><b>Home</b></a><br /><br />';
echo ($data['clanok'       ] == "1"?'<a href="index.php?file=admin_clanok_insert.php"       target="_self"><b>Vlož článok</b></a><br />':'');
echo ($data['clanok'       ] == "1"?'<a href="index.php?file=admin_clanok_list.php"         target="_self"><b>Uprav článok</b></a><br /><br />':'');

echo ($data['ankety'       ] == "1"?'<a href="index.php?file=admin_ankety.php"              target="_self"><b>Ankety</b></a><br />':'');
echo ($data['zo_sveta'     ] == "1"?'<a href="index.php?file=admin_zo_sveta.php"            target="_self"><b>Zo Sveta</b></a><br />':'');
echo ($data['odkazy'       ] == "1"?'<a href="index.php?file=admin_odkazy.php"              target="_self"><b>Partneri</b></a><br />':'');
echo ($data['partylist'    ] == "1"?'<a href="index.php?file=admin_partylist.php"           target="_self"><b>Party list</b></a><br /><br />':'');

echo ($data['playlists'    ] == "1"?'<a href="index.php?file=admin_audioplaylist.php"       target="_self"><b>Audio playlist</b></a><br />
       															 <a href="index.php?file=admin_videoplaylist.php"       target="_self"><b>Video playlist</b></a><br /><br />':'');
 
echo ($data['pocitadlo'    ] == "1"?'<a href="index.php?file=admin_counter.php"             target="_self"><b>Pocitadlo</b></a><br /><br />':'');
 
echo ($data['sekcie'       ] == "1"?'<a href="index.php?file=admin_main_section.php"        target="_self"><b>Sekcie</b></a><br />':'');
echo ($data['foto'         ] == "1"?'<a href="index.php?file=admin_section.php"             target="_self"><b>Fotoalbumy</b></a><br />':'');
echo ($data['foto'         ] == "1"?'<a href="index.php?file=admin_foto3.php"               target="_self"><b>Vlož foto</b></a><br />':'');
echo ($data['foto'         ] == "1"?'<a href="index.php?file=admin_foto_edit.php"           target="_self"><b>Uprav foto</b></a><br /><br />':'');
 
echo ($data['banlist'      ] == "1"?'<a href="index.php?file=admin_banlist.php"             target="_self"><b>Ban List</b></a><br />':'');
echo ($data['fucklist'     ] == "1"?'<a href="index.php?file=admin_fucklist.php"            target="_self"><b>Fuck List</b></a><br />':'');
echo ($data['mailinglist'  ] == "1"?'<a href="index.php?file=admin_mailinglist.php"         target="_self"><b>Mail List</b></a><br /><br />':'');
 
															 echo '<a href="index.php?file=admin_users.php"               target="_self"><b>Užívateľ</b></a><br /><br />
                                     <a href="https://atmail.dnsserver.eu/"                 target="_blank"><b>E-mail</b></a><br /><br />';

echo ($data['userlogin'    ] == "1"?'<a href="index.php?file=admin_user_login.php"          target="_self"><b>Počítadlo prístupov</b></a><br /><br />':'');
 
															 echo '<a href="index.php?file=admin_forum.php"               target="_self"><b>Forumko</b></a><br /><br />
                                     <a href="index.php?file=admin_logout.php"              target="_self"><b>Odhlásiť sa</b></a><br />';

echo '
    </div>
    <div class="site">';

//Vypis vystraznej hlasky
if($_REQUEST['warning']){
	alert($_REQUEST['warning']);
}

if($_REQUEST['file']){
	include($_REQUEST['file']);
} else {
	include("admin_main.php");
}

echo '
    </div>
  </div>
 </body>
</html>';

ob_end_flush();
?>