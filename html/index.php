<?PHP
ob_start();

//cachovani obrazku
/*
 Header("Cache-Control: must-revalidate");
 $offset = 60 * 60 * 8; //8 hodiny v cache, pak znovunacteni do cache
 $ExpStr = "Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
 Header($ExpStr);
 */
include_once("admin/admin_functions.php");
include_once("public/day_values.php");
include_once("public/newcomments.php");
require_once('public/captcha.php');

$path = getPath();

$pathArr = explode('/', $_SERVER['REQUEST_URI']);
$id = explode('-',$pathArr[count($pathArr)-1]);
if(0 < ($id[0])){$_REQUEST['id'] = $id[0];}else{}

//pridanie komentara
if(isset($_REQUEST['action']) && $_REQUEST['action']=='add_comment'){
	if(! isBanned($_SERVER['REMOTE_ADDR'])){
		if($_REQUEST['id']){
			$ver_key = $_REQUEST['ver_key'];
			$ver_sol = $_REQUEST['ver_sol'];
			$captcha = new CAPTCHA();
			if($captcha->check($ver_key, $ver_sol) ){
				if(strlen($_REQUEST['name']) > 0){
					if(strlen($_REQUEST['comment']) > 0){
						if(! (strlen($_REQUEST['comment']) > 4096) ){
							mysql_query($sql = 'INSERT INTO comment(clanok_id, text, nick, mail, ip, datetime) VALUES ("'.$_REQUEST['id'].'", "'.$_REQUEST['comment'].'", "'.$_REQUEST['name'].'", "'.$_REQUEST['mail'].'", "'.$_SERVER['REMOTE_ADDR'].'", "'.date('Y-m-d G:i:s').'" );');
							$_REQUEST['comment'] = null;
							$_REQUEST['name'] = null;
							$_REQUEST['mail'] = null;
							header('Location: '.$path.'index.php?'.$_SERVER['QUERY_STRING'].'');
							die;
						} else {
							$warning = "Text správy musí obsahovať maximálne 4000 znakov.";
						}
					} else {
						$warning = "Vložte text";
					}
				} else {
					$warning = "Vložte meno";
				}
			} else {
				$warning = "Nespravne odpisany kod.";
			}
		}
	} else {
		$reason = isBanned($_SERVER['REMOTE_ADDR']);
		$error = 'Zakázané vkladanie správ z dôvodu: '.$reason[1].'!';
	}
}

if( !isset($_REQUEST['from']) || !0 < ($_REQUEST['from']) ){ $_REQUEST['from'] = 0; }

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="sk">
 <head>
  <meta name="robots" content="index, follow" />
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <meta http-equiv="content-language" content="sk" />
  <meta name="Author" content="pSw rhm, http://www.downhillaction.com" />
  <meta name="description" content="" />
  <meta name="keywords" content="downhill, biking, downhill biking, free ride, 4cross" />
  <meta http-equiv="imagetoolbar" content="no" />
  <meta http-equiv="Cache-control" content="no-cache" />
  <meta http-equiv="Pragma" content="no-cache" />
  <link href=\''.$path.'style/style.css\' rel=\'stylesheet\' type=\'text/css\' />
  <title>'.SITENAME.(isset($_REQUEST['id'])?' - '.getNazovClankuFromId($_REQUEST['id']):' - Downhill | Freeride | Cross Country | Fourcross'). '</title>
  <script type="text/javascript" src="'.$path.'js/js.js"></script>
  <script type="text/javascript">
    var GB_ROOT_DIR = "'.$path.'greybox/";
    //a();b();c();
    function getPath(){
      return \''.$path.'\';
    }
  </script>
  <script type="text/javascript" src="'.$path.'greybox/AJS.js"></script>
  <script type="text/javascript" src="'.$path.'greybox/AJS_fx.js"></script>
  <script type="text/javascript" src="'.$path.'greybox/gb_scripts.js"></script>
  <script type="text/javascript">AC_FL_RunContent = 0;</script>
  <script type="text/javascript" src="'.$path.'js/AC_RunActiveContent.js"></script>
  <script type="text/javascript" src="'.$path.'js/swfobject.js"></script>
		
  <link href="'.$path.'greybox/gb_styles.css" rel="stylesheet" type="text/css" />
 </head>
 <body>';
/*
 if((!$_REQUEST['section'])&&(!$_REQUEST['id'])){
 echo '
 <center>
 <br />
 <a href="' .$_SERVER['PHP_SELF']. '?section=home" target="_self" title="'.SITENAME.'">
 <span style="color: #eeeeee; font-weight: bold;" >Pokračuj na '.SITENAME.'</span><br /><br />
 <img src="pics/site/ikonka.jpg" /></a>
 <br /><br />
 <a style="border-width: 1px;" href="' .$_SERVER['PHP_SELF']. '?id=249" target="_self" title="Počúvaj ma sem vol.2...">
 <img src="http://'.$_SERVER['PHP_ROOT'].'/admin/image.php?i=4&j=41" /></a>
 </center>';
 } else {
 */
