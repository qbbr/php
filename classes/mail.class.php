<?php
/**
 * отправка почты
 * @author Sokolov Innokenty (c) 2010
 */

class mail {

	/**
	 * кодировка письма
	 * @var string
	 */
	public $charset = "utf8";

	/**
	 * кодирование контента письма
	 * @var string
	 */
	private $encoding = "8bit";

	/**
	 * заголовки
	 * @var array
	 */
	private $headers = array();

	/**
	 * кому
	 * @var string
	 */
	private $to = "";

	/**
	 * тема письма
	 * @var string
	 */
	private $subject = "";

	/**
	 * текст
	 * @var string
	 */
	private $message = "";


	/**
	 * установка заголовка
	 * @param string $key ключ
	 * @param string $value значение
	 * @return bool
	 */
	private function set_header($key, $value) {
		if (empty($key) || empty($value)) return false;

		$this->headers[$key] = $value;

		return true;
	}


	/**
	 * от кого
	 * @param string $mail почта
	 * @param string $name ФИО
	 * @return bool
	 */
	public function from($mail, $name = "") {
		return $this->set_header("From", "$name <$mail>");
	}


	/**
	 * обратный адрес
	 * @param string $mail почта
	 * @param string $name ФИО
	 * @return bool
	 */
	public function reply_to($mail, $name = "") {
		$mail = preg_replace("/.*<(.*)>/", "$1", $mail);
		return $this->set_header("Reply-To", "$name <$mail>");
	}


	/**
	 * кому
	 * @param string $mail почта
	 * @return bool
	 */
	public function to($mail) {
		$this->to = $this->parse_mail($mail);
		return true;
	}


	/**
	 * копия
	 * @param string $mail почта
	 * @return bool
	 */
	public function cc($mail) {
		return $this->set_header("Cc", $this->parse_mail($mail));
	}


	/**
	 * скрытая копия
	 * @param string $mail почта
	 * @return bool
	 */
	public function bcc($mail) {
		return $this->set_header("Bcc", $mail = $this->parse_mail($mail));
	}


	/**
	 * тема сообщения
	 * @param string $subject тема письма
	 * @return bool
	 */
	public function subject($subject) {
		$this->subject = $subject;

		return true;
	}


	/**
	 * текст сообщения
	 * @param string $msg сообщение
	 * @return bool
	 */
	public function message($msg) {
		$this->message = $msg;

		return true;
	}


	/**
	 * назначить приоритет важности письма
	 * @param int $priority приоритет от 1 (самый высокий) до 5 (самый низкий)
	 * @return bool
	 */
	public function priority($priority) {
		if (!is_numeric($priority)) return false;

		return $this->set_header("X-Priority", $priority);
	}


	/**
	 * отправка
	 * @return bool
	 */
	public function send() {
		if (empty($this->headers)) return false;

		$headers = $this->build_headers();

		return @mail($this->to, $this->subject, $this->message, $headers);
	}


	/**
	 * правим почту (запятую), если их несколько
	 * @param string $mail электронный адрес
	 * @return bool
	 */
	private function parse_mail($mail) {
		if (empty($mail)) return false;

		$mails = explode(",", $mail);
		for ($i = 0; $i < count($mails); $i++) {
			$mails[$i] = trim($mails[$i]);
		}
		return implode(", ", $mails);
	}


	/**
	 * собираем заголовки
	 * @return string
	 */
	private function build_headers() {
		if (empty($this->headers["Reply-To"])) {
			$this->reply_to($this->headers["From"]);
		}

		$this->set_header("Mime-Version", "1.0");
		$this->set_header("X-Mailer", "PHP ".phpversion());

		$headers = "";
		foreach ($this->headers as $key => $value) {
			$value = trim($value);
			$headers .= "$key: $value\n";
		}
		$headers .= "Content-Type: text/plain; charset=".$this->charset."\n";
		$headers .= "Content-Transfer-Encoding: ".$this->encoding;

		return $headers;
	}

}
?>