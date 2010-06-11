<?php
/**
 * работа с базой данных MySQL
 *
 * @author Sokolov Innokenty, <qbbr@qbbr.ru>
 * @copyright Copyright (c) 2010, qbbr
 */

class db {

	/**
	 * MySQL обьект
	 * @staticvar resource
	 */
	static private $obj = null;

	/**
	 * массив с настройками подключения
	 * @see setConfig
	 * @staticvar array
	 */
	static private $config = array();

	/**
	 * порт по-умолчанию
	 * @staticvar int
	 */
	static private $db_port = 3306;

	/**
	 * debug
	 * @staticvar bool
	 */
	static public $debug = false;

	/**
	 * лог debug`a
	 * @staticvar array
	 */
	static public $query_log = array();

	/**
	 * суммарное время выполнение всех запроса
	 * @staticvar float
	 */
	static public $all_query_time = 0;


	/**
	 * получение MySQL обьекта
	 * @static
	 * @return obj
	 */
	static private function getObj() {
		if (!self::$obj) {
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
		if (empty(self::$config)) {
			throw new Exception("Database config not found!");
		}

		$db_port = isset(self::$config["db_port"]) ? self::$config["db_port"] : self::$db_port;

		$obj = @mysql_connect(self::$config["db_host"] . ":" . $db_port, self::$config["db_user"], self::$config["db_psswd"]);

		if (!$obj) {
			throw new Exception("Could not connect to server!");
		}

		if (!@mysql_select_db(self::$config["db_name"], $obj)) {
			throw new Exception("Could not select database!");
		}

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
		if (self::$debug) self::logs($query, $result, $start_time);

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
	 * кол-во строк
	 * @param str $query SQL запрос
	 * @return int
	 */
	static public function num_rows($query) {
		if (self::$debug) $start_time = self::get_microtime(); // debug

		$result = mysql_query($query, self::getObj());

		// debug
		if (self::$debug) self::logs($query, $result, $start_time);

		return @mysql_num_rows($result);
	}


	/**
	 * начать транзакцию
	 * @return bool
	 */
	static public function transaction_begin() {
		return self::query(" START TRANSACTION ");
	}


	/**
	 * зафиксировать
	 * @return bool
	 */
	static public function transaction_commit() {
		return self::query(" COMMIT ");
	}


	/**
	 * откат
	 * @return bool
	 */
	static public function transaction_rollback() {
		return self::query(" ROLLBACK ");
	}


	/**
	 * получение auto_increment id из последнего запроса
	 * @static
	 * @return int
	 */
	static public function last_insert_id() {
		return mysql_insert_id(self::getObj());
	}


	/**
	 * экранирование (mysql_real_escape_string)
	 * @param str $str строка, которая должна быть экранирована
	 * @return str
	 */
	static public function escape($str) {
		return mysql_real_escape_string($str, self::getObj());
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


	/**
	 * логирование запросов
	 * @param str $query SQL запрос
	 * @param resource $result результат
	 * @param float $start_time время старта
	 * @return bool
	 */
	static private function logs($query, $result, $start_time) {
		$query_time = self::get_microtime() - $start_time;
		self::$all_query_time += $query_time;
		array_push(self::$query_log, array(
			"query" => $query,
			"result" => $result,
			"timestamp" => $query_time
		));

		return true;
	}

}
?>