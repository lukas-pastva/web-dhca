<?
//ak sa posle form
if($_REQUEST['action']=='insert'){
  $data = explode('|', $_REQUEST['text']);

  $data2 = array();
  $i = 0;
  $j = 0;
  foreach($data as $dat){
    $data2[$i][$j] = str_ireplace("\\", '', $dat);
    $j ++;
    if($dat==''){$i ++;$j = 0;}
  }


//print_r($data2);
  include('../admin/admin_functions.php');

  if(0 < ($_REQUEST['clanok_id'])){
    foreach($data2 as $d){
      psw_mysql_query($sql = 'INSERT INTO `comment` (
      `clanok_id` ,
      `text` ,
      `nick` ,
      `mail` ,
      `ip` ,
      `datetime`
      )
      VALUES (
      "'.$_REQUEST['clanok_id'].'", "'.$d[4].'", "'.$d[1].'", "'.(($d[2]!='none')?$d[2]:'').'" , "'.$d[3].'", "'.date('Y-m-d G:h:s', $d[0]).'");');
      print_r($sql);echo '<br />';
    }
  }
}

echo '
<form action="'.$_SERVER['PHP_SELF'].'" method="post">
  clanokid:<input type="text" name="clanok_id" value="" /><br />
  text:<input type="text" name="text" value="" /><br />
  <input type="hidden" name="action" value="insert" />
  <input type="submit" />
</form>'




?>