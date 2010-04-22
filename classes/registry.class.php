<?php
/**
 * регистр для хранения временных данных
 * @author Sokolov Innokenty (c) 2010
 */

class registry {

	/**
	 * регистр
	 * @var array
	 */
	static private $registry = array();


	/**
	 * вставка данных в регистр
	 * @static
	 * @param string $key - ключ массива
	 * @param mixed $value - значение
	 * @return bool
	 */
	static public function set($key, $value) {
		if ($key && !isset(self::$registry[$key])) {
			self::$registry[$key] = $value;
			return true;
		}
		return false;
	}


	/**
	 * получение данных из регистра
	 * @static
	 * @param string $key1, $key2, $key3... - ключи массива
	 * @return mixed
	 */
	static public function get() {
		$args = func_get_args();

		$r = array();
		foreach ($args as $value) {
			if (empty($r) && isset(self::$registry[$value])) {
				$r = self::$registry[$value];
			} else if (isset($r[$value]) && is_array($r)) {
				$r = $r[$value];
			} else {
				return false;
			}
		}
		return $r;
	}

}
?>