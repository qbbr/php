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
	 * кодирование контента
	 * @var string
	 */
	private $encoding = "8bit";

	/**
	 * формат контента
	 * @var string
	 */
	private $content_type = "text/plain";

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
	 * прикреплённые файлы
	 * @var array
	 */
	private $attach = array();

	/**
	 * граница для прикреплённых файлов
	 * @var string
	 */
	private $boundary = "";


	public function __construct() {
		$this->boundary = "--".md5(uniqid("boundary"));
	}


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
	 * открытая копия
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
	 * @param bool $content_type_is_html[optional] сообщение в формате html?
	 * @return bool
	 */
	public function message($msg, $content_type_is_html = false) {
		if ($content_type_is_html) $this->content_type = "text/html";

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
	 * прикрепить файл
	 * @param string $file путь до файла
	 * @param string $file_name[optional] название файла
	 * @param string $file_type[optional] тип файла
	 * @param string $disposition[optional]
	 * @return bool
	 */
	public function attach($file, $file_name = null, $file_type = null, $disposition = "attachment") {
		if (!is_file($file)) return false;

		if (empty($file_type)) $file_type = mime_content_type($file); // "application/x-unknown-content-type"
		if (empty($file_name)) $file_name = basename($file);

		$this->attach[] = array($file, $file_name, $file_type, $disposition);

		return true;
	}


	/**
	 * отправка
	 * @return bool
	 */
	public function send() {
		if (empty($this->headers)) return false;

		$headers = $this->build_headers();

		$body = empty($this->attach) ? $this->message : $this->build_attach();

		return @mail($this->to, $this->subject, $body, $headers);
	}


	/**
	 * собираем прикреплённые файла
	 * @return string
	 */
	private function build_attach() {
		$body = "This is a multi-part message in MIME format.\n--$this->boundary\n";
		$body .= "Content-Type: ".$this->content_type."; charset=$this->charset\n";
		$body .= "Content-Transfer-Encoding: $this->encoding\n\n";
		$body .= $this->message;
		$body .= "\n";

		$attachz = array();

		foreach ($this->attach as $value) {
			$file = $value[0];
			$basename = $value[1];
			$file_type = $value[2];
			$disposition = $value[3];

			$attachz[] = "--$this->boundary\nContent-type: $file_type;\n name=\"$basename\"\nContent-Transfer-Encoding: base64\nContent-Disposition: $disposition;\n filename=\"$basename\"\n";
			$attachz[] = chunk_split(base64_encode(file_get_contents($file)));
		}

		$body .= implode(chr(13).chr(10), $attachz);

		return $body;
	}


	/**
	 * собираем заголовки
	 * @return string
	 */
	private function build_headers() {
		if (empty($this->headers["Reply-To"])) {
			$this->reply_to($this->headers["From"]);
		}

		if (empty($this->attach)) {
			$this->set_header("Content-Type", "$this->content_type; charset=".$this->charset);
		} else {
			$this->set_header("Content-Type", "multipart/mixed;\n boundary=\"$this->boundary\"");
		}

		$this->set_header("Mime-Version", "1.0");
		$this->set_header("X-Mailer", "PHP ".phpversion());
		$this->set_header("Content-Transfer-Encoding", $this->encoding);

		$headers = "";
		foreach ($this->headers as $key => $value) {
			$value = trim($value);
			$headers .= "$key: $value\n";
		}

		return $headers;
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

}
?>