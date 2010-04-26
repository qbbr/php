<?php
/**
 * кэширование
 * @author Sokolov Innokenty (c) 2010
 */

class cache {

	/**
	 * директория для хранения кэша
	 * @var string
	 */
	static public $dir = '';


	/**
	 * записать в кэш
	 * @param string $key ключ кэша
	 * @param mix $value значение
	 * @return bool
	 */
	static public function set($key, $value) {
		$path = self::get_path($key);

		if (!is_file($path) && is_dir(self::$dir) && is_writable(self::$dir)) {
			return @file_put_contents($path, @serialize($value));
		}

		return false;
	}


	/**
	 * прочитать кэш
	 * @param string $key ключ кэша
	 * @param int $expire_in_minuts время жизни кэша в минутах
	 * @return bool
	 */
	static public function get($key, $expire_in_minuts) {
		$path = self::get_path($key);

		if (!is_file($path)) return false;

		if (filemtime($path) + 60 * $expire_in_minuts > time()) {
			return @unserialize(@file_get_contents($path));
		} else {
			@unlink($path);
			return false;
		}
	}

	
	/**
	 * получение пути до кэша
	 * @param string $key ключ кэша
	 * @return string
	 */
	static private function get_path($key) {
		return rtrim(self::$dir, "/\\").DIRECTORY_SEPARATOR.md5($key).'.tmp';
	}

}
?>