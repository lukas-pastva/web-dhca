<?php
ini_set('display_errors', 1);
ini_set('arg_separator.output', '&amp;');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING); // 2039

date_default_timezone_set('UTC');

define("SQL_HOST", $_ENV['MYSQL_HOST']);
define("SQL_DBNAME", $_ENV['MYSQL_DATABASE']);
define("SQL_USERNAME", $_ENV['MYSQL_USER']);
define("SQL_PASSWORD", $_ENV['MYSQL_PASSWORD']);

define("SITENAME", 'Downhillaction.com');

$connId = mysqli_connect(SQL_HOST, SQL_USERNAME, SQL_PASSWORD, SQL_DBNAME);

// Velkosti a kvality obrazkov
define("THUMB_PICTURE_HEIGHT", 75);
define("THUMBNAIL_WIDTH", 50);
define("THUMB_PICTURE_QUALITY", 98);
define("BIG_PICTURE_WIDTH", 950);
define("BIG_PICTURE_WIDTH_2", 800);
define("BIG_WIDE_PICTURE_WIDTH", 1024);
define("BIG_PICTURE_QUALITY", 95);
define("FORUM_POCET_SPRAV_NA_STRANU", 20);
define("ZO_SVETA_POCET_SPRAV_NA_STRANU", 5);
define("PLAYLIST_POCET_NA_STRANU", 10);
define("POCET_OBRAZKOV_NA_RIADOK", 3);
define("POCET_OBRAZKOV_NA_STRANU", 18);

define("MINIMALNA_SIRKA_OBJEKTU", '40');
define("MINIMALNA_VYSKA_OBJEKTU", '10');
define("KOMENTARE_DLZKA_SPRAVY", '40');

define("ODRAZKA", '›'); // »
define("PARTNERI_TEXTOVO", 1);
define('DIGG', 'http://vybrali.sme.sk');

if (! function_exists('mb_strtolower')) {

    function mb_strtolower($str)
    {
        return strtolower($str);
    }
}
if (! function_exists('mb_strlen')) {

    function mb_strlen($str)
    {
        return strlen($str);
    }
}
if (! function_exists('mb_substr')) {

    function mb_substr($str, $a = false, $b = false)
    {
        return substr($str, $a, $b);
    }
}
if (! function_exists('mb_strpos')) {

    function mb_strpos($str, $a = false, $b = false)
    {
        return strpos($str, $a, $b);
    }
}

$notDisplayedSections = Array(
    '1' => 'hidden'
);

?>