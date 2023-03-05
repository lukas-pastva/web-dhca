<?php
/**
 * Subor v ktorom budu funkcie potrebne pre pracu.
 */

// ini_set('arg_separator.output', '&amp;');

/**
 * **************************************************************************************************************
 */
// Pripojenie do databazy
include_once ("admin/db.inc.php");

psw_mysql_query('SET NAMES utf8');

if (is_array($_REQUEST)) {
    foreach ($_REQUEST as $key => $value) {
        $_REQUEST[$key] = str_replace('"', '&quot;', $value);
    }
}

/**
 * ******************************************************************************
 */

// Funkcia, ktora prihlasi a zeregistruje sesiony.
function login($nick, $pass)
{
    $nick = strtolower($nick);
    $pass = strtolower($pass);
    $pass = md5($pass);
    $logged = true;

    $result = psw_mysql_query('SELECT * FROM user WHERE nick="' . $nick . '" ');
    $vybratie = $result->fetch_assoc();

    if ($pass == $vybratie['pass']) {
        psw_mysql_query('INSERT INTO user_login (time, user_id, ip) VALUES ("' . time() . '", "' . $vybratie['id'] . '", "' . $_SERVER["REMOTE_ADDR"] . '") ');

        $change_id = session_regenerate_id();
        session_register('meno_uzivatela');
        $_SESSION['meno_uzivatela'] = $nick;
        $logged = true;
    } else {
        $logged = false;
    }

    return $logged;
}

/**
 * ******************************************************************************
 */

// Funkcia, ktora vrati ci uzivatel ma, alebo nema povoleny pristup k danej sekcii
function userGetAccess($nick, $section)
{
    $isAccessed = false;

    $result1 = psw_mysql_query('SELECT `' . $section . '` FROM user WHERE nick = "' . $nick . '" ');
    $result = $result1->fetch_assoc();
    // debug($result);
    // debug($sql);
    // echo mysql_error();
    if ($result[$section] == "1") {
        $isAccessed = true;
    }
    return $isAccessed;
}

/**
 * ******************************************************************************
 */

// Funkcia, ktora vrati ci uzivatel ma, alebo nema povoleny pristup k danej sekcii
function userGetAccessBySectionId($nick, $sectionId)
{
    $isAccessed = false;
    $section = getSectionNameById($sectionId);

    $result1 = psw_mysql_query('SELECT `' . $section . '` FROM user WHERE nick = "' . $nick . '" ');
    $result = $result1->fetch_assoc();

    // debug($sql);

    if ($result[$section] == "1") {
        $isAccessed = true;
    }
    return $isAccessed;
}

/**
 * ******************************************************************************
 */

// Funkcia, ktora vrati ci uzivatel ma, alebo nema povoleny pristup k danej sekcii
function getSectionNameById($sectionId)
{
    $result1 = psw_mysql_query('SELECT main_section FROM main_section WHERE main_section_id = "' . $sectionId . '" ');
    $result = $result1->fetch_assoc();

    return $result['main_section'];
}

/**
 * *******************************************************************************************************************
 */
function echoErrors()
{
    $errorStr = '';
    foreach ($_REQUEST as $key => $value) {
        if (($_REQUEST[$key] == '') && (mb_substr($key, 0, 1) == '_')) {
            $errorStr .= '<span style="color: #bb0000;">Vložte hodnotu ' . mb_substr($key, 1) . '</span><br />';
        }
    }
    foreach ($_FILES as $key => $value) {
        if (($_FILES[$key]['tmp_name'] == '') && (mb_substr($key, 0, 1) == '_')) {
            $errorStr .= '<span style="color: #bb0000;">Vložte hodnotu ' . mb_substr($key, 1) . '</span><br />';
        }
    }
    return $errorStr;
}

/**
 * *******************************************************************************************************************
 */
function getSections()
{
    $sections = Array();

    $result = psw_mysql_query('SELECT * FROM main_section ORDER BY main_section');
    while ($pocesSkrytych = $result->fetch_assoc()) {

        if (userGetAccess($_SESSION['meno_uzivatela'], $results['main_section'])) {
            $sections[count($sections)]['id'] = $results['main_section_id'];
            $sections[count($sections) - 1]['name'] = $results['main_section'];
        }
    }
    return $sections;
}

/**
 * *******************************************************************************************************************
 */
function getClankyByUser($user, $order_by, $from, $limit, $ordering)
{
    if (! $from) {
        $from = 0;
    }
    $clanky = Array();
    if (! $order_by) {
        $order_by = 'clanok_id';
    }
    if ($ordering) {
        if ($_SESSION['orderBy'] == $order_by) {
            if ($_SESSION['asc'] == 'ASC') {
                $_SESSION['asc'] = 'DESC';
            } else {
                $_SESSION['asc'] = 'ASC';
            }
        }
    }
    $_SESSION['orderBy'] = $order_by;

    $result = psw_mysql_query('SELECT clanok_id, main_section_id, nazov, datetime, user FROM clanok ORDER BY ' . $order_by . ' ' . ($_SESSION['asc'] ? $_SESSION['asc'] : 'DESC') . ' LIMIT ' . $from . ', ' . $limit . '');
    while ($results = $result->fetch_assoc()) {

        // debug($results['nazov']);
        if (userGetAccessBySectionId($user, $results['main_section_id'])) {
            $clanky[count($clanky)] = $results;
        }
    }
    return $clanky;
}

/**
 * *******************************************************************************************************************
 */
function getSectionNameFromId($id)
{
    $result = psw_mysql_query('SELECT main_section FROM main_section WHERE main_section_id = "' . $id . '"');
    $name = $result->fetch_assoc();

    return $name['main_section'];
}

/**
 * *******************************************************************************************************************
 */
function getTableRows($table, $params, $orderBy, $odkial = 0, $kolko = 9999)
{
    if (is_array($params)) {
        $i = NULL;
        foreach ($params as $key => $value) {
            if (mb_substr($key, 0, 6) == 'where_') {
                $i .= 'AND ' . mb_substr($key, 6) . ' = \'' . $value . '\' ';
            }
        }
        $params = $i;
    }

    $sql = 'SELECT * FROM `' . $table . '` WHERE 1=1 ' . $params . ' ORDER BY ' . (strpos($orderBy, 'ASC') == TRUE ? $orderBy : $orderBy . ' DESC') . ' LIMIT  ' . $odkial . ', ' . $kolko . '';

    $results = array();
    $i = 0;
    // debug($sql);

    $fetch = psw_mysql_query($sql);
    while ($result = $fetch->fetch_assoc()) {

        $result2 = array();
        foreach ($result as $key => $value) {
            if (! is_int($key)) {
                $result2[$key] = $value;
            }
        }

        $results[$i] = $result2;
        $i ++;
    }
    return $results;
}

/**
 * *******************************************************************************************************************
 */
