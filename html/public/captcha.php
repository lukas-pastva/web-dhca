<?php
/**
 * Copyright (C) 2003-2005 Radek HULÁN
 * http://hulan.cz/
 *
 * This script requires PHP 5.0 or later with SQLite support.
 * This script is in UTF-8 encoding.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 *
 * Captcha image generation code based on:
 *
 *   - hn_captcha by Horst Nogajski
 *     (http://hn273.users.phpclasses.org/browse/package/1569.html)
 *   - ocr_captcha by Julien Pachet
 *     (http://new21.mirrors.phpclasses.org/browse/package/1538.html)
 **/


// show Captcha image
if (isset($_GET['action'])) {
	if (__getVar('action')=='image') {

		include_once('../admin/admin_functions.php');
		 
		$captcha = new CAPTCHA();
		$captcha->doCaptcha();
		exit(0);
	}
}


class CAPTCHA {

	/**
	 * @shortdesc Absolute path to folder with TrueTypeFonts (with trailing slash!). This must be readable by PHP.
	 * @type string
	 **/
	var $TTF_folder;

	/**
	 * @shortdesc A List with available TrueTypeFonts for random char-creation.
	 * @type mixed[array|string]
	 **/
	var $TTF_RANGE  = array('dustismo.ttf');

	/**
	 * @shortdesc How many chars the generated text should have
	 * @type integer
	 **/
	var $chars		= 4;

	/**
	 * @shortdesc The minimum size a Char should have
	 * @type integer
	 **/
	var $minsize	= 20;

	/**
	 * @shortdesc The maximum size a Char can have
	 * @type integer
	 **/
	var $maxsize	= 20;

	/**
	 * @shortdesc The maximum degrees a Char should be rotated. Set it to 30 means a random rotation between -30 and 30.
	 * @type integer
	 **/
	var $maxrotation = 0;

	/**
	 * @shortdesc Background noise On/Off (if is Off, a grid will be created)
	 * @type boolean
	 **/
	var $noise		= TRUE;

	/**
	 * @shortdesc This will only use the 216 websafe color pallette for the image.
	 * @type boolean
	 **/
	var $websafecolors = FALSE;

	/**
	 * @shortdesc JPEG quality (100 = best, 0 = clos to none)
	 * @type int
	 **/
	var $jpeg_quality = 95;


	/**
	 * @shortdesc don't generate captchas with less than $minchars chars
	 * @type int
	 **/
	var $minchars = 4;

	/**
	 * @shortdesc captchas live no longer than $ttl minutes
	 * @type int
	 **/
	var $ttl = 30;

	/**
	 * @shortdesc Message if captcha fails
	 * @type text
	 **/
	var $FailedMsg;

	/**
	 * @shortdesc html code to show captcha form
	 * @type text
	 **/
	var $formHTML;

	/**
	 * @shortdesc table in which captcha keys are stored
	 * @type text
	 **/
	var $table;

	/**
	 * @shortdesc sqlite database name
	 * @type text
	 **/
	var $database;

	/**
	 * @shortdesc URL to captcha code
	 * @type text
	 **/
	var $url;

