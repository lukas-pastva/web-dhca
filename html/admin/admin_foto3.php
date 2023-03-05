<?php
/*********************************************************************************************/
if (! userGetAccess($_SESSION['meno_uzivatela'], "foto") ) {
	header("location: index.php");
	die;
}

/***************************************************************************************************************/
echo '
<script language="JavaScript">	
	function mysubmit()
	{
	  if(document.getElementById(\'section_id\').value>0){
			var Flash;
			if(document.embeds && document.embeds.length>=1 && navigator.userAgent.indexOf("Safari") == -1)
				Flash = document.getElementById("EmbedFlashFilesUpload");
			else
				Flash = document.getElementById("FlashFilesUpload");
			var FormObj = document.getElementById("myform");
	
			var FormValues = \'\';
			for (var i = 0; i<FormObj.elements.length; i++)
				FormValues += escape(FormObj.elements[i].name) + \'=\' + escape(FormObj.elements[i].value) + ((i!=(FormObj.elements.length-1))?\'&\':\'\');
			Flash.SetVariable("SubmitFlash", FormValues); 
			return false;
		} else {
		  alert(\'Najprv vyber fotoalbum!\');
		  return false;
		}
	}
</script>
<h3>Vlož fotografiu</h3>

 <div class="clanok_autor">
  Pre vloženie fotografií vo formáte jpeg vyber najpev sekciu, potom subory a klikni na tlačítko nahraj súbory.<br />
  Ak chceš upraviť text alebo dátum fotografie, choď cez menu položku Uprav foto.<br />
  Pozor! Fotografie s opakujúcmí sa názvami sa nenahrajú.<br />
  Na jeden krát je možné nahrať maximálne 10 fotografií. (čoskoro opravím)
 </div>
 <center>
 <br />
 <div class="admin_msg_left">
  <form method="POST" onSubmit="return mysubmit();" id="myform" name="myform" action="">  
   <table>
    <tr>
     <td>
      Fotoalbum do ktorého sa fotka vloží:
     </td>
     <td>
      <select id="section_id" name="section_id" size="1" style="width: 300px;">
	     <option></option>';

$query = psw_mysql_query('SELECT main_section FROM section GROUP BY main_section');
while ($fetch = psw_mysql_fetch_array($query) ){
	if( userGetAccess($_SESSION['meno_uzivatela'], $fetch['main_section']) ){
		echo '<optgroup label="' .$fetch['main_section']. '">';
		$query2 = psw_mysql_query('SELECT * FROM section WHERE main_section = "'.$fetch['main_section'].'" ORDER BY sub_section ASC');
		while ($fetch2 = psw_mysql_fetch_array($query2) ){
			$pocet_obrazkov = psw_mysql_fetch_array(psw_mysql_query('SELECT count(*) AS pocet_obrazkov FROM picture WHERE section_id = "' .$fetch2['section_id']. '" '));
			echo '<option value="' .$fetch2['section_id']. '">' .$fetch2['section_name']. ' (' .$pocet_obrazkov['pocet_obrazkov']. ')</option>';
		}
		echo '</optgroup>';
	}
}

echo '</select>
     </td>
    </tr>
    <tr>
     <td>&nbsp;
     </td>
    </tr>
    <tr>
    <tr>
     <td>
      Povoliť resizovanie fotografii (meniť ich rozmery)
     </td>
     <td>
      <input type="checkbox" name="resize" checked="checked" onMouseUp="if(!this.checked){document.getElementById(\'width_1\').disabled=false;document.getElementById(\'width_2\').disabled=false;this.value=\'on\';}else{document.getElementById(\'width_1\').disabled=true;document.getElementById(\'width_2\').disabled=true;this.value=\'off\';}" />
     </td>
    </tr>
    <tr>
     <td>
      Maximálna šírka fotografie orientovanej na výšku:
     </td>
     <td>
      <input id="width_1" type="text" name="width_1" value="'.BIG_PICTURE_WIDTH.'" size="5" />
     </td>
    </tr>
    <tr>
     <td>
      Maximálna šírka fotografie orientovanej na šírku:
     </td>
     <td>
      <input id="width_2" type="text" name="width_2" value="'.BIG_PICTURE_WIDTH_2.'" size="5" />
     </td>
    </tr>
    <tr>
     <td>&nbsp;
     </td>
    </tr>
   </table>  
   <OBJECT id="FlashFilesUpload" codeBase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="650" height="300" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" VIEWASTEXT>
	 <PARAM NAME="FlashVars" VALUE="uploadUrl=admin_upload_images.php
			&backgroundColor=#cccccc
			&listBackgroundColor=#cccccc
			&fileTypes=Obrázky vo formáte jpeg|*.jpg
			&uploadButtonVisible=No
			&labelUploadText=Vyber súbory pre upload
			&uploadButtonText=Nahraj
			&browseButtonText=Prechádzať...
			&removeButtonText=Vymaž súbor
			&clearListButtonText=Vyčisti zoznam
			&progressMainText=<PERCENT>%25 Nahrané(<FILESNUM> files)<PART2DIV><BR>Transfer rate: <RATEVALUE>/sec<BR>Zostáva času: <LEFTMIN> min <LEFTSEC> sek
			&progressUploadCompleteText=Upload kompletný
			&progressUploadingText=Uploaduje sa...
			&progressUploadCanceledText=Zrušený upload daľších súborov, Moment...
			&progressUploadStoppedText=Upload zastavený
			&cancelButtonText=Zrušiť
			&totalSizeText=Celková veľkosť <SIZE>
			&fileSizeExceedMessage=Iba súbory o maximálnej veľkosti <MAXFILESIZE> Kb sú povolené. <COUNTINVALIDFILES> files were ignored!
			&fileSizeTotalExceedMessage=Maximálna veľkosť všetkých súborov musí byť <MAXFILESIZETOTAL> Kb. <COUNTINVALIDFILES> súbory boli ignorované!
			&filesCountExceedMessage=Iba <MAXFILECOUNT> súbory sú povolené na upload! <COUNTINVALIDFILES> súbory boli ignorované!
			&zeroSizeMessage=<COUNTINVALIDFILES> súbory s nulovou veľkosťou boli ignorované!
			&fileTypeWrongMessage=Iba súbory nasledujúcich typov : <FILETYPES> sú povolené na upload! <COUNTINVALIDFILES> súbory boli ignorované!
			&retryDialogCaption=Opakovať upload?
			&retryDialogMessage=Niektoré súbory neboli nahrané, chcete ich nahrať znovu?
			&retryDialogYesLabel=Áno
			&retryDialogNoLabel=Nie
			&sortAscLabel=Zoradiť ASC
			&sortByNameLabel=Zoradiť podľa mena
			&sortBySizeLabel=Zoradiť podľa veľkosti
			&sortByDateLabel=Zoradiť podľa dátumu
			&clearListButtonX=545
			&filesListWidth=645
			&filesListHeight=180
			&uploadButtonY=255
			&progressBarWidth=528
			&progressBarY=245
			&labelInfoY=250">
 	 <PARAM NAME="BGColor" VALUE="#cccccc">
	 <PARAM NAME="Movie" VALUE="uploader.swf">
	 <PARAM NAME="Src" VALUE="uploader.swf">
	 <PARAM NAME="WMode" VALUE="Window">
	 <PARAM NAME="Play" VALUE="-1">
	 <PARAM NAME="Loop" VALUE="-1">
	 <PARAM NAME="Quality" VALUE="High">
	 <PARAM NAME="SAlign" VALUE="">
	 <PARAM NAME="Menu" VALUE="-1">
	 <PARAM NAME="Base" VALUE="">
	 <PARAM NAME="AllowScriptAccess" VALUE="always">
	 <PARAM NAME="Scale" VALUE="ShowAll">
	 <PARAM NAME="DeviceFont" VALUE="0">
	 <PARAM NAME="EmbedMovie" VALUE="0">
	 <PARAM NAME="SWRemote" VALUE="">
	 <PARAM NAME="MovieData" VALUE="">
	 <PARAM NAME="SeamlessTabbing" VALUE="1">
	 <PARAM NAME="Profile" VALUE="0">
	 <PARAM NAME="ProfileAddress" VALUE="">
	 <PARAM NAME="ProfilePort" VALUE="0">

	 <embed bgcolor="#cccccc" id="EmbedFlashFilesUpload" src="uploader.swf" quality="high" pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash"	type="application/x-shockwave-flash" width="650" height="300"	flashvars="uploadUrl=admin_upload_images.php
		&backgroundColor=#cccccc
		&listBackgroundColor=#cccccc
		&fileTypes=Obrázky vo formáte jpeg|*.jpg
		&uploadButtonVisible=No
		&labelUploadText=Vyber súbory pre upload
		&uploadButtonText=Nahraj
		&browseButtonText=Prechádzať...
		&removeButtonText=Vymaž súbor
		&clearListButtonText=Vyčisti zoznam
		&progressMainText=<PERCENT>%25 Nahrané(<FILESNUM> files)<PART2DIV><BR>Transfer rate: <RATEVALUE>/sec<BR>Zostáva času: <LEFTMIN> min <LEFTSEC> sek
		&progressUploadCompleteText=Upload kompletný
		&progressUploadingText=Uploaduje sa...
		&progressUploadCanceledText=Zrušený upload daľších súborov, Moment...
		&progressUploadStoppedText=Upload zastavený
		&cancelButtonText=Zrušiť
		&totalSizeText=Celková veľkosť <SIZE>
		&fileSizeExceedMessage=Iba súbory o maximálnej veľkosti <MAXFILESIZE> Kb sú povolené. <COUNTINVALIDFILES> files were ignored!
		&fileSizeTotalExceedMessage=Maximálna veľkosť všetkých súborov musí byť <MAXFILESIZETOTAL> Kb. <COUNTINVALIDFILES> súbory boli ignorované!
		&filesCountExceedMessage=Iba <MAXFILECOUNT> súbory sú povolené na upload! <COUNTINVALIDFILES> súbory boli ignorované!
		&zeroSizeMessage=<COUNTINVALIDFILES> súbory s nulovou veľkosťou boli ignorované!
		&fileTypeWrongMessage=Iba súbory nasledujúcich typov : <FILETYPES> sú povolené na upload! <COUNTINVALIDFILES> súbory boli ignorované!
		&retryDialogCaption=Opakovať upload?
		&retryDialogMessage=Niektoré súbory neboli nahrané, chcete ich nahrať znovu?
		&retryDialogYesLabel=Áno
		&retryDialogNoLabel=Nie
		&sortAscLabel=Zoradiť ASC
		&sortByNameLabel=Zoradiť podľa mena
		&sortBySizeLabel=Zoradiť podľa veľkosti
		&sortByDateLabel=Zoradiť podľa dátumu
		&clearListButtonX=545
		&filesListWidth=645
		&filesListHeight=180
		&uploadButtonY=255
		&progressBarWidth=528
		&progressBarY=245
		&labelInfoY=250">
	 </embed>
   </OBJECT>
 
   <input name="submitbtn" onClick="mysubmit();" type="submit" value="Nahraj súbory do fotoalbumu" >
  </form>    
 </div>
</center>';