function getTableRowsByAttribudes($table, $attributes, $params, $orderBy, $odkial = 0, $kolko = 9999)
{
    if (is_array($params)) {
        $i = NULL;
        foreach ($params as $key => $value) {
            if (mb_substr($key, 0, 6) == 'where_') {
                $i .= 'AND ' . mb_substr($key, 6) . ' = \'' . $value . '\' ';
            }
        }
        $params = $i;
    }

    $sql = 'SELECT ' . $attributes . ' FROM `' . $table . '` WHERE 1=1 ' . $params . ' ORDER BY ' . (strpos($orderBy, 'ASC') == TRUE ? $orderBy : $orderBy . ' DESC') . ' LIMIT  ' . $odkial . ', ' . $kolko . '';
    // debug($sql);
    $stmt = psw_mysql_query($sql);
    $results = array();
    $i = 0;

    while ($result = $stmt->fetch_assoc()) {

        $result2 = array();
        foreach ($result as $key => $value) {
            if (! is_int($key)) {
                $result2[$key] = $value;
            }
        }

        $results[$i] = $result2;
        $i ++;
    }
    return $results;
}

/**
 * *******************************************************************************************************************
 */
function getTableRow($table, $id_name, $id)
{
    if (0 < ($id)) {
        $id = $id;
    } else {
        $id = '"' . $id . '"';
    }

    $sql = 'SELECT * FROM `' . $table . '` WHERE ' . $id_name . ' =  ' . $id . '';
    if (! $stmt = psw_mysql_query($sql)) {
        echo mysql_error();
    }

    $results = array();
    $i = 0;

    $stmt = psw_mysql_query($sql);
    while ($result = $stmt->fetch_assoc()) {

        $result2 = array();
        foreach ($result as $key => $value) {
            if (! is_int($key)) {
                $result2[$key] = $value;
            }
        }

        $results[$i] = $result2;
        $i ++;
    }
    return $results;
}

/**
 * *******************************************************************************************************************
 */
function echoPaging($table, $where, $from, $limit, $params)
{
    $result = psw_mysql_query('SELECT count(*) AS pocet FROM ' . $table . ' WHERE 1=1 ' . $where);
    $pocet = $result->fetch_assoc();

    $pocet = $pocet['pocet'];
    $pocetStranok = ceil($pocet / $limit);

    echo '
        <form action="' . $_SERVER['PHP_SELF'] . '?' . $params . '" method="post">
         <div class="paging">Počet záznamov na stranu', printSelectBox(array(
        '10',
        '20',
        '30',
        '40',
        '999'
    ), 'limit', $limit, TRUE), 'Strana:';
    for ($i = 0; $i < $pocetStranok; $i ++) {
        if (($i * $limit) == $from) {
            echo '<a href="' . $_SERVER['PHP_SELF'] . '?limit=' . $limit . '&amp;from=' . ($i * $limit) . '&amp;' . $params . '"><b>' . ($i + 1) . '</b></a>&nbsp;';
        } else {
            echo '<a href="' . $_SERVER['PHP_SELF'] . '?limit=' . $limit . '&amp;from=' . ($i * $limit) . '&amp;' . $params . '">' . ($i + 1) . '</a>&nbsp;';
        }
    }
    echo '
         </div>
        </form>
      ';
}

/**
 * *******************************************************************************************************************
 */
function printSelectBox($data, $name, $selectedValue = '', $onSelect = FALSE)
{
    echo '
       <select name="' . $name . '"  onchange="', ($onSelect ? ' submit();' : ''), '"  >
         <option value="">
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
         </option>
     ';
    foreach ($data as $value) {
        if ($value == $selectedValue) {
            echo '<option selected="selected" value="' . $value . '">' . $value . '</option>';
        } else {
            echo '<option value="' . $value . '">' . $value . '</option>';
        }
    }
    echo '
       </select>
     ';
}

/**
 * *******************************************************************************************************************
 */
function printFotoAlbum($selectedValue = '')
{
    echo '<select name="section_id" size="1">
	<option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
	';

    $result = psw_mysql_query('SELECT main_section FROM main_section');
    while ($fetch = $result->fetch_assoc()) {

        if (userGetAccess($_SESSION['meno_uzivatela'], $fetch['main_section'])) {
            echo "\r\n\t<optgroup label=\"" . $fetch['main_section'] . "\">";

            $result2 = psw_mysql_query('SELECT * FROM section WHERE main_section = "' . $fetch['main_section'] . '" ORDER BY sub_section ASC');
            while ($fetch2 = $result2->fetch_assoc()) {

                // Vypiseme pocet obrazkov do zatvoriek
                $result3 = psw_mysql_query('SELECT count(*) AS pocet_obrazkov FROM picture WHERE section_id = "' . $fetch2['section_id'] . '" ');
                $pocet_obrazkov = $result3->fetch_assoc();

                echo "\r\n\t<option ";
                if ($selectedValue == $fetch2['section_id']) {
                    echo "selected";
                }
                echo " value=\"" . $fetch2['section_id'] . "\">" . $fetch2['section_name'] . " (" . $pocet_obrazkov['pocet_obrazkov'] . ")";
            }
            echo "\r\n\t</optgroup>";
        }
    }

    echo '</select>';
}

/**
 * *******************************************************************************************************************
 */
function getCommentsForArticle($clanok_id)
{
    $rows = getTableRows('comment', ' AND clanok_id = "' . $clanok_id . '" ', 'datetime ASC');
    return $rows;
}

/**
 * *******************************************************************************************************************
 */
function getClanokNameFromId($clanok_id)
{
    $row = getTableRow('clanok', 'clanok_id', $clanok_id);
    return $row[0]['nazov'];
}

/**
 * *******************************************************************************************************************
 */
function getCommentsNr($clanok_id)
{
    $result = psw_mysql_query('SELECT count(*) AS pocet FROM comment WHERE clanok_id = "' . $clanok_id . '"');
    $row = $result->fetch_assoc();

    return $row['pocet'];
}

/**
 * *******************************************************************************************************************
 */
function getSectionIdFromName($sectionName)
{
    $result = psw_mysql_query('SELECT main_section_id FROM main_section WHERE main_section = "' . $sectionName . '"');
    $id = $result->fetch_assoc();

    if (! 0 < ($id['main_section_id'])) {
        return 0;
    } else {
        return $id['main_section_id'];
    }
}

/**
 * *******************************************************************************************************************
 */

// Funkcia, ktora vyhodi vystrazne okno
function alert($hlaska)
{
    echo "<script>alert(\"" . $hlaska . "\");</script>";
}

/**
 * *******************************************************************************************************************
 */