	/**
	 * Create CAPTCHA object
	 */
	function __construct()
	{
		global $path, $id;
		
		// form html
		$this->formHTML =
			'
			<form action="'.$path.'index.php?id='.$_REQUEST['id'].'" method="post">
       <table class="message_form" cellspacing="0" cellpadding="0">
        <tr>
         <th colspan="3">
          <h4>Pridať komentár</h4>
         </th>
        </tr>
        <tr>
         <td>
          <b>Meno:</b>
         </td>
         <td>
          <input type="text" name="name" value="'.$_REQUEST['name'].'" />
         </td>
         <td rowspan="3">
          <%imgHtml%>
         </td>
        </tr>
        <tr>
         <td>
          <b>Kontakt(voliteľné):</b>
         </td>
         <td>
          <input type="text" name="mail" value="'.$_REQUEST['mail'].'" />
         </td>
        </tr>
        <tr>
         <td>
          <b>Kód z obrázka:</b>
         </td>
         <td>
          <input type="text" name="ver_sol" maxlength="6" id="captcha_cf_verif" />
          <input name="ver_key" type="hidden" value="<%key%>" />
         </td>
        </tr>
        <tr>
         <td colspan="3" style="text-align:center; padding: 8px 0 0 0;">
					<b>Text komentára</b>
         </td>
        <tr>
         <td colspan="3">
          <script type="text/javascript">
		        //<![CDATA[
			        function insertext(text){
			        document.getElementById(\'comment\').value+=" "+ text;
			        document.getElementById(\'comment\').focus();
			        }
		        //]]>
	        </script>
          <textarea id="comment" name="comment" >'.$_REQUEST['comment'].'</textarea><br />
         </td>
        </tr>
        <tr>
         <td>
          <input type="hidden" name="action" value="add_comment" />
          <input type="submit" name="submit" value="Pridať komentár" />
         </td>
         <td colspan="2" align="right">
	        <a href="javascript:insertext(\':smile:\',\'short\')"><img style="border: none;" alt="smile" src="'.$path.'pics/smiles/smile.gif" /></a>
	        <a href="javascript:insertext(\':wink:\',\'short\')"><img style="border: none;" alt="wink" src="'.$path.'pics/smiles/wink.gif" /></a>
	        <a href="javascript:insertext(\':wassat:\',\'short\')"><img style="border: none;" alt="wassat" src="'.$path.'pics/smiles/wassat.gif" /></a>
	        <a href="javascript:insertext(\':tongue:\',\'short\')"><img style="border: none;" alt="tongue" src="'.$path.'pics/smiles/tongue.gif" /></a>
	        <a href="javascript:insertext(\':laughing:\',\'short\')"><img style="border: none;" alt="laughing" src="'.$path.'pics/smiles/laughing.gif" /></a>
	        <a href="javascript:insertext(\':sad:\',\'short\')"><img style="border: none;" alt="sad" src="'.$path.'pics/smiles/sad.gif" /></a>
	        <a href="javascript:insertext(\':angry:\',\'short\')"><img style="border: none;" alt="angry" src="'.$path.'pics/smiles/angry.gif" /></a>
	        <a href="javascript:insertext(\':crying:\',\'short\')"><img style="border: none;" alt="crying" src="'.$path.'pics/smiles/crying.gif" /></a>
	        <br />
         </td>
        </tr>
       </table>
      </form>     
      ';

		// sqlite table
		$this->table = 'captcha';

		// URL to captcha
		$this->url = 'http://'. __serverVar('HTTP_HOST') . __serverVar('REQUEST_URI');
		$this->url = mb_substr($this->url,0,strrpos($this->url,'/')+1) . mb_substr(__FILE__,strrpos(__FILE__,DIRECTORY_SEPARATOR)+1);
	}

	/**
	 * Destroy CAPTCHA object
	 */
	function __destruct()
	{
	}


	/**
	 * Creates Captcha graphics
	 */
	function doCaptcha()
	{
		// initialize on first call
		if (!$this->_inited)
		$this->_init_captcha();
			
		$key 	= __getVar('key');
		$width	= int__getVar('width');
		$height	= int__getVar('height');

		if ($width < 200) $width = -1;
		if ($height < 25) $height = -1;

		$this->generateImage($key, $width, $height);
	}

	/**
	 * We'll add HTML code to insert the captcha image
	 */
	function doFormExtras() {
		// initialize on first call
		if (!$this->_inited)
		$this->_init_captcha();
			
		// don't do anything when no GD libraries are available
		if (!$this->isAvailable())
		return;

		// create captcha key. This key is required to
		//
		// 1. create the captcha image
		// 2. check the validity of the entered solution
		$key = $this->generateKey();

		$aVars = array(
			'imgHtml' => $this->generateImgHtml($key),
			'key' => htmlspecialchars($key)
		);

		return $this->fill($this->formHTML, $aVars);
	}

	/**
	 * Called when message is validated. We'll check if the
	 * provided captcha solution is correct here. If not, we'll return an error.
	 */
	function doValidateForm() {
		// initialize on first call
		if (!$this->_inited)
		$this->_init_captcha();

		// don't do anything when no GD libraries are available
		if (!$this->isAvailable())
		return 'No GD2 library installed';

		// get key and attempted solution from request
		$ver_key = __postVar('ver_key');
		$ver_sol = __postVar('ver_sol');

		// check if the solution matches what is in the database
		if (!$this->check($ver_key, $ver_sol)) {
			return $this->FailedMsg;
		}

		// delete captcha key
		$this->_deleteKey($ver_key);
		return false;
	}

	/**
	 * Returns the URL of the captcha image. The image will be created upon first call
	 * of the URL. When requested multiple times, all but the first request will fail.
	 */
	function generateImgUrl($key, $width = -1, $height = -1)
	{
		global $CONF;

		$imgUrl = $this->url.'?&action=image&key=' . $key;

		if (($width != -1) && ($height != -1))
		$imgUrl .= '&width=' . intval($width) . '&height=' . intval($height);
			
		return $imgUrl;
	}

	/**
	 * Returns an <img src=...> tag that includes the captcha image in the output.
	 */
	function generateImgHtml($key, $width = -1, $height = -1)
	{
		global $path;
		
		$imgUrl  = $this->generateImgUrl($key, $width, $height);
		if ($width == -1)		$width = $this->_lx;
		if ($height == -1)		$height = $this->_ly;
		$imgHtml = '<img src="'.$path.'public/captcha.php?action=image&key=' . $key . '"  width="' . intval($width). '" height="' . intval($height) . ' " alt="Antispamova ochrana" title="Prosim odpiste kod z obrazku." border="1"/>';
		return $imgHtml;
	}

	/**
	 * Returns true only when GD libraries are available. If these libraries
	 * are not installed, it's impossible to generate captcha images
	 */
	function isAvailable() {
		return ($this->_gd_version > 0);
	}

	/**
	 * Generates a random identifier string that will be used to identify
	 * a captcha. This ID will get included as hidden variable in the form
	 * together with the captcha image. Once the form is submitted back to
	 * the server, the ID is used to verify the captcha (solution to the
	 * captcha will be in the database, linked to the ID)
	 */
	function generateKey()
	{
		// initialize on first call
		if (!$this->_inited)
		$this->_init_captcha();

		$ok = false;

		while (!$ok)
		{
			// generate a random token
			srand((double)microtime()*1000000);
			$key = sha1(uniqid(rand(), true));
			$query=$this->sql_query('select ckey from captcha where ckey="'.$this->sql_escape($key).'"');
			if ($this->sql_num_rows($query)==0) {
				// add in database as non-active
				$query = 'INSERT INTO captcha (ckey, time, solution, active) ';
				$query .= 'VALUES ("' . $this->sql_escape($key). '", "' . date('Y-m-d H:i:s',time()) . '", "", "0")';
				$this->sql_query($query);
				$ok = true;
			}
		}

		return $key;
	}

	/**
	 * Checks if a given solution is the correct one for a given key. For each active key, this
	 * method can only be called once. After the call, the entry is removed from the database.
	 */
	function check($key, $solution)
	{
		// initialize on first call
		if (!$this->_inited)
		$this->_init_captcha();

		// cleanup old captchas
		$this->_removeOldEntries();

		// check if key exists
		if (!$this->_existsKey($key))
		return false;

		// get info
		$_res = $this->sql_query($sql = 'SELECT * FROM captcha WHERE ckey="' . $this->sql_escape($key) . '"');
		if ($_res)
		$o = $this->sql_fetch_object($_res);
			
		if (!$_res || !$o)
		return false;

		// check if captcha entry is active
		if ($o->active != 1)
		return false;
			
		// check solution
		if (sha1(mb_strtoupper($solution)) != $o->solution)
		return false;

		// correct solution for captcha challenge
		return true;
		
	}

	/**
	 * Generates a captcha image and outputs it to standard output (image/png)
	 */
	function generateImage($key, $width = -1, $height = -1)
	{
		// initialize on first call
		if (!$this->_inited)
		$this->_init_captcha();
			
		// re-calculate settings when explicit width/height is specified
		if (($width != -1) && ($height != -1) && ($width >= 200) && ($height >= 25))
		{
			// generate appropriate settings for the new width
			$this->_lx = $width;
			$this->_ly = $height;
			$this->maxsize = (int)($this->_ly / 2.4);
			if ($this->maxsize < $this->minsize)
			$this->minsize = $this->maxsize;
			$this->chars = (int) ($this->_lx / (int)(($this->maxsize + $this->minsize) / 1.5)) - 1;
		}

		// cleanup old captchas (older than 10 minutes)
		$this->_removeOldEntries();

		// cannot create an image if there is no gd library
		if ($this->_gd_version == 0)
		die('no gd library');

		if(count($this->TTF_RANGE) < 1)
		$this->_error_img($this->_lx, $this->_ly, 'no font available.');

		// make sure it's not possible to create a captcha with only 1 or even no characters
		// by messing with the width and height
		if ($this->chars < $this->minchars)
		$this->_error_img($this->_lx, $this->_ly, 'unsafe to create.');

		// check if key exists
		if (!$this->_existsKey($key))
		$this->_error_img($this->_lx, $this->_ly, 'invalid key.');

		// captcha must be inactive
		if ($this->_isActive($key))
		$this->_error_img($this->_lx, $this->_ly, 'already activated.');

		// generate solution (random string of $this->chars characters)
		$private_key = $this->_generateSolution();

		// store key in database & mark as activated
		$query = 'UPDATE captcha SET active=1, solution="' . $this->sql_escape(sha1($private_key)) . '" WHERE ckey="' . $this->sql_escape($key) . '"';
		$this->sql_query($query);

		// create Image and set the apropriate function depending on GD-Version & websafecolor-value
		if($this->_gd_version >= 2 && !$this->websafecolors)
		{
			$func_createImg = 'imagecreatetruecolor';
			$func_color = 'imagecolorallocate';
		}
		else
		{
			$func_createImg = 'imageCreate';
			$func_color = 'imagecolorclosest';
		}
		$image = call_user_func($func_createImg, $this->_lx, $this->_ly);

		// select first TrueTypeFont
		$this->_change_TTF();

		// Set Backgroundcolor
		$this->_random_color(224, 255);
		$_back =  @imagecolorallocate($image, $this->_r, $this->_g, $this->_b);
		@imagefilledrectangle($image,0,0,$this->_lx,$this->_ly,$_back);

		// allocates the 216 websafe color palette to the image
		if($this->_gd_version < 2 || $this->websafecolors) $this->_makeWebsafeColors($image);

		// fill with noise or grid
		if($this->_nb_noise > 0)
		{
			// random characters in background with random position, angle, color
			for($i=0; $i < $this->_nb_noise; $i++)
			{
				srand((double)microtime()*1000000);
				$size	= intval(rand((int)($this->minsize / 2.3), (int)($this->maxsize / 1.7)));
				srand((double)microtime()*1000000);
				$angle	= intval(rand(0, 360));
				srand((double)microtime()*1000000);
				$x		= intval(rand(0, $this->_lx));
				srand((double)microtime()*1000000);
				$y		= intval(rand(0, (int)($this->_ly - ($size / 5))));
				$this->_random_color(160, 224);
				$color	= call_user_func($func_color, $image, $this->_r, $this->_g, $this->_b);
				srand((double)microtime()*1000000);
				$text	= chr(intval(rand(45,250)));
				@imagettftext($image, $size, $angle, $x, $y, $color, $this->_change_TTF(), $text);
			}
		}
		else
		{
			// generate grid
			for($i=0; $i < $this->_lx; $i += (int)($this->minsize / 1.5))
			{
				$this->_random_color(160, 224);
				$color	= call_user_func($func_color, $image, $this->_r, $this->_g, $this->_b);
				@imageline($image, $i, 0, $i, $this->_ly, $color);
			}
			for($i=0 ; $i < $this->_ly; $i += (int)($this->minsize / 1.8))
			{
				$this->_random_color(160, 224);
				$color	= call_user_func($func_color, $image, $this->_r, $this->_g, $this->_b);
				@imageline($image, 0, $i, $this->_lx, $i, $color);
			}
		}

		// generate Text
		for($i=0, $x = intval(rand($this->minsize,$this->maxsize)); $i < $this->chars; $i++)
		{
			$text	= strtoupper(mb_substr($private_key, $i, 1));
			srand((double)microtime()*1000000);
			$angle	= intval(rand(($this->maxrotation * -1), $this->maxrotation));
			srand((double)microtime()*1000000);
			$size	= intval(rand($this->minsize, $this->maxsize));
			srand((double)microtime()*1000000);
			$y		= intval(rand((int)($size * 1.5), (int)($this->_ly - ($size / 7))));
			$this->_random_color(0, 127);
			$color	=  call_user_func($func_color, $image, $this->_r, $this->_g, $this->_b);
			$this->_random_color(0, 127);
			$shadow = call_user_func($func_color, $image, $this->_r + 127, $this->_g + 127, $this->_b + 127);
			@imagettftext($image, $size, $angle, $x + (int)($size / 15), $y, $shadow, $this->_change_TTF(), $text);
			@imagettftext($image, $size, $angle, $x, $y - (int)($size / 15), $color, $this->_TTF_file, $text);
			$x += (int)($size + ($this->minsize / 5));
		}

		header('Content-Type: image/jpeg');
		@imagejpeg($image, '', $this->jpeg_quality);
		@imagedestroy($image);

	}


	var $_inited = 0;		// indicates that _init_captcha has been called once
	var $_lx;				// width of picture
	var $_ly;				// height of picture
	var $_noisefactor = 9;	// this will multiplyed with number of chars
	var $_nb_noise;			// number of background-noise-characters
	var $_TTF_file;			// holds the current selected TrueTypeFont
	var $_gd_version;		// holds the Version Number of GD-Library
	var $_r;				// R
	var $_g;				// G
	var $_b;				// B
	var $_activedb;			// handle to SQLite database

	/**
	 * Initializes and checks internal variables
	 */
	function _init_captcha()
	{

		// Test for GD-Library(-Version)
		$this->_gd_version = $this->_get_gd_version();

		// check settings
		if($this->minsize > $this->maxsize)
		{
			$temp = $this->minsize;
			$this->minsize = $this->maxsize;
			$this->maxsize = $temp;
		}

		// check TrueTypeFonts
		if (!is_array($this->TTF_RANGE))
		$this->TTF_RANGE = array();

		$temp = array();
		foreach($this->TTF_RANGE as $k=>$v)
		{
			if(is_readable($this->TTF_folder.$v)) $temp[] = $v;
		}
		$this->TTF_RANGE = $temp;

		// get number of noise-chars for background if is enabled
		$this->_nb_noise = $this->noise ? ($this->chars * $this->_noisefactor) : 0;

		// set (initial) dimension of image
		$this->_lx = ($this->chars + 1) * (int)(($this->maxsize + $this->minsize) / 1.5);
		$this->_ly = (int)(2.4 * $this->maxsize);

		// make sure the method is called only once
		$this->_inited = 1;
	}


	function _existsKey($key)
	{
		return ($this->quickQuery('SELECT COUNT(*) AS result FROM captcha WHERE ckey="' . $this->sql_escape($key) . '"') == 1);
	}

	function _isActive($key)
	{
		return ($this->quickQuery('SELECT active AS result FROM captcha WHERE ckey="' . $this->sql_escape($key) . '"') == 1);
	}

	function _deleteKey($key)
	{
		$this->sql_query('DELETE FROM captcha WHERE ckey="'.$this->sql_escape($key).'"');
	}

	function _removeOldEntries()
	{
		$_boundary = time() - $this->ttl * 60;	// no captcha lives for more than one hour
		$this->sql_query('DELETE FROM captcha WHERE time < "' . date('Y-m-d H:i:s',$_boundary) . '"');
	}

	function _get_gd_version()
	{
		if (function_exists('imagecreatetruecolor'))
		return 2;
		else
		return 0;	// no GD installed, or old version
	}

	function _change_TTF()
	{
		srand((float)microtime() * 10000000);
		$key = array_rand($this->TTF_RANGE);
		$this->_TTF_file = $this->TTF_folder.$this->TTF_RANGE[$key];
		return $this->_TTF_file;
	}

	function _random_color($min,$max)
	{
		srand((double)microtime() * 1000000);
		$this->_r = intval(rand($min,$max));
		srand((double)microtime() * 1000000);
		$this->_g = intval(rand($min,$max));
		srand((double)microtime() * 1000000);
		$this->_b = intval(rand($min,$max));
	}

	function _makeWebsafeColors(&$image)
	{
		for($_r = 0; $_r <= 255; $_r += 51)
		{
			for($_g = 0; $_g <= 255; $_g += 51)
			{
				for($_b = 0; $_b <= 255; $_b += 51)
				{
					$color = imagecolorallocate($image, $_r, $_g, $_b);
				}
			}
		}
	}

	function _generateSolution()
	{
		//$letters = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
		$letters = array('1', '2', '3', '4', '5', '6', '7', '8', '9');
		$numbers = array('1', '2', '3', '4', '5', '6', '7', '8', '9');

		$num_letters = count($letters);
		$num_numbers = count($numbers);

		$private_key = '';
		for($i = 0; $i < $this->chars / 2; $i++){
			srand((double)microtime()*1000000);
			$private_key = $letters[rand(0, $num_letters - 1)] . $private_key . $numbers[rand(0, $num_numbers - 1)];
		}
		return strtoupper(mb_substr($private_key, 0, $this->chars));
	}

	function _error_img($w, $h, $error)
	{
		// create Image and set the apropriate function depending on GD-Version & websafecolor-value
		if($this->_gd_version >= 2 && !$this->websafecolors)
		{
			$func_createImg = 'imagecreatetruecolor';
			$func_color = 'imagecolorallocate';
		}
		else
		{
			$func_createImg = 'imageCreate';
			$func_color = 'imagecolorclosest';
		}
		$image = call_user_func($func_createImg, $this->_lx, $this->_ly);

		// fill background in red
		$_back =  @imagecolorallocate($image, 255, 128, 128);
		@imagefilledrectangle($image,0,0,$w,$h,$_back);

		// add text
		$fore =  @imagecolorallocate($image, 255, 255, 255);
		imagestring($image, 3, 5, $this->_ly/2 - 5, $error, $fore);

		// dump image
		header('Content-Type: image/jpeg');
		@imagejpeg($image, '', 90);
		@imagedestroy($image);

		exit;

	}

	/**
	 * fills a template with values
	 * (static)
	 *
	 * @param $template
	 *		Template to be used
	 * @param $values
	 *		Array of all the values
	 */
	function fill($template, $values) {
		if (sizeof($values) > 0) {
			// go through all the values
			for(reset($values); $key = key($values); next($values))
			$template = str_replace("<%$key%>",$values[$key],$template);
		}
		// remove non matched template-tags
		return preg_replace('/<%[a-zA-Z]+%>/','',$template);
	}



	/**
	 * SQLITE:: executes an SQL query
	 */
	function sql_query($query) {
		$query = preg_replace('/`(\w+)`/','$1',$query);
		$_result = @psw_mysql_query($query)
		or
		die ("<pre>Invalid query: ".htmlspecialchars($query)." <br /><br /><br />: <b>".mysql_error()."</b></pre>");
		return $_result;
	}

	function quickQuery($query) {
		$_r = $this->sql_query($query);
		if ($obj = $this->sql_fetch_object($_r))
		return $obj->result;
		else
		return '';
	}

	/**
	 * SQLITE:: Returns escaped string for query
	 */
	function sql_escape($text){
		return $text;
	}

	/**
	 * SQLITE:: Unescapes string
	 */
	function sql_unescape($text){
		return $text;
	}

	/**
	 * SQLITE:: Returns number of rows for resultset
	 */
	function sql_num_rows(&$_resource){
		return @mysql_num_rows($_resource);
	}

	/**
	 * SQLITE:: Shows SQL DB <br /> message
	 */
	function sql_error() {
		return @sqlite_error_string(@sqlite_last_error($this->_activedb));
	}

	/**
	 * SQLITE:: Fetch resultset as an object
	 */
	function sql_fetch_object(&$_resource){
		return @mysql_fetch_object($_resource);
	}

}

/**
 * Helper fuctions
 */
function __getVar($name)
{
	if (array_key_exists($name,$_GET))
	return __undoMagic($_GET[$name]);
	else
	return;
}

function __postVar($name)
{
	if (array_key_exists($name,$_POST))
	return __undoMagic($_POST[$name]);
	else
	return;
}

function __serverVar($name) {
	if (array_key_exists($name,$_SERVER))
	return $_SERVER[$name];
	else
	return;
}

// removes magic quotes if that option is enabled
function __undoMagic($data)
{
	return get_magic_quotes_gpc() ? stripslashes($data) : $data;
}

function int__getVar($name)
{
	return intval(__getVar($name));
}

?>
