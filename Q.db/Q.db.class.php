<?php
/**
 * работа с базой данных MySQL
 * @author Sokolov Innokenty (c) 2010
 */

class db {

	static private $obj = null;
	static private $config = array();

	/**
	 * debug
	 * @var bool
	 */
	static public $debug = false;

	/**
	 * лог debug`a
	 * @var array
	 */
	static public $query_log = array();

	/**
	 * суммарное время выполнение всех запроса
	 * @var float
	 */
	static public $all_query_time = 0;


	/**
	 * получение MySQL обьекта
	 * @static
	 * @return obj
	 */
	static private function getObj() {
		if (empty(self::$obj)) {
			self::$obj = self::connect();
		}
		return self::$obj;
	}


	/**
	 * подключение к DB
	 * @static
	 * @return obj
	 */
	static private function connect() {
		if (empty(self::$config)) trigger_error("db config not found!", E_USER_WARNING);

		$obj = @mysql_connect(self::$config['db_host'].':'.self::$config['db_post'], self::$config['db_user'], self::$config['db_psswd']);
		if (!$obj) trigger_error('could not connect to server `'.self::$config['db_host'].'`', E_USER_WARNING);

		if (!@mysql_select_db(self::$config['db_name'], $obj)) trigger_error('could not set db `'.self::$config['db_name'].'`', E_USER_WARNING);

		mysql_query(" SET NAMES UTF8 ");

		return $obj;
	}


	/**
	 * назначение настроек
	 * @static
	 * @param array $config массив с настройками подключения
	 * @example array(
	 * 'db_host' => 'localhost',
	 * 'db_post' => 3306,
	 * 'db_name' => 'test_base',
	 * 'db_user' => 'user_name',
	 * 'db_psswd' => 'password
	 * ));
	 * @return bool
	 */
	static public function setConfig($config) {
		if (empty(self::$config)) {
			self::$config = $config;
			return true;
		}
		return false;
	}


	/**
	 * запрос к БД
	 * @static
	 * @param string $query SQL запрос
	 * @return mixed
	 */
	static public function query($query, $one_row = true) {
		if (self::$debug) $start_time = self::get_microtime(); // debug

		$result = mysql_query($query, self::getObj());

		// debug
		if (self::$debug) {
			$query_time = self::get_microtime() - $start_time;
			self::$all_query_time += $query_time;
			array_push(self::$query_log, array(
				'query' => $query,
				'result' => $result,
				'timestamp' => $query_time
			));
		}

		if ($result === true) {
			return true;
		} else if ($result === false) {
			return false;
		} else {
			if ($one_row === false) $num_rows = 2; // (принудительно) не одна строка
			else $num_rows = mysql_num_rows($result);

			switch ($num_rows) {
				case 0:
					return null;

				case 1: // одна строка
					if (mysql_num_fields($result) == 1) { // один столбец
						$r = mysql_fetch_row($result);
						return $r[0];
					} else { // несколько столбцов
						return mysql_fetch_assoc($result);
					}

				default: // много строк
					$r = array();
					while ($row = mysql_fetch_assoc($result)) {
						array_push($r, $row);
					}
					return $r;
			}
		}
	}


	/**
	 * запрос к БД
	 * @static
	 * @param string $query SQL запрос
	 * @return bool
	 */
	static public function exec($query) {
		if (self::$debug) $start_time = self::get_microtime(); // debug

		$result = mysql_query($query, self::getObj());

		// debug
		if (self::$debug) {
			$query_time = self::get_microtime() - $start_time;
			self::$all_query_time += $query_time;
			array_push(self::$query_log, array(
				'query' => $query,
				'result' => $result,
				'timestamp' => $query_time
			));
		}

		if ($result === true) return true;
		else return false;
	}


	/**
	 * получение auto_increment id из последнего запроса
	 * @static
	 * @return int
	 */
	static public function lastInsertId() {
		return mysql_insert_id(self::getObj());
	}


	/**
	 * получение времени с микросекундами для debug`а
	 * @static
	 * @return float
	 */
	static private function get_microtime() {
		$time = explode(" ", microtime());
		return ((float)$time[0] + (float)$time[1]);
	}

}
?>