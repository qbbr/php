<?php
/**
 * Q.cssmin - Cascading Style Sheets Minifier
 *
 * add to htaccess
 * RewriteRule ^.*\.css$ cssmin.php [L]
 *
 * @author Sokolov Innokenty
 * @copyright qbbr, 2010
 */

error_reporting(0);

define("BASE_DIR", dirname(__FILE__)); // root dir /
define("CACHE_DIR", BASE_DIR.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR); // if def cache = true; else = false
define("CACHE_EXPIRE_TIME", 3600 * 24 * 10); // 10 days

$file_URI = $_SERVER['REDIRECT_URL'];
$file_path = BASE_DIR.str_replace(array("\\", "/"), DIRECTORY_SEPARATOR, $file_URI);

if (!is_file($file_path)) return false;
header("Content-type: text/css");

/* cache read --- {{{ */
$cache_file = CACHE_DIR.md5($file_path).'.css';
if (is_file($cache_file) && filemtime($cache_file) + CACHE_EXPIRE_TIME > time()) {
	readfile($cache_file);
	exit();
} else {
	@unlink($cache_file);
}
/* }}} --- */

$file = file_get_contents($file_path);

$file = preg_replace("/\/\*[\d\D]*?\*\/|\t+/", " ", $file);
$file = str_replace(array("\n", "\r", "\t"), "", $file);
$file = preg_replace("/\s\s+/", " ", $file);
$file = preg_replace("/\s*({|}|;|:|,|\.|~|=|\+|>|<|\?)\s*/", "$1", $file);
$file = str_replace(";}", "}", $file);

/* cache write --- {{{ */
if (CACHE_DIR && is_dir(CACHE_DIR) && is_writable(CACHE_DIR)) {
	@file_put_contents($cache_file, $file);
}
/* }}} --- */

echo $file;
?>