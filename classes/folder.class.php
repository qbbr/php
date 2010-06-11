<?php
/**
 * работа с директориями
 * 
 * @author Sokolov Innokenty, <qbbr@qbbr.ru>
 * @copyright Copyright (c) 2010, qbbr
 */

class folder {

	/**
	 * путь
	 * @var string
	 */
	private $path = "";

	/**
	 * права доступа
	 * @var int
	 */
	private $chmod = 0755;


	/**
	 * конструктор
	 * @param string $path путь
	 * @param bool $create создать директорию, если таковой нет
	 * @param int $mod права доступа
	 */
	public function __construct($path = false, $create = false, $mod = false) {
		if ($path !== false && empty($path)) {
			throw new Exception("Path to dir is empty");
		}

		$this->path = $path;

		if ($mod) {
			$this->chmod = $mod;
		}

		if ($create === true && !file_exists($path)) {
			$this->create($path, $this->chmod);
		}
	}


	/**
	 * создание новой директории
	 * @param string $path путь
	 * @param int $mod права доступа
	 * @return bool
	 */
	public function create($path = null, $mod = false) {
		if (empty($path)) $path = $this->path;

		if (empty($path) || file_exists($path)) {
			return false;
		}

		if (!$mod) {
			$mod = $this->chmod;
		}

		return @mkdir($path, $mod, true);
	}


	/**
	 * рекурсивное удаление директории
	 * @param string $path путь
	 * @return bool
	 */
	public function delete($path = null) {
		if (empty($path)) $path = $this->path;

		if (!file_exists($path)) return true;

		$path = $this->repair_path($path);

		foreach (scandir($path) as $item) {
			if ($item == "." || $item == "..") continue;

			$item = $path.DIRECTORY_SEPARATOR.$item;

			if (is_file($item) && !@unlink($item)) {
				return false;
			} else if (is_dir($item) && !$this->delete($item)) {
				return false;
			}
		}

		return @rmdir($path);
	}


	/**
	 * получение пути
	 * @return string
	 */
	public function pwd() {
		return $this->path;
	}


	/**
	 * удаляем правый слэш
	 * @param string $path путь
	 * @return string
	 */
	private function repair_path($path = null) {
		if (empty($path)) $path = $this->path;

		return rtrim($path, "/\\");
	}

}
?>