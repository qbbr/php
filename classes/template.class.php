<?php
/**
 * шаблонизатор
 * @author Sokolov Innokenty (c) 2010
 */

class template {

	/**
	 * вывод ошибок
	 * @var bool
	 */
	public $error_reporting = false;

	/**
	 * директория с шаблонами
	 * @var string
	 */
	private $tmpl_dir = '/var/www/pub.tema.local.q/__app__/__modules__/role/views/';

	/**
	 * шаблон (полный путь)
	 * @var string
	 */
	private $tmpl_file = '';

	private $variables = array();


	/**
	 * создаём шаблонизатор
	 * @param string $tmpl_name название шаблона
	 * @param string $tmpl_dir[optional] директория где лежит шаблон
	 */
	public function __construct($tmpl_name, $tmpl_dir = null) {
		if (isset($tmpl_dir)) $this->tmpl_dir = $tmpl_dir;
		$this->tmpl_dir = rtrim($this->tmpl_dir, "/\\");

		$tmpl_file = $this->tmpl_dir.DIRECTORY_SEPARATOR.$tmpl_name.'.tmpl.php';

		if (!is_file($tmpl_file)) {
			trigger_error("template module say `$tmpl_file` not found!", E_USER_ERROR);
		}

		$this->tmpl_file = $tmpl_file;
	}


	/**
	 * обработка шаблона
	 * @return text
	 */
	private function redner() {
		if (!empty($this->variables)) extract($this->variables);

		if ($this->error_reporting) error_reporting(E_ALL);
		else error_reporting(0);

		ob_start();
		include $this->tmpl_file;
		$f = ob_get_contents();
		ob_end_clean();

		return $f;
	}


	/**
	 * назначение переменной
	 * @param <type> $name название переменной
	 * @param <type> $value значение
	 */
	public function __set($name, $value) {
		$this->variables[$name] = $value;
	}


	/**
	 * вывод шаблона
	 * @return text
	 */
	public function __toString() {
		return $this->redner();
	}

}
?>