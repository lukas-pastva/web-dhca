<?php
/*********************************************************************************************/
$limit;
if(0 < ($_REQUEST['limit'])){$limit=$_REQUEST['limit'];}else{$limit=40;}
$from;
if(0 < ($_REQUEST['from'])){$from=$_REQUEST['from'];}else{$from=0;}

echo '
<h3>Fórumko</h3>
    <div class="clanok_autor">
     Forumko, mozes pisat spravy ktore bude citat uplne kazdy, lepsie ako si pisat na icq a dufat ze to pride ;-)
    </div><br />
';

/*********************************************************************************************/
if(echoErrors($_REQUEST)){
	echo echoErrors($_REQUEST);
	// vlozit clanok
} else {
	if($_REQUEST['action']=='insert'){

		psw_mysql_query($sql='
				INSERT INTO forumko 
				(datetime, nick, text) 
				VALUES 
				(now(), "'.($_SESSION['meno_uzivatela']).'", "'.($_REQUEST['_text']).'") ');
		if(mysql_error()){echo mysql_error();} else {$_REQUEST = null;}
		//debug($sql);
	}
}
/*********************************************************************************************/

echo '
<br />
<form action="' .$_SERVER['PHP_SELF']. '?file=admin_forum.php" method="post" enctype="multipart/form-data">
<table>
 <tr>
  <td>
   <b>text správy</b> (môžeš používať "odEnterovanie")
  </td>
 </tr>
 <tr>
  <td>
   <textarea name="_text" style="width:700px; height:100px;"/></textarea><br />
   <input type="submit" value="Pošli správu" />
  </td>
 </tr>
<input type="hidden" name="action" value="insert" />
<table>
</form>
<br /><br />
';

$comments = getTableRows('forumko', '', 'datetime', $from ,$limit);
echo '<table class="data_table" >';
if(count($comments)>0){
	$even = false; 
	foreach($comments as $key => $comment){
		echo '<tr '.($even?' class="even"':'').'>
		       <td style="vertical-align: top; border: 0; width: 100px;">'.mb_substr($comment['datetime'],0,-3).'</td>
		       <td style="vertical-align: top; border: 0; width: 60px;"><b>'.$comment['nick'].'</b></td>
		       <td style="font-size: 10px; vertical-align: top; border: 0; padding: 3px 0 3px 0">'.wordwrap( str_replace("\n", '<br />', $comment['text']), 97, "\n", 1 ).'</td>
			    </tr>';
		if(!$even){$even=true;}else{$even=false;}
	}
}	else {
	echo '<tr><td>Žiadne správy</td></tr>';
}
echo '</table>';

echoPaging('forumko', '', $from, $limit, 'file=admin_forum.php');

?>