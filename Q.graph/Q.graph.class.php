<?php
/**
 * graph - рисование графиков
 * @author Sokolov Innokenty (c) 2010
 */

class graph {

	/**
	 * уровень размытия
	 * @var int
	 */
	static public $smooth_lvl = 3;

	/**
	 * ширина картинки
	 * @var int
	 */
	static public $img_width = 300;

	/**
	 * высота картинки
	 * @var int
	 */
	static public $img_height = 300;

	/**
	 * фон картинки
	 * @var hex
	 */
	static public $background_color = 'ffffff';

	/**
	 * высота 3D эффекта
	 * @var int
	 */
	static public $img_3d_effect = 20;


	/**
	 * рисуем график pie
	 * @param array $items массив данных для построения графика<br/>
	 * $items = array(
	 *	'var1' => 30,
	 *	'var2' => 10,
	 *	'var3' => 49
	 * );
	 * @param int $img_width[optional] ширина картинки
	 * @param int $img_height[optional] высота картинки
	 */
	static public function pie($items, $img_width = null, $img_height = null) {
		$img_width = isset($img_width) ? $img_width : self::$img_width;
		$img_height = isset($img_height) ? $img_height : self::$img_height;

		$img_width *= self::$smooth_lvl;
		$img_height *= self::$smooth_lvl;

		self::$img_3d_effect *= self::$smooth_lvl;

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

		$img = imagecreatetruecolor($img_width, $img_height);
		$background = self::hex($img, self::$background_color);
		imagefill($img, 0, 0, $background);

		$cx = $img_width / 2;
		$cy = $img_height / 2;
		$graph_width = $img_width;
		$graph_height = $img_height / 2;

		for ($i = $cy + self::$img_3d_effect; $i > $cy; $i--) {
			$start = $p = 0;
			foreach ($items as $item_name => $value) {
				$end = $value;
				$color_shadow = self::hex_shadow($img, $color_pallet[$p]);
				imagefilledarc($img, $cx, $i, $graph_width, $graph_height, $start, $end, $color_shadow, IMG_ARC_PIE);
				$start = $end;
				$p++;
			}
		}

		$start = $p = 0;
		foreach ($items as $item_name => $value) {
			$end = $value;
			$color = self::hex($img, $color_pallet[$p]);
			imagefilledarc($img, $cx, $cy, $graph_width, $graph_height, $start, $end , $color, IMG_ARC_PIE);
			$start = $end;
			$p++;
		}

		header('Content-type: image/png');

		if (self::$smooth_lvl > 1) {
			$new_img_width = $img_width / self::$smooth_lvl;
			$new_img_height = $img_height / self::$smooth_lvl;
			$img_smooth = imagecreatetruecolor($new_img_width, $new_img_height);
			imagecopyresampled($img_smooth, $img, 0, 0, 0, 0, $new_img_width, $new_img_height, $img_width, $img_height);
			imagepng($img_smooth);
		} else {
			imagepng($img);
		}
	}

	/**
	 * создание цвета
	 * @param obj $img - идентификатор картинки
	 * @param string $hex - hex цвет
	 * @return obj [imagecolorallocate]
	 */
	static private function hex($img, $hex) {
		$r = hexdec(substr($hex, 0, 2));
		$g = hexdec(substr($hex, 2, 2));
		$b = hexdec(substr($hex, 4, 2));

		return imagecolorallocate($img, $r, $g, $b);
	}

	/**
	 * создание цвета для теней
	 * @param obj $img - идентификатор картинки
	 * @param string $hex - hex цвет
	 * @return obj [imagecolorallocate]
	 */
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