<?php
/**
 * магическая обработка картинок на лету
 *
 * add to htacces
 * RewriteRule ^(.*)(\.jpg|\.jpeg|\.bmp|\.png|\.gif)(\.magic)$ path_to_dir/Q.magicImage.php [L]
 *
 * @param int width - ширина картинке на выходе (optional if height)
 * @param int height - высота картинке на выходе (optional if width)
 * @param bool m - подгоняет по меньшей грани (optional = false) [need width && height]
 * @param bool wp - без учёта пропорции (optional = false)
 * @author Sokolov Innokenty
 * @copyright qbbr, 2010
 */

define("BASE_DIR", dirname(dirname(__FILE__))); // root dir /
define("CACHE_DIR", BASE_DIR.DIRECTORY_SEPARATOR.'etc'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR); // if def cache = true; else = false
define("COPYRIGHT_PIC", BASE_DIR.DIRECTORY_SEPARATOR.'etc'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'copyright.png'); // optional
define("COPYRIGHT_OPACITY", 100); // [0 - 100]

function resize($fileURI, $maxWidth = null, $maxHeight = null, $m = false, $wp = false) {
	if (!$fileURI) return false;

	$no_work = false;
	$file = BASE_DIR.str_replace(array("\\", "/"), DIRECTORY_SEPARATOR, $fileURI);

	if (!is_file($file)) return false;

	$properties = getimagesize($file);
	$width = $properties[0];
	$height = $properties[1];
	$mime_type = $properties['mime'];

	header("Content-type: ".$mime_type);

	if (CACHE_DIR) {
		$t = explode(".", $fileURI);
		$file_type = end($t);
		$file_name = basename($file);
		$cache_file = CACHE_DIR.md5($file_name.$maxWidth.$maxHeight.$m.$wp).'.'.$file_type;

		if (is_file($cache_file)) {
			die(file_get_contents($cache_file));
		}
	}

	if (isset($maxWidth, $maxHeight)) {
		if ($width > $maxWidth || $height > $maxHeight) {
			if ($wp) {
				$newWidth = $maxWidth;
				$newHeight = $maxHeight;
			} else {

				if ( ($width > $maxWidth && $width > $height && $maxWidth < $maxHeight) || ($m && $width > $height) ) {
					$p = ($maxWidth * 100) / $width;
					$newWidth = $maxWidth;
					$newHeight = ($height * $p) / 100;
				} else {
					$p = ($maxHeight * 100) / $height;
					$newHeight = $maxHeight;
					$newWidth = ($width * $p) / 100;
				}

			}
		} else {
			$no_work = true;
		}
	} else if (isset($maxWidth)) {
		if ($width > $maxWidth) {
			$p = ($maxWidth * 100) / $width;
			$newWidth = $maxWidth;
			$newHeight = ($height * $p) / 100;
		} else {
			$no_work = true;
		}
	} else if (isset($maxHeight)) {
		if ($height > $maxHeight) {
			$p = ($maxHeight * 100) / $height;
			$newHeight = $maxHeight;
			$newWidth = ($width * $p) / 100;
		} else {
			$no_work = true;
		}
	} else {
		$no_work = true;
	}

	if ($no_work) {
		echo file_get_contents($file);
		return true;
	}

	$image_p = imagecreatetruecolor($newWidth, $newHeight);

	if ($mime_type == "image/jpeg" ) {
		$image = imagecreatefromjpeg($file);
	} else if ($mime_type == "image/png") {
		$image = imagecreatefrompng($file);
	} else if ($mime_type == "image/gif") {
		$image = imagecreatefromgif($file);
	}
	imagecopyresampled($image_p, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

	/* copyright --- {{{ */
	if (COPYRIGHT_PIC) {
		if (is_file(COPYRIGHT_PIC)) {
			$ci = getimagesize(COPYRIGHT_PIC);
			$c = imagecreatefrompng(COPYRIGHT_PIC);
			imagecopymerge($image_p, $c, $newWidth-$ci[0], $newHeight-$ci[1], 0, 0, $ci[0], $ci[1], COPYRIGHT_OPACITY);
		}
	}
	/* }}} --- */

	ob_start();
	if ($mime_type == "image/jpeg" ) {
		imagejpeg($image_p, null, 100);
	} else if ($mime_type == "image/png") {
		imagepng($image_p);
	} else if ($mime_type == "image/gif") {
		imagegif($image_p);
	}
	$contents = ob_get_contents();

	/* cache --- {{{ */
	if (CACHE_DIR && is_dir(CACHE_DIR) && is_writable(CACHE_DIR)) {
		$f = fopen($cache_file, 'w');
		fwrite($f, $contents);
		fclose($f);
	}

	/* }}} --- */

	imagedestroy($image_p);
}

$file = $_SERVER['REDIRECT_URL'];
$file = substr($file, 0, strlen($file) - 6);

$width = isset($_GET['width']) ? $_GET['width'] : null;
$height = isset($_GET['height']) ? $_GET['height'] : null;
$m = isset($_GET['m']) ? true : false;
$wp = isset($_GET['wp']) ? true : false;

resize($file, $width, $height, $m, $wp);
?>