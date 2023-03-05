<?PHP

$short_story_template = '
 <table class="short_story_table" onclick="window.open(\''.$path.'clanok/'. $story['clanok_id']. '-'.normalizeClanokName($story['title']).'\', \'_self\');" onmouseover="this.style.background=\'#f7f6f4\';" onmouseout="this.style.background=\'#ffffff\';" cellspacing="0" cellpadding="0">
  <tr>
   <td class="story_head">
    <h3>'.$story['title'].'</h3>
   </td>
  </tr>
  <tr>
   <td class="story_body">
    <img style="border:1px solid #999999;" alt="" align="left" src="'.$path.'image/1/'.$story['clanok_id'].'.jpg" />
    '.$story['short_story'].'
   </td>
  </tr>
  <tr>
   <td class="story_foot">
    <div style="float:left; font-size: 9px; ">
     '.mb_substr($story['date'],0, 10).',  <b> '.$story['author'].'</b> 
    </div>
    <div style="float:right; font-size: 9px;">
     [zobrazené ('.$story['counter'].') <a href="'.$path.'clanok/'. $story['clanok_id']. '-'.normalizeClanokName($story['title']).'#bott" target="_self" title="Zobrazenie komentárov">komentáre ('.$story['comments_num'].')</a>]<br />
    </div>
   </td>
  </tr>
 </table>
';


$big_story_template = '
 <table class="big_story_table" cellspacing="0" cellpadding="0">
  <tr>
   <td class="story_head">
    <h3>'.$story['title'].'</h3>
   </td>
  </tr>
  <tr>
   <td class="story_body">
    <img style="border:1px solid #999999;" alt="" align="left" src="'.$path.'image/1/'.$story['clanok_id'].'.jpg" />
    '.$story['big_story'].'
    '.(DIGG!=''?'<br /><br />pridať článok na <a href="'.DIGG.'" target="_blank"><b>digg</b></a>':'').'
   </td>
  </tr>
  <tr>
   <td class="story_foot">
    '.mb_substr($story['date'],0, 10).',  <b> '.$story['author'].'</b> 
   </td>
  </tr>
 </table>
 <br />
';


?>