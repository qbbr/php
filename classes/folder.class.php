<?php
/**
 * работа с директориями
 * @author Sokolov Innokenty (c) 2010
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
	public function __construct($path = null, $create = false, $mod = false) {
		if (empty($path)) {
			trigger_error("folder.class.php - path to dir is empty", E_USER_ERROR);
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
	public function create($path, $mod = false) {
		if (empty($path) || file_exists($path)) {
			return false;
		}
		
		if (!$mod) {
			$mod = $this->chmod;
		}

		return @mkdir($path, $mod, true);
	}


	/**
	 * получение пути
	 * @return string
	 */
	public function pwd() {
		return $this->path;
	}

}
?>