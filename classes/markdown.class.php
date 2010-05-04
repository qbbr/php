<?php
/**
 * markdown - test
 *
 * @author Sokolov Innokenty, <qbbr@qbbr.ru>
 * @copyright Copyright (c) 2010, qbbr
 */

class markdown {

	static $file_content = "";
	
	static public function parse($file) {
		if (!is_file($file)) return false;

		self::$file_content = file_get_contents($file);

		self::parse_headers();

		self::parse_list();

		self::parse_code();


  	return self::$file_content;
 	}

	static private function parse_headers() {
		// h1 - bottom line
		self::$file_content = preg_replace("/(.*)\n={3,}/", "<h1>$1</h1>", self::$file_content);
		// h2 - bottom line
		self::$file_content = preg_replace("/(.*)\n-{3,}/", "<h2>$1</h2>", self::$file_content);

		// sharp h1 - h6
		self::$file_content = preg_replace("/^######(.*)/m", "<h6>$1</h6>", self::$file_content);
		self::$file_content = preg_replace("/^#####(.*)/m", "<h5>$1</h5>", self::$file_content);
		self::$file_content = preg_replace("/^####(.*)/m", "<h4>$1</h4>", self::$file_content);
		self::$file_content = preg_replace("/^###(.*)/m", "<h3>$1</h3>", self::$file_content);
		self::$file_content = preg_replace("/^##(.*)/m", "<h2>$1</h2>", self::$file_content);
		self::$file_content = preg_replace("/^#(.*)$/m", "<h1>$1</h1>", self::$file_content);
 	}

	static private function parse_list() {
		// ol
		self::$file_content = preg_replace("/ \d (.*)\n/", "<li>$1</li>\n", self::$file_content);
		self::$file_content = preg_replace("/(<li>.*<\/li>\n)+/", "<ol>\n$0</ol>\n", self::$file_content);

		// ul
		self::$file_content = preg_replace("/ \* (.*)\n/", "<li>$1</li>\n", self::$file_content);
		self::$file_content = preg_replace("/(?<!>\n)(<li>.*<\/li>\n)+/", "<ul>\n$0</ul>\n", self::$file_content);
	}

	static private function parse_code() {
		self::$file_content = preg_replace("/(\t.*\n?)+/", "<pre><code>$0</code></pre>\n", self::$file_content);
	}
	
/*
	static private function parse_code() {
		//self::$file_content = preg_replace("!\t(.*)\n!", "<pre><code>$1</code></pre>", self::$file_content);
		self::$file_content = preg_replace("!\t(.*)\n!", "<code>$1</code>", self::$file_content);
		self::$file_content = preg_replace("!<code>([\t\n]?.*[\t\n]?)</code>!", "<pre>$1</pre>", self::$file_content);
		//self::$file_content = preg_replace("!</?code>!", "\n", self::$file_content);
		self::$file_content = str_replace("<code>", "\n", self::$file_content);
		self::$file_content = str_replace("</code>", "", self::$file_content);
		self::$file_content = str_replace("<pre>", "<pre><code>", self::$file_content);
		self::$file_content = str_replace("</pre>", "</code></pre>", self::$file_content);
	}

	static private function parse_url() {
		self::$file_content = preg_replace("![^\!]\[(.*)\]\((.*)\)!", "<a href=\"$2\" class=\"Q.fancybox\">$1</a>", self::$file_content);
	}

	static private function parse_list() {
		// li
		self::$file_content = preg_replace("! \* ?(.*)\n!", "<li>$1</li>", self::$file_content);

		// ul
		self::$file_content = preg_replace("!(<li>.*</li>\n?)+!", "<ul>$1</ul>", self::$file_content);
	}

	static private function parse_img() {
		self::$file_content = preg_replace("!\!\[(.*)\]\((.*)\)!", "<img src=\"$2\" alt=\"$1\" />", self::$file_content);
	}
	*/
}

?>