echo '
  <div class="container">
   <div class="containerbg">
    <div id="imgheader">
     <div class="logolink"><a href="'.$_SERVER['PHP_SELF'].'"></a></div>
    </div>     
    <div id="topmenu">
		<span id="navhome">
			<a href="'.$path.'sekcia/home"                '.(isSelectedSection('home')?'class="active"':'').      ' target="_self">Home</a>
		</span>
		<span id="navlinks">     
			<a href="'.$path.'sekcia/cyklistika"          '.(isSelectedSection('cyklistika')?'class="active"':'').' target="_self">Cyklistika</a> 
			<a href="'.$path.'sekcia/turistika"           '.(isSelectedSection('turistika')?'class="active"':''). ' target="_self">Turistika</a>
			<a href="'.$path.'sekcia/klub"                '.(isSelectedSection('klub')?'class="active"':'').      ' target="_self">Klub Down Hill Čadca</a>
			<a href="http://www.downhillaction.com/forum/"'.(isSelectedSection('forum')?'class="active"':'').     ' target="blank">Fórum</a>
			<a href="'.$path.'sekcia/kontakt"             '.(isSelectedSection('kontakt')?'class="active"':'').   ' target="_self">Kontakt</a>
		</span>
	</div>
	<br />
    <div class="outer1">
     <div class="polozka">
      <div class="header">
       NAJNOVŠIE ČLÁNKY:
      </div>
	  <br />
      <div class="body">
      ' ,echoNajnovsieClanky(10), '
      </div>
     </div>
	 <br />
     <div class="polozka">
      <div class="header">
       VYHĽADÁVANIE:
      </div>
	  <br />
      <div class="body">
       <form method="post" action="'.$path.'index.php?section=search">
        <input type="text" name="search_text" size="17" value="'.$_REQUEST['search_text'].'" />
        <input type="submit" name="ok" value="ok" /><br /><br />
        <input type="checkbox" name="in_nadpis" checked="checked" /> V nadpisoch<br />
        <input type="checkbox" name="in_clanok" checked="checked" /> V článkoch<br />
        <input type="checkbox" name="in_komentar" checked="checked" /> V komentároch<br />
       </form>
      </div>
     </div>
	 <br />
     <div class="polozka">
      <div class="header">
       PARTNERI:
      </div>
	  <br />
      <div class="body" style="text-align: left; line-height: 3px;">
       ',printOdkazy(),'
      </div>
     </div>
    </div>
    <div class="outer2">';

if(0 < ($_REQUEST['id'])){
	echo '<div class="article" >';
	echoClanok($_REQUEST['id']);
	echo '</div>';
} else {

	// HOME
	if($_REQUEST['section']=='home'){
		echoHomeArticles('1.tpl', $_REQUEST['from'], 10);
	}
	else if ($_REQUEST['section'] == 'cyklistika'){
		echoSectionStories('cyklistika', '1.tpl', $_REQUEST['from'], 10);
	}
	else if ($_REQUEST['section'] == 'turistika'){
		echoSectionStories('turistika', '1.tpl', $_REQUEST['from'], 10);
	}
	else if ($_REQUEST['section'] == 'klub'){
		echoSectionStories('klub', '1.tpl', $_REQUEST['from'], 10);
	}
	/////////HIDDEN//////////
	else if ($_REQUEST['section'] == 'hidden'){
		echoSectionStories('hidden', '1.tpl');
	}
	/////////KONTAKT//////////
	else if ($_REQUEST['section'] == "kontakt"){
		include('public/kontakt.php');
		include('public/send_mail.php');
		echo '
			 </td>
		  </tr>
		 </table>
		</div>
		';
	}
	/////////SEARCH//////////
	else if ($_REQUEST['section'] == "search"){
		printSearching();
	}
	/////////HACKING?//////////
	else {
		echoHomeArticles('1.tpl', 0, 10);
	}
}

echo '
    </div>
    <div class="outer3">
   
    

     <div class="polozka">
      <div class="header">
       NAJNOVŠIE KOMENTÁRE:
      </div>
	  <br />
      <div class="body">
       ',echoNajnovsieKomentare(15),'
      </div>
     </div>
	 <br />
     <div class="polozka">
      <div class="header">
       ANKETA:
      </div>
	  <br />
      <div class="body" id="anketa">
       '; include('public/anketa.php'); echo '
      </div>
     </div>
    </div>
    <div class="footer">
	<ul>
		<li>Down Hill Čadca &copy; 2009</li> |
		<li><a href="'.$path.'sekcia/home"                '.(isSelectedSection('home')?'class="active"':'').      ' target="_self">Home</a></li> | 
		<li><a href="'.$path.'sekcia/cyklistika"          '.(isSelectedSection('cyklistika')?'class="active"':'').' target="_self">Cyklistika</a></li> |
		<li><a href="'.$path.'sekcia/turistika"           '.(isSelectedSection('turistika')?'class="active"':''). ' target="_self">Turistika</a></li> |
		<li><a href="'.$path.'sekcia/klub"                '.(isSelectedSection('klub')?'class="active"':'').      ' target="_self">Klub Down Hill Čadca</a></li> |
		<li><a href="http://www.downhillaction.com/forum/"'.(isSelectedSection('forum')?'class="active"':'').     ' target="blank">Fórum</a></li> |
		<li><a href="'.$path.'sekcia/kontakt"             '.(isSelectedSection('kontakt')?'class="active"':'').   ' target="_self">Kontakt</a></li>
    </ul>
	</div>
   </div>
  </div>';

//}

echo '

  <!-- '.SITENAME.' SITE GENERATET IN '.(microtime(true) - $start_time).' SECONDS -->
 </body>
</html>
';



if( $_REQUEST['warning'] ){
	alert($_REQUEST['warning']);
}
if( isset($warning) ){
	alert($warning);
}
ob_end_flush();
?>