// Prefiltruje text o smajliky :-)
function getSmiles($text)
{
    global $path;

    $text = mb_str_replace(":smile:", "<img border=\"0\" src=\"" . $path . "pics/smiles/smile.gif\" />", $text);
    $text = mb_str_replace(":wink:", "<img border=\"0\" src=\"" . $path . "pics/smiles/wink.gif\" />", $text);
    $text = mb_str_replace(":wassat:", "<img border=\"0\" src=\"" . $path . "pics/smiles/wassat.gif\" />", $text);
    $text = mb_str_replace(":tongue:", "<img border=\"0\" src=\"" . $path . "pics/smiles/tongue.gif\" />", $text);
    $text = mb_str_replace(":laughing:", "<img border=\"0\" src=\"" . $path . "pics/smiles/laughing.gif\" />", $text);
    $text = mb_str_replace(":sad:", "<img border=\"0\" src=\"" . $path . "pics/smiles/sad.gif\" />", $text);
    $text = mb_str_replace(":angry:", "<img border=\"0\" src=\"" . $path . "pics/smiles/angry.gif\" />", $text);
    $text = mb_str_replace(":crying:", "<img border=\"0\" src=\"" . $path . "pics/smiles/crying.gif\" />", $text);

    return $text;
}

/**
 * *******************************************************************************************************************
 */

// Zisti ci ma uzivatel ban a vrati pole...[0 = type] [1 = reason]
function isBanned($ip)
{
    return false;
}

/**
 * *******************************************************************************************************************
 */

// Ofltruje text o nadavky, resp prepise ich zahviezdickovanymi
function fuckFilter($text)
{
    return $text;
}

/**
 * *******************************************************************************************************************
 */
function debug($variable, $file = '', $line = '')
{
    echo '<b>file:</b> ' . $file . ' <b>line</b>: ' . $line . '<br /><pre>';
    print_r($variable);
    echo '</pre>' . "\n";
}

/**
 * *******************************************************************************************************************
 */
function printOdkazy()
{
    global $path;

    $odkazy = Array();

    $result = psw_mysql_query('SELECT * FROM odkaz');
    while ($odkaz = $result->fetch_assoc()) {

        echo '
		 <a href="' . $odkaz['link'] . '" title="' . $odkaz['alt'] . '" target="_blank" >' . (PARTNERI_TEXTOVO ? $odkaz['alt'] : '<img style="border:1px solid #000000;" alt="' . $odkaz['alt'] . '" border="1" src="' . $path . 'image/2/' . $odkaz['odkaz_id'] . '.jpg" />') . '</a>' . (PARTNERI_TEXTOVO ? '<br />' : '');
    }
}

/**
 * *******************************************************************************************************************
 */
function printPartyList()
{
    global $path;

    $partyList = Array();

    $is = false;

    $result = psw_mysql_query('SELECT * FROM partylist WHERE schvalene = "1" ORDER BY ordering ASC LIMIT 0, 3 ');
    while ($partyList = $result->fetch_assoc()) {

        $is = true;
        echo '
      <a href="' . (($partyList['link'] != '') ? $partyList['link'] : '' . $path . 'image/4/' . $partyList['partylist_id'] . '.jpg') . '"  target="_blank">
       <img style="border:1px solid #000000;" alt="' . $partyList['alt'] . '" border="1" src="' . $path . 'image/3/' . $partyList['partylist_id'] . '.jpg" />
      </a>
      <b><br />' . $partyList['title'] . '</b>
      <br />' . $partyList['text'] . '<br /><br />
		';
    }
    if (! $is) {
        echo '
      <br />Žiadna akcia nie je vložená.<br /><br />
		';
    }
}

/**
 * *******************************************************************************************************************
 */
function getNrOfComments($clanokId)
{
    $result = psw_mysql_query('SELECT count(*) AS pocet FROM comment WHERE clanok_id = "' . $clanokId . '"');
    $pocet = $result->fetch_assoc();

    return $pocet['pocet'];
}

/**
 * *******************************************************************************************************************
 */
function echoSectionStories($sectionName, $templateFile, $from = 0, $pocet = 999)
{
    global $path;

    // read stories from db
    $clanky = getTableRowsByAttribudes('clanok', 'clanok_id, nazov, short_text, datetime, user, main_section_id, counter, image ', ' AND main_section_id="' . getSectionIdFromName($sectionName) . '" ', 'datetime', $from, $pocet);

    $result = psw_mysql_query('SELECT count(*) AS pocet FROM clanok WHERE main_section_id="' . getSectionIdFromName($sectionName) . '"');
    $pocet_clankov = $result->fetch_assoc();

    $pocet_clankov = $pocet_clankov['pocet'];

    if ($pocet_clankov > 0) {
        foreach ($clanky as $clanok) {
            $is = TRUE;

            $story = Array();
            $story['params'] = 'id=' . $clanok['clanok_id'];
            $story['clanok_id'] = $clanok['clanok_id'];
            $story['title'] = $clanok['nazov'];
            $story['short_story'] = mb_str_replace('uploads/', $path . 'uploads/', mb_str_replace("\n", "<br />", getSmiles($clanok['short_text'])));
            // $story['short_story'] = $clanok['short_text'];
            $story['date'] = $clanok['datetime'];
            $story['counter'] = $clanok['counter'];
            $story['author'] = $clanok['user'];
            $story['comments_num'] = getNrOfComments($clanok['clanok_id']);

            if (is_file('public/templates/' . $templateFile)) {
                include ('public/templates/' . $templateFile);
            }
            echo $short_story_template;
        }
        if ($pocet_clankov > $pocet) {
            $pocet_stranok = ceil($pocet_clankov / $pocet);

            echo '<div class="story_listing" >' . ((($from - $pocet) >= 0) ? '<a href="' . $path . 'index.php?section=' . $sectionName . '&amp;from=' . ($from - $pocet) . '" >&lt; Predchádzajúce</a>&nbsp;' : '');
            for ($i = 0; $i < $pocet_stranok; $i ++) {
                echo '<a href="' . $path . 'index.php?section=' . $sectionName . '&amp;from=' . $i * $pocet . '">' . ($from == $i * $pocet ? '<b>' : '') . ($i + 1) . '' . ($from == $i * $pocet ? '</b>' : '') . '</a>&nbsp;';
            }
            echo ((($pocet + $from) < $pocet_stranok * $pocet) ? '<a href="' . $path . 'index.php?section=' . $sectionName . '&amp;from=' . ($from + $pocet) . '" >Ďaľšie &gt;</a>' : '') . '</div>';
        }
    } else {
        echo '<br /><br />V tejto sekcii sa nenachádzajú žiadne články.';
    }
}

/**
 * *******************************************************************************************************************
 */
function echoClanok($id)
{
    global $path;

    // inkrementuj clanok read counter
    incClanokReadCounter($id);

    $section = getSectionByClanokId($id);

    $templateFile = getTemplateForSection($section);

    // read story from db
    $clanok = getTableRow('clanok', 'clanok_id', $id);

    $clanok = $clanok[0];

    $story = Array();
    $story['params'] = 'id=' . $clanok['clanok_id'];
    $story['clanok_id'] = $clanok['clanok_id'];
    $story['title'] = $clanok['nazov'];
    $story['big_story'] = mb_str_replace('uploads/', $path . 'uploads/', mb_str_replace("\n", "<br />", getSmiles($clanok['big_text'])));
    $story['date'] = $clanok['datetime'];
    $story['author'] = $clanok['user'];

    if (is_file('public/templates/' . $templateFile)) {
        include ('public/templates/' . $templateFile);
    }
    echo $big_story_template;

    // echo video
    if (isset($_REQUEST['video'])) {
        echo '<a name="video"></a><span style="display: block; font-weight: bold; padding: 0 0 0 4px; margin: 20px 0 10px 0;">Čekuj video:</span>
          <script type="text/javascript">play2(\'' . $_REQUEST['video'] . '\', \'' . $_REQUEST['video'] . '\');</script>
          <center><div id="' . $_REQUEST['video'] . '"></div></center><br />';
    }

    // echo galery
    if (0 < ($_REQUEST['galery'])) {
        // echo '<div id="fotoalbum">';
        $_REQUEST['galeryId'] = $_REQUEST['galery'];
        echoGaleryByGaleryIdGeryBox($_REQUEST['galery']);
        // include('../public/fotoalbum.php');
        // echo '</div>';
        // echoGaleryByGaleryIdGeryBox($_REQUEST['galery']);
    } else {

        echoGaleryByArticleId($clanok['clanok_id']);
    }

    // echo comments
    echoComments($clanok['clanok_id'], $clanok['comments']);

    if ($clanok['comments'] == '1') {
        $captcha = new CAPTCHA();
        $captcha->__construct();
        echo $captcha->doFormExtras();
    } else {
        echo '<b>Nie je povolené vkladať komentáre.</b>';
    }
}

/**
 * *******************************************************************************************************************
 */
function echoClanokList($id)
{
    global $path;

    $templateFile = '1.tpl';

    // read story from db
    $clanok = getTableRow('clanok', 'clanok_id', $id);
    $clanok = $clanok[0];

    $story = Array();
    $story['params'] = 'id=' . $clanok['clanok_id'];
    $story['clanok_id'] = $clanok['clanok_id'];
    $story['title'] = $clanok['nazov'];
    $story['short_story'] = mb_str_replace('uploads/', $path . 'uploads/', mb_str_replace("\n", "<br />", getSmiles($clanok['short_text'])));
    $story['date'] = $clanok['datetime'];
    $story['counter'] = $clanok['counter'];
    $story['author'] = $clanok['user'];
    $story['comments_num'] = getNrOfComments($clanok['clanok_id']);

    if (is_file('public/templates/' . $templateFile)) {
        include ('public/templates/' . $templateFile);
    }
    echo $short_story_template;
}

/**
 * *******************************************************************************************************************
 */
function echoComments($clanokId, $comentsAllowed)
{
    $comments = getCommentsForArticle($clanokId);

    echo '<div class="story_comments">';
    if (count($comments) > 0) {
        foreach ($comments as $key => $comment) {
            echo '
			<table style="width: 100%; height: 40px;" cellspacing="0" cellpadding="3">
			  <tr>
			   <td height="1" style="font-family: verdana; color:#000000;font-size:11;" >
			    od <b>' . ($comment['mail'] ? '<a href="' . (strpos($comment['mail'], '@') ? 'mailto: ' . fuckFilter($comment['mail']) : '' . fuckFilter($comment['mail'])) . '" title="' . (strpos($comment['mail'], '@') ? 'mailto: ' . fuckFilter($comment['mail']) : '' . fuckFilter($comment['mail'])) . '" >' . fuckFilter($comment['nick']) . '</a>' : fuckFilter($comment['nick'])) . '</b> @ <span title="' . $comment['ip'] . '">' . $comment['datetime'] . '</span>
			   </td>
			  </tr>
			  <tr>
			   <td height="40" valign="top" bgcolor="#F9F9F9">
			    ' . getSmiles(fuckFilter($comment['text'])) . '
			   </td>
			  </tr>
			 </table>
			 <br />';
        }
    } else {
        if ($comentsAllowed == '1') {
            echo '<tr><td colspan="6"><br /><b>Žiadne komentáre nie sú vložené</b></td></tr>';
        }
    }
    echo '</div><br /><br />';
}

/**
 * *******************************************************************************************************************
 */
function echoNajnovsieKomentare($pocet)
{
    global $path;

    // fix!
    $query = '
	SELECT c. *, co. *
	FROM (
		SELECT clanok_id, max( datetime ) AS datetime
		FROM comment
		GROUP BY clanok_id
		ORDER BY datetime DESC
		LIMIT 0, ' . $pocet . '
	)x
		JOIN clanok c ON c.clanok_id = x.clanok_id
		JOIN comment co ON co.clanok_id = x.clanok_id
		AND co.datetime = x.datetime
		ORDER BY co.datetime DESC
	';

    $result = psw_mysql_query($query);
    while ($comment = $result->fetch_assoc()) {

        $commentHtml = fuckFilter('<b>' . strip_tags($comment['nick']) . '</b> »' . strip_tags($comment['text']));
        echo '<a href="' . $path . 'clanok/' . $comment['clanok_id'] . '-' . normalizeClanokName($comment['nazov']) . '#bott" title="' . validateForm(fuckFilter($comment['text'])) . '" target="_self">' . (mb_strlen($commentHtml) > (KOMENTARE_DLZKA_SPRAVY + 2) ? mb_substr($commentHtml, 0, KOMENTARE_DLZKA_SPRAVY) . '..' : $commentHtml) . '</a><br />';
    }
}

/**
 * *******************************************************************************************************************
 */
function echoNajnovsieClanky($pocet)
{
    global $path, $notDisplayedSections;

    // majprv musim zistit kolko je skrytych clankov a potom k poctu pripocitat to cislo
    $result = psw_mysql_query('select count(*) as pocet from clanok where home="0" AND main_section_id IN("27","32")');
    $pocesSkrytych = $result->fetch_assoc();

    $pocet += $pocesSkrytych['pocet'];

    $clanky = getTableRowsByAttribudes('clanok', 'clanok_id, nazov, main_section_id, home ', '', 'datetime', 0, $pocet);

    $count = 0;
    foreach ($clanky as $key => $clanok) {
        // nezobrazovat clanky co su v hidden, ale niesu na home
        $xxx = false;
        foreach ($notDisplayedSections as $notDisplayedSection) {
            if ($clanok['main_section_id'] == $notDisplayedSection) {
                $xxx = true;
                continue;
            }
        }
        if ($xxx && ($clanok['home'] == '0')) {} else {
            echo '<a href="' . $path . 'clanok/' . $clanok['clanok_id'] . '-' . normalizeClanokName($clanok['nazov']) . '">  ' . ODRAZKA . ' ' . ($count < 3 ? '<b>' . (mb_strlen($clanok['nazov']) > 25 ? mb_substr($clanok['nazov'], 0, 23) . '..' : $clanok['nazov']) . '</b>' : (mb_strlen($clanok['nazov']) > 27 ? mb_substr($clanok['nazov'], 0, 25) . '..' : $clanok['nazov'])) . '</a><br />';
            $count ++;
        }
    }
}

/**
 * *******************************************************************************************************************
 */
function echoHomeArticles($templateFile, $from = 0, $pocet = 999)
{
    global $path;

    $clanky = getTableRowsByAttribudes('clanok', '*', ' AND home = "1"', 'datetime', $from, $pocet);

    $result = psw_mysql_query('SELECT count(*) AS pocet FROM clanok WHERE home = "1"');
    $pocet_clankov = $result->fetch_assoc();

    $pocet_clankov = $pocet_clankov['pocet'];

    if ($pocet_clankov > 0) {
        foreach ($clanky as $clanok) {

            $story = Array();
            $story['params'] = 'id=' . $clanok['clanok_id'];
            $story['clanok_id'] = $clanok['clanok_id'];
            $story['title'] = $clanok['nazov'];
            $story['short_story'] = mb_str_replace('uploads/', $path . 'uploads/', mb_str_replace("\n", "<br />", getSmiles($clanok['short_text'])));
            $story['date'] = $clanok['datetime'];
            $story['author'] = $clanok['user'];
            $story['counter'] = $clanok['counter'];
            $story['comments_num'] = getNrOfComments($clanok['clanok_id']);

            if (is_file('public/templates/' . $templateFile)) {
                include ('public/templates/' . $templateFile);
            }
            echo $short_story_template;
        }

        $pocet_stranok = ceil($pocet_clankov / $pocet);

        echo '<div class="story_listing" >' . ((($from - $pocet) >= 0) ? '<a href="' . $path . 'index.php?section=home&amp;from=' . ($from - $pocet) . '" >&lt; Predchádzajúce</a>&nbsp;' : '');
        for ($i = 0; $i < $pocet_stranok; $i ++) {
            echo '<a href="' . $path . 'index.php?section=home&amp;from=' . $i * $pocet . '">' . ($from == $i * $pocet ? '<b>' : '') . ($i + 1) . '' . ($from == $i * $pocet ? '</b>' : '') . '</a>&nbsp;';
        }
        echo ((($pocet + $from) < $pocet_stranok * $pocet) ? '<a href="' . $path . 'index.php?section=home&amp;from=' . ($from + $pocet) . '" >Ďaľšie &gt;</a>' : '') . '</div>';
    } else {
        echo '<br /><br /><b>V tejto sekcii sa nenachádzajú žiadne články.</b>';
    }
}

/**
 * *******************************************************************************************************************
 */
function echoGaleryByArticleId($articleId)
{
    $clanok = getTableRow('clanok', 'clanok_id', $articleId);
    $galeryId = $clanok[0]['section_id'];
    if ($galeryId) {
        // echoGaleryByGaleryIdGeryBox2($galeryId);
        // echo '<div id="fotoalbum">';
        $_REQUEST['galeryId'] = $galeryId;
        // include('public/fotoalbum.php');
        // echo '</div>';
        echoGaleryByGaleryIdGeryBox($galeryId);
    }
}

/**
 * *******************************************************************************************************************
 */
function echoGaleryByGaleryIdGeryBox($galeryId)
{
    global $path;

    echo '';

    $section_id = $galeryId;
    /**
     * **************************************************************************************************************
     */
    $pocet_obrazkov_na_riadok = 3;
    $pocet_obrazkov_na_stranu = 18;
    /**
     * **************************************************************************************************************
     */

    // Limit odkial sa budu zobrazovat fotky
    if ($_REQUEST['limit']) {
        $limit = $_REQUEST['limit'];
    } else {
        $limit = 0;
    }

    $result = psw_mysql_query('SELECT * FROM section WHERE section_id = "' . $section_id . '" ');
    $section = $result->fetch_assoc();

    $destination = '' . $path . 'foto/' . $section['main_section'] . "/" . $section['sub_section'] . "/thumbs/";
    $destinationBig = '' . $path . 'foto/' . $section['main_section'] . "/" . $section['sub_section'] . "/";

    $result = psw_mysql_query('SELECT count(*) AS pocet FROM picture WHERE section_id = "' . $section_id . '" ');
    $pocet = $result->fetch_assoc();

    $pocet_stran = ceil($pocet['pocet'] / $pocet_obrazkov_na_stranu);
    $aktualna_strana = ceil(($limit / $pocet_obrazkov_na_stranu) + 1);

    $foto = getTableRows('picture', ' AND section_id = "' . $section_id . '"', 'date');

    if ($limit + $pocet_obrazkov_na_stranu > count($foto)) {
        $pokial_zobrazovat = count($foto);
    } else {
        $pokial_zobrazovat = $limit + $pocet_obrazkov_na_stranu;
    }

    echo '<div class="fotoalbum">
	      <a name="galery"></a>
        <b>' . $section['section_name'] . '</b><br />
        &nbsp;' . $aktualna_strana . '/' . $pocet_stran . '&nbsp;&nbsp;(počet fotografií: ' . $pocet['pocet'] . ')<br /><br />
        ';

    if ($limit > 0) {
        echo '&nbsp;<b>|</b>&nbsp;
	      <a href="' . $path . 'index.php?' . (isset($_REQUEST['section']) ? 'section=' . $_REQUEST['section'] . '&amp;' : '') . '' . (0 < ($_REQUEST['id']) ? 'id=' . $_REQUEST['id'] : '') . (0 < ($_REQUEST['galery']) ? '&amp;galery=' . $_REQUEST['galery'] : '') . '&amp;limit=' . ($limit - $pocet_obrazkov_na_stranu) . '#galery">
	       Predchádzajúca strana
	      </a>
	      &nbsp;<b>|</b>&nbsp;';
    }

    if ($limit + $pocet_obrazkov_na_stranu < count($foto)) {
        echo '&nbsp;<b>|</b>&nbsp;
				<a href="' . $path . 'index.php?' . (isset($_REQUEST['section']) ? 'section=' . $_REQUEST['section'] . '&amp;' : '') . '' . (0 < ($_REQUEST['id']) ? 'id=' . $_REQUEST['id'] : '') . (0 < ($_REQUEST['galery']) ? '&amp;galery=' . $_REQUEST['galery'] : '') . '&amp;limit=' . ($limit + $pocet_obrazkov_na_stranu) . '#galery">
				 Nasledujúca strana
				</a>
				&nbsp;<b>|</b>&nbsp;';
    }

    echo '<div style="display: none;">';
    for ($i = 0; $i < $limit; $i ++) {
        echo '<a href="' . $destinationBig . $foto[$i]['filename'] . '" rel="gb_imageset[foto]" title="' . validateForm($section['section_name'] . ($foto[$i]['text'] ? ': ' . $foto[$i]['text'] : '')) . '" >' . $i . '</a>';
    }
    echo '</div>';

    for ($i = $limit; $i < $pokial_zobrazovat;) {
        echo '
        <table>
	       <tr>';

        for ($j = 0; $j < $pocet_obrazkov_na_riadok; $j ++) {
            if (($i < count($foto)) && ($i < $pokial_zobrazovat)) {
                echo '
				<td>
				 <a href="' . $destinationBig . $foto[$i]['filename'] . '" rel="gb_imageset[foto]" title="' . validateForm($section['section_name'] . ($foto[$i]['text'] ? ': ' . $foto[$i]['text'] : '')) . '" >
				  <img src="' . $destination . $foto[$i]['filename'] . '" border="1" />
				 </a>
				</td>';
                $i ++;
            }
        }
        echo ' </tr>
        </table>';
    }
    echo '<div style="display: none;">';
    for ($i = $pokial_zobrazovat; $i < count($foto); $i ++) {
        echo '<a href="' . $destinationBig . $foto[$i]['filename'] . '" rel="gb_imageset[foto]" title="' . validateForm($section['section_name'] . ($foto[$i]['text'] ? ': ' . $foto[$i]['text'] : '')) . '" >' . $i . '</a>';
    }
    echo '</div></div>';
}

/**
 * *******************************************************************************************************************
 */
function echoGaleryByGaleryIdGeryBox2($galeryId)
{
    global $path;

    echo '<script type="text/javascript">
    var GB_ROOT_DIR = "' . $path . 'greybox/";
    //a();b();c();
  </script>';

    $section_id = $galeryId;
    /**
     * **************************************************************************************************************
     */
    $pocet_obrazkov_na_riadok = 3;
    $pocet_obrazkov_na_stranu = 18;
    /**
     * **************************************************************************************************************
     */

    // Limit odkial sa budu zobrazovat fotky
    if ($_REQUEST['limit']) {
        $limit = $_REQUEST['limit'];
    } else {
        $limit = 0;
    }

    $result = psw_mysql_query('SELECT * FROM section WHERE section_id = "' . $section_id . '" ');
    $section = $result->fetch_assoc();

    $destination = '' . $path . 'foto/' . $section['main_section'] . "/" . $section['sub_section'] . "/thumbs/";
    $destinationBig = '' . $path . 'foto/' . $section['main_section'] . "/" . $section['sub_section'] . "/";

    $result = psw_mysql_query('SELECT count(*) AS pocet FROM picture WHERE section_id = "' . $section_id . '" ');
    $pocet = $result->fetch_assoc();

    $pocet_stran = ceil($pocet['pocet'] / $pocet_obrazkov_na_stranu);
    $aktualna_strana = ceil(($limit / $pocet_obrazkov_na_stranu) + 1);

    $foto = getTableRows('picture', ' AND section_id = "' . $section_id . '"', 'date');

    echo '<div style="dispclay: none;">';
    for ($i = 0; $i < count($foto); $i ++) {
        echo '<a href="' . $destinationBig . $foto[$i]['filename'] . '" rel="gb_imageset[foto]" title="' . validateForm($section['section_name'] . ($foto[$i]['text'] ? ': ' . $foto[$i]['text'] : '')) . '" >' . $i . '</a>';
    }
    echo '</div>';
}

/**
 * *******************************************************************************************************************
 */
function validateForm($text)
{
    $text = mb_str_replace('"', '&quot;', $text);
    return $text;
}

/**
 * *******************************************************************************************************************
 */
function getNazovClankuFromId($clanok_id)
{
    $clanok = getTableRow('clanok', 'clanok_id', $clanok_id);
    $nazov = $clanok[0]['nazov'];
    return $nazov;
}

/**
 * *******************************************************************************************************************
 */
function getSectionNameFromClanokId($clanok_id)
{
    $result = psw_mysql_query('SELECT main_section FROM main_section WHERE main_section_id IN (SELECT main_section_id FROM clanok WHERE clanok_id = "' . $clanok_id . '" )');
    $name = $result->fetch_assoc();

    $name = $name['main_section'];
    return $name;
}

/**
 * *******************************************************************************************************************
 */
function incClanokReadCounter($id = 0)
{
    psw_mysql_query('UPDATE clanok SET counter = (counter+1) WHERE clanok_id = ' . $id . '');
}

/**
 * *******************************************************************************************************************
 */
function echoPartyfotoz($galery = false)
{

    // getall partylists
    global $path;

    if (0 < ($galery)) {
        echoGaleryByGaleryIdGeryBox($galery);
    } else {
        // foreach print partyfotoz
        $parties = getTableRows('section', ' AND main_section="partyfotoz" ', 'section_id');
        echo '
			<table class="big_story_table" cellspacing="0" cellpadding="0">
			 <tr>
			  <td class="story_head">
			   <h3>Party Fotoz</h3>
			  </td>
			 </tr>
			 <tr>
			  <td class="story_body"><br />';

        foreach ($parties as $party) {
            echo '<a href="' . $path . 'index.php?section=partyfotoz&amp;galery=' . $party['section_id'] . '"><b>' . $party['section_name'] . '</b></a><br />';
        }

        echo '<br />
        </td>
       </tr>
      </table>';
    }
}

/**
 * *******************************************************************************************************************
 */
function printPartyListSection()
{
    global $path;

    $partyList = Array();
    $is = false;

    $result = psw_mysql_query('SELECT * FROM partylist WHERE schvalene = "1" ORDER BY ordering ASC');
    while ($partyList = $result->fetch_assoc()) {

        $is = true;

        if (is_file('public/templates/' . $templateFile)) {
            include ('public/templates/' . $templateFile);
        }

        echo '
			 <table class="short_story_table" onclick="window.open(\'' . (($partyList['link'] != '') ? $partyList['link'] : 'image/4/' . $partyList['partylist_id'] . '.jpg') . '\', \'_blank\');" onmouseover="this.style.background=\'#f7f6f4\';" onmouseout="this.style.background=\'#ffffff\';" cellspacing="0" cellpadding="0">
			  <tr>
			   <td class="story_head">
			    <h3>' . $partyList['title'] . '</h3>
			   </td>
			  </tr>
			  <tr>
			   <td class="story_body">
				  <img style="border:1px solid #999999;" alt="' . $story['title'] . '" align="left" src="image/3/' . $partyList['partylist_id'] . '.jpg" />
			    ' . $partyList['text'] . '
			   </td>
			  </tr>
			  <tr>
			   <td class="story_foot">
          &nbsp;
			   </td>
			 </table>
		';
    }
    if (! $is) {
        echo '
      <br />Žiadna párty nieje vložená.<br /><br />
		';
    }
}

/**
 * *******************************************************************************************************************
 */
function printSearching()
{
    global $path;

    // hm, najprv si zistim v com mam vyhladaavat a potom budem vyhladavat
    $text = $_REQUEST['search_text'];

    if (($_REQUEST['in_nadpis'] == 'on') && ($text != '')) {
        echo '<div class="heading">Nájdené výsledky v nadpisoch:</div>';
        $clanky = getTableRowsByAttribudes('clanok', 'clanok_id', ' AND nazov LIKE "%' . $text . '%"', 'datetime');
        $is = false;
        foreach ($clanky as $clanok) {
            $is = true;
            echoClanokList($clanok['clanok_id']);
        }
        if (! $is) {
            echo '<b>Neboli nájdené žiadne výsledny</b><br />';
        }
    }
    if (($_REQUEST['in_clanok'] == 'on') && ($text != '')) {
        echo '<br /><div class="heading">Nájdené výsledky v článkoch:</div>';
        $clanky = getTableRowsByAttribudes('clanok', 'clanok_id', ' AND big_text LIKE "%' . $text . '%"', 'datetime');
        foreach ($clanky as $clanok) {
            $is = true;
            echoClanokList($clanok['clanok_id']);
        }
        if (! $is) {
            echo '<b>Neboli nájdené žiadne výsledny</b><br />';
        }
    }
    if (($_REQUEST['in_komentar'] == 'on') && ($text != '')) {
        echo '<br /><div class="heading">Nájdené výsledky v komentároch:</div>';
        $komentare = getTableRows('comment', ' AND text LIKE "%' . $text . '%" OR nick LIKE "%' . $text . '%" GROUP BY clanok_id', 'datetime');
        foreach ($komentare as $komentar) {
            $is = true;
            echoClanokList($komentar['clanok_id']);
        }
        if (! $is) {
            echo '<b>Neboli nájdené žiadne výsledny</b><br />';
        }
    }
    if (($_REQUEST['in_nadpis'] != 'on') && ($_REQUEST['in_clanok'] != 'on') && ($_REQUEST['in_komentar'] != 'on')) {
        echo '<br /><div class="heading">Vyberte sekciu v ktorej chcete vyhľadávať</div><br />
		      <a href="' . $path . 'index.php">Hlavná stránka</a>';
    }
    if (mb_strlen($text) < 1) {
        echo '<br /><div class="heading">Vložte vyhľadávací reťazec</div><br />
		      <a href="' . $path . 'index.php">Hlavná stránka</a>';
    }
}

/**
 * *******************************************************************************************************************
 */
function mb_str_replace($needle, $replacement, $haystack)
{
    if (SQL_HOST == "mysql.downhillaction.com") {
        return str_replace($needle, $replacement, $haystack);
    } else {
        $needle_len = mb_strlen($needle);
        $replacement_len = mb_strlen($replacement);
        $pos = mb_strpos($haystack, $needle);
        while ($pos !== false) {
            $haystack = mb_substr($haystack, 0, $pos) . $replacement . mb_substr($haystack, $pos + $needle_len);
            $pos = mb_strpos($haystack, $needle, $pos + $replacement_len);
        }
        return $haystack;
    }
}

/*
 * function mb_str_replace($haystack, $search,$replace, $offset=0,$encoding='auto'){
 * $len_sch=mb_strlen($search,$encoding);
 * $len_rep=mb_strlen($replace,$encoding);
 *
 * while (($offset=mb_strpos($haystack,$search,$offset,$encoding))!==false){
 * $haystack=mb_substr($haystack,0,$offset,$encoding)
 * .$replace
 * .mb_substr($haystack,$offset+$len_sch,1000,$encoding);
 * $offset=$offset+$len_rep;
 * if ($offset>mb_strlen($haystack,$encoding))break;
 * }
 * return $haystack;
 * }
 */
/**
 * *******************************************************************************************************************
 */
function getSectionByClanokId($clanok_id)
{
    $result = psw_mysql_query('SELECT main_section FROM main_section m, clanok c WHERE c.clanok_id = "' . $clanok_id . '" AND m.main_section_id = c.main_section_id ');
    $section = $result->fetch_assoc();

    $section = $section['main_section'];

    return $section;
}

/**
 * *******************************************************************************************************************
 */
function isSelectedSection($section)
{
    if (isset($_REQUEST['id'])) {
        $sectionDb = getSectionByClanokId($_REQUEST['id']);
        if (0 < (strpos($sectionDb, $section))) {
            return true;
        } else {
            return false;
        }
    } else {
        if (0 < (strpos($_REQUEST['section'], $section))) {
            return true;
        } else {
            return false;
        }
    }
}

/**
 * *******************************************************************************************************************
 */
function normalizeFilename($name, $truncExt = false)
{
    if ($truncExt && mb_strrpos($name, '.') !== FALSE) {
        $name = mb_substr($name, 0, mb_strrpos($name, '.'));
    }

    static $tbl = array(
        "\xc3\xa1" => "a",
        "\xc3\xa4" => "a",
        "\xc4\x8d" => "c",
        "\xc4\x8f" => "d",
        "\xc3\xa9" => "e",
        "\xc4\x9b" => "e",
        "\xc3\xad" => "i",
        "\xc4\xbe" => "l",
        "\xc4\xba" => "l",
        "\xc5\x88" => "n",
        "\xc3\xb3" => "o",
        "\xc3\xb6" => "o",
        "\xc5\x91" => "o",
        "\xc3\xb4" => "o",
        "\xc5\x99" => "r",
        "\xc5\x95" => "r",
        "\xc5\xa1" => "s",
        "\xc5\xa5" => "t",
        "\xc3\xba" => "u",
        "\xc5\xaf" => "u",
        "\xc3\xbc" => "u",
        "\xc5\xb1" => "u",
        "\xc3\xbd" => "y",
        "\xc5\xbe" => "z",
        "\xc3\x81" => "A",
        "\xc3\x84" => "A",
        "\xc4\x8c" => "C",
        "\xc4\x8e" => "D",
        "\xc3\x89" => "E",
        "\xc4\x9a" => "E",
        "\xc3\x8d" => "I",
        "\xc4\xbd" => "L",
        "\xc4\xb9" => "L",
        "\xc5\x87" => "N",
        "\xc3\x93" => "O",
        "\xc3\x96" => "O",
        "\xc5\x90" => "O",
        "\xc3\x94" => "O",
        "\xc5\x98" => "R",
        "\xc5\x94" => "R",
        "\xc5\xa0" => "S",
        "\xc5\xa4" => "T",
        "\xc3\x9a" => "U",
        "\xc5\xae" => "U",
        "\xc3\x9c" => "U",
        "\xc5\xb0" => "U",
        "\xc3\x9d" => "Y",
        "\xc5\xbd" => "Z",
        " " => "_",
        "/" => "_",
        "&amp;" => "_",
        "?" => "_"
    );
    $name = strtr(mb_strtolower($name), $tbl); // odstraneni akcentu + nahrazeni nekterych znaku a prevedeni na male znaky

    $tbl2 = array(
        '.',
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '8',
        '9',
        '0',
        'a',
        'b',
        'c',
        'd',
        'e',
        'f',
        'g',
        'h',
        'i',
        'j',
        'k',
        'l',
        'm',
        'n',
        'o',
        'p',
        'q',
        'r',
        's',
        't',
        'u',
        'v',
        'w',
        'x',
        'y',
        'z',
        '_',
        '-'
    );
    for ($x = 0; $x < mb_strlen($name); $x ++) {
        $ch = mb_substr($name, $x, 1);
        if (! in_array($ch, $tbl2)) {
            $name = str_replace($ch, '#', $name); // nahrazeni neznamych znaku #
        }
    }

    $name = str_replace('#', '', $name); // odstraneni #
    $name = ereg_replace('(-[-]+)', '-', $name); // odstraneni dvojitych -
    $name = trim($name, " -\n\t\r\0"); // orezani

    return $name;
}

/**
 * *******************************************************************************************************************
 */
function normalizeClanokName($name, $truncExt = false)
{
    if ($truncExt && mb_strrpos($name, '.') !== FALSE) {
        $name = mb_substr($name, 0, mb_strrpos($name, '.'));
    }

    static $tbl = array(
        "\xc1\xe1" => "a",
        "\xc9\xe9" => "e",

        "\xc3\xa1" => "a",
        "\xc3\xa4" => "a",
        "\xc4\x8d" => "c",
        "\xc4\x8f" => "d",
        "\xc3\xa9" => "e",
        "\xc4\x9b" => "e",
        "\xc3\xad" => "i",
        "\xc4\xbe" => "l",
        "\xc4\xba" => "l",
        "\xc5\x88" => "n",
        "\xc3\xb3" => "o",
        "\xc3\xb6" => "o",
        "\xc5\x91" => "o",
        "\xc3\xb4" => "o",
        "\xc5\x99" => "r",
        "\xc5\x95" => "r",
        "\xc5\xa1" => "s",
        "\xc5\xa5" => "t",
        "\xc3\xba" => "u",
        "\xc5\xaf" => "u",
        "\xc3\xbc" => "u",
        "\xc5\xb1" => "u",
        "\xc3\xbd" => "y",
        "\xc5\xbe" => "z",
        "\xc3\x81" => "A",
        "\xc3\x84" => "A",
        "\xc4\x8c" => "C",
        "\xc4\x8e" => "D",
        "\xc3\x89" => "E",
        "\xc4\x9a" => "E",
        "\xc3\x8d" => "I",
        "\xc4\xbd" => "L",
        "\xc4\xb9" => "L",
        "\xc5\x87" => "N",
        "\xc3\x93" => "O",
        "\xc3\x96" => "O",
        "\xc5\x90" => "O",
        "\xc3\x94" => "O",
        "\xc5\x98" => "R",
        "\xc5\x94" => "R",
        "\xc5\xa0" => "S",
        "\xc5\xa4" => "T",
        "\xc3\x9a" => "U",
        "\xc5\xae" => "U",
        "\xc3\x9c" => "U",
        "\xc5\xb0" => "U",
        "\xc3\x9d" => "Y",
        "\xc5\xbd" => "Z",
        " " => "-",
        "/" => "-",
        "&amp;" => "-",
        "?" => "-"
    );
    $name = strtr(mb_strtolower($name), $tbl); // odstraneni akcentu + nahrazeni nekterych znaku a prevedeni na male znaky

    $tbl2 = array(
        '.',
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '8',
        '9',
        '0',
        'a',
        'b',
        'c',
        'd',
        'e',
        'f',
        'g',
        'h',
        'i',
        'j',
        'k',
        'l',
        'm',
        'n',
        'o',
        'p',
        'q',
        'r',
        's',
        't',
        'u',
        'v',
        'w',
        'x',
        'y',
        'z',
        '_',
        '-'
    );
    for ($x = 0; $x < mb_strlen($name); $x ++) {
        $ch = mb_substr($name, $x, 1);
        if (! in_array($ch, $tbl2)) {
            $name = str_replace($ch, '#', $name); // nahrazeni neznamych znaku #
        }
    }

    $name = str_replace('#', '', $name); // odstraneni #
                                         // $name = ereg_replace('(-[-]+)', '-', $name); // odstraneni dvojitych -
    $name = trim($name, " -\n\t\r\0"); // orezani

    return $name;
}

/**
 * *******************************************************************************************************************
 */
function movedir($src, $dest)
{
    // nacitam si setky subory a potom v cykle najprv skopirujem a potom zmazem stary
    $src_files = scandir($src);
    $temp = Array();
    $i = 0;
    foreach ($src_files as $src_file) {
        if (($src_file != '.') && ($src_file != '..') && ($src_file != 'thumbs')) {
            $temp[$i] = $src_file;
            $i ++;
        }
    }
    $src_files = $temp;

    foreach ($src_files as $src_file) {
        copy($src . $src_file, $dest . $src_file);
        unlink($src . $src_file);
    }
}

/**
 * *******************************************************************************************************************
 */
function regenerateAudioPlaylist()
{

    // unlink('../mp3/playlist.xml');
    $file = fopen('../mp3/playlist.xml', 'w');

    fwrite($file, '<?xml version="1.0" encoding="UTF-8"?>
<xml>');

    $songs = getTableRows('audio_playlist', '', ' ord ASC ');
    foreach ($songs as $song) {
        fwrite($file, '<track>
		<path>' . $song['text'] . '</path>
		<title>' . $song['href'] . '</title>
	</track>');
    }

    fwrite($file, '</xml>');
}

/**
 * *******************************************************************************************************************
 */
function echoVideoPlaylist()
{
    global $path;

    $videos = getTableRows('video_playlist', '', ' ord ASC ');
    foreach ($videos as $video) {

        echo '<span onClick="play(\'' . $video['text'] . '\');">» ' . $video['href'] . '</span><br />';
    }
}

/**
 * *******************************************************************************************************************
 */
function getTemplateForSection($section)
{
    $template = getTableRow('main_section', 'main_section', $section);

    return $template[0]['template'];
}

/**
 * *******************************************************************************************************************
 */
function echoZoSveta()
{
    $result = psw_mysql_query('SELECT * FROM zo_sveta ORDER BY id DESC LIMIT ' . ZO_SVETA_POCET_SPRAV_NA_STRANU . ' ');
    while ($sprava = $result->fetch_assoc()) {

        echo '<div class="zo_sveta_msg"><a href="' . $sprava['href'] . '"	target="_blank">' . $sprava['text'] . '</a><br /></div>';
    }
}

/**
 * *******************************************************************************************************************
 */
function getPath()
{
    $pathArr = explode('/', $_SERVER['REQUEST_URI']);

    if ($pathArr[count($pathArr) - 2] == 'clanok') {
        $path = '../';
    } else if ($pathArr[count($pathArr) - 2] == 'sekcia') {
        $path = '../';
    } else {
        $path = '';
    }

    if ($pathArr[count($pathArr) - 2] == 'public') {
        $path = '../';
    }

    return $path;
}

/**
 * *******************************************************************************************************************
 */
function psw_mysql_query($sql)
{
    $sql = str_replace("'", "&quot;", $sql);

    /*
     * $query = mysql_query($sql);
     * echo mysql_error();
     * return $query;
     */

    global $connId;

    return $connId->query($sql);
}

?>