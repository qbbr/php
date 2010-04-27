<?php
/**
 * работа с файлом
 *
 * @depends folder.class.php
 * @author Sokolov Innokenty, <qbbr@qbbr.ru>
 * @copyright Copyright (c) 2010, qbbr
 */

class file {

	/**
	 * название файла
	 * @var string
	 */
	public $name = "";

	/**
	 * путь до файла
	 * @var string
	 */
	private $path = "";

	/**
	 * информация о пути к файлу
	 * @var array
	 */
	private $info = array();

	/**
	 * handle файла, если он открыт
	 * @var resource
	 */
	private $handle = null;

	/**
	 * обьект директории, где находится файл
	 * @var obj
	 */
	private $folder = null;


	/**
	 * конструктор
	 * @param string $path путь до файла
	 * @param bool $create создать файл, если такового не существует
	 */
	public function __construct($path, $create = false) {
		if ($create === false && !is_file($path)) {
			trigger_error("file.class.php - path to file is wrong", E_USER_ERROR);
		}
		
		$this->path = $path;
		$this->folder = new folder(dirname($path), $create);
		$this->name = basename($path);

		if ($create === true) {
			$this->create();
		}
	}


	/**
	 * создание нового файла
	 * @return bool
	 */
	public function create() {
		$dir = $this->folder->pwd();

		if (is_dir($dir) && is_writable($dir)) {
			return @touch($this->path);
		}

		return false;
	}


	/**
	 * открытие файла
	 * @param string $mode тип доступа к файлу
	 * @return bool
	 */
	public function open($mode = "r") {
		$this->handle = @fopen($this->path, $mode);
		if (is_resource($this->handle)) {
			return true;
		}

		return false;
	}


	/**
	 * чтение файла
	 * @param string $mode тип доступа к файлу
	 * @return bool
	 */
	public function read($mode = 'rb') {
		if (!$this->open($mode)) {
			return false;
		}
		
		return @fread($this->handle, $this->size());
	}


	/**
	 * запись в файл
	 * @param string $data текст для записи
	 * @param string $mode тип доступа к файлу
	 * @return bool
	 */
	public function write($data, $mode = 'w') {
		if (!$this->open($mode)) {
			return false;
		}

		return @fwrite($this->handle, $data);
	}


	/**
	 * удалить текущий файл
	 * @return bool
	 */
	public function delete() {
		return @unlink($this->path);
	}


	/**
	 * закрыть текущий файл, если он открыт
	 * @return bool
	 */
	public function close() {
		if (is_resource($this->handle)) {
			return @fclose($this->handle);
		}
		return true;
	}


	/**
	 * информация о пути к файлу
	 * @return array
	 */
	public function info() {
		if (empty($this->info)) {
			$this->info = pathinfo($this->path);
		}

		return $this->info;
	}


	/**
	 * права доступа к файлу (chmod)
	 * @return int
	 */
	public function perms() {
		return substr(sprintf('%o', fileperms($this->path)), -4);
	}


	/**
	 * размер файла в байтах
	 * @return int
	 */
	public function size() {
		return filesize($this->path);
	}


	/**
	 * время последнего изменения
	 * @return int
	 */
	public function last_change() {
		return filemtime($this->path);
	}


	/**
	 * время последнего досупа
	 * @return int
	 */
	public function last_access() {
		return fileatime($this->path);
	}

}
?>