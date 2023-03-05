<?PHP

$short_story_template = '
 <table onclick="window.open(\''.$path.'clanok/'. $story['clanok_id']. '-'.normalizeClanokName($story['title']).'\', \'_self\');" style="cursor: pointer;" width="100%">
  <tr>
   <td width="100%" valign="bottom"
      style="width: 440px;
             text-align:justify;
             font-size: 9px;">
     '.$story['short_story'].'
   </td>
  </tr>
 </table>
';


$big_story_template = '
<table border="0" style="border-right: 1px groove #999999" width="100%" table-layout:fixed cellspacing="0" cellpadding="0"><td class="short-story-head" >
    <h3>{title}</h3>
</td>
</tr>
<tr>
<td width="100%" style="text-align:justify;">
<font style="font-family: verdana;	color:#000000; font-size:11;">{full-story}</font></td>
</tr>
<tr>
<td width="100%">
<table border="0" width="100%" cellspacing="0">
<tr>
<td width="100%" font style="font-family:verdana; color:#000000; font-size:11;">{date} pridal <b> {author}</b></font><br /> </td><td width="100%" ><div align=right>
<br />
</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
<div align="center"> 
          <strong><a href="javascript:history.go(-1)" onMouseover=highlight(this,'#CCCCCC') onMouseout="highlight(this,document.bgColor)"><font size="2" face="Verdana">[naspä»]</a> 
          </strong></div>
';


?>