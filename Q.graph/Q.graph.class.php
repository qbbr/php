<?php
/**
 * graph
 * @author Sokolov Innokenty
 * @copyright qbbr, 2010
 */

/*
$items = array(
	'name1' => 71,
	'name2' => 21,
	'name3' => 11,
	'name4' => 41,
	'name5' => 22,
	'name6' => 40,
	'name7' => 42,
	'name8' => 55,
	'name8' => 6
);
graph::pie($items);
 */

class graph {

	static public function pie($items) {
		$smooth_lvl = 2;
		$img_width = 500 * $smooth_lvl;
		$img_height = 500 * $smooth_lvl;
		$background_color = '';
		$height_3d = 20 * $smooth_lvl;

		$color_pallet = array("BDA9C7", "5A7291", "E6DF00", "03A616", "9E3B36", "50553F", "282127", "D8E315", "FF6505");
	
		$all_count = 0;
		foreach ($items as $value) {
			$all_count += $value;
		}

		// dolya v pie
		$last_dolya = 0;

		foreach ($items as $item_name => $value) {
			$dolya = round($value * 360 / $all_count, 2) + $last_dolya;
			if ($dolya > 359) $dolya = 360;
			$items[$item_name] = $dolya;
			$last_dolya = $dolya;
		}

		header('Content-type: image/png');

		$img = imagecreatetruecolor($img_width, $img_height);
		$background = imagecolorallocate($img, 255, 255, 255);
		imagefill($img, 0, 0, $background);

		$cx = $img_width / 2;
		$cy = $img_height / 2;
		$graph_width = $img_width;
		$graph_height = $img_height / 2;

		for ($i = $cy + $height_3d; $i > $cy; $i--) {
			$start = 0;
			$p = 0;
			foreach ($items as $item_name => $value) {
				$end = $value;
				$color_shadow = self::hex_shadow($img, $color_pallet[$p]);
				imagefilledarc($img, $cx, $i, $graph_width, $graph_height, $start, $end, $color_shadow, IMG_ARC_PIE);
				$start = $end;
				$p++;
			}
		}

		$start = 0;
		$p = 0;
		foreach ($items as $item_name => $value) {
			$end = $value;
			$color = self::hex($img, $color_pallet[$p]);
			imagefilledarc($img, $cx, $cy, $graph_width, $graph_height, $start, $end , $color, IMG_ARC_PIE);
			$start = $end;
			$p++;
		}

		$new_img_width = $img_width / $smooth_lvl;
		$new_img_height = $img_height / $smooth_lvl;
		$img_smooth = imagecreatetruecolor($new_img_width, $new_img_height);
		imagecopyresampled($img_smooth, $img, 0, 0, 0, 0, $new_img_width, $new_img_height, $img_width, $img_height);

		imagepng($img_smooth);
	}

	static private function hex($img, $hex) {
		$r = hexdec(substr($hex, 0, 2));
		$g = hexdec(substr($hex, 2, 2));
		$b = hexdec(substr($hex, 4, 2));
		
		return imagecolorallocate($img, $r, $g, $b);
	}

	static private function hex_shadow($img, $hex) {
		$r = hexdec(substr($hex, 0, 2));
		$g = hexdec(substr($hex, 2, 2));
		$b = hexdec(substr($hex, 4, 2));

		$r = ($r > 99) ? $r -= 100 : 0;
		$g = ($g > 99) ? $g -= 100 : 0;
		$b = ($b > 99) ? $b -= 100 : 0;
		
		return imagecolorallocate($img, $r, $g, $b);
	}

}
?>