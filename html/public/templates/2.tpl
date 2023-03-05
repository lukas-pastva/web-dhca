<?PHP

$short_story_template = '
 <table onclick="window.open(\''.$path.'clanok/'. $story['clanok_id']. '-'.normalizeClanokName($story['title']).'\', \'_self\');" style="cursor: pointer;" >
  <tr>
   <td valign="bottom" title="[zobrazené ('.$story['counter'].') komentáre ('.$story['comments_num'].')]" 
      style="background-image: url('.$path.'image/1/'.$story['clanok_id'].'.jpg); 
             background-repeat: no-repeat; 
             width: 440px;
             height: 80px;
             border: 1px solid #888888;
             font-size: 9px;
             color: #ffffff;
             font-weight: bold;
             text-align: right;
             vertival-align: bottom;">
   </td>
  </tr>
 </table>
';


$big_story_template = '
 <table width="100%">
   <td style="border-bottom 1px solid #000000; text-align:justify; width: 100%;">
    <img style="margin: 0 1px 0 0; border:1px solid #999999;" alt="" align="left" src="'.$path.'image/1/'.$story['clanok_id'].'.jpg" />
     '.$story['big_story'].'
     '.(DIGG!=''?'<br /><br />pridať článok na <a href="'.DIGG.'" target="_blank"><b>digg</b></a>':'').'
   </td>
  </tr>
 </table>
 <br />
';

?>