db.class.php - php class for MySQL DB with debug
================================================

	require_once "db.class.php";

	db::setConfig(
		array(
			"db_host" => "localhost",
			"db_port" => 3306,
			"db_name" => "test_base",
			"db_user" => "user_name",
			"db_psswd" => "password123"
		)
	);

	$users = db::query("
	  SELECT *
	  FROM `t1`
	  LEFT JOIN (`t2`) USING (`id`)
	  ORDER by `name`
	");

	foreach($users as $user) {
	  print_r($user]);
	}



template.class.php - small template engine
==========================================

	require_once "template.class.php";

	$template = new template("template", "/var/www/site/templates");
	$template->msg = "hello";

	echo $template;


in file: /var/www/site/templates/template.tmpl.php

	<?=$msg;?>, world!



registry.class.php - temporary storage
======================================

	require_once "registry.class.php";

	registry::set("test1", "value");
	echo registry::get("test1");
	// value


	$array = array(
		"key1" => array(
			"key12" => "value12"
		),
		"key2" => "value2"
	);
	registry::set("test2", $array);

	echo registry::get("test2", "key1", "key12");
	// value12



mail.class.php - send mail
==========================

	require_once "mail.class.php";

	$mail = new mail();
	$mail->from("mail@mail.ru", "Sokolov Innokenty");
	$mail->to("mail@mail.ru");
	$mail->cc("mail@mail.ru");
	$mail->bcc("mail@mail.ru");
	$mail->subject("тема");
	$mail->message("текст");
	$mail->attach("/var/www/etc/file.txt");
	$mail->send();



folder.class.php - folder manipulation
======================================

	require "folder.class.php";

	$folder = new folder("/home/qbbr/folder");
	$folder->create();
	echo $folder->pwd();
	$folder->delete();



file.class.php - file manipulation
==================================

	require "folder.class.php"; // depends: folder.class.php
	require "file.class.php";

	$file = new file("/var/www/dir/file", true);
	$file->write("xDD");

	var_dump($file->read());

	var_dump($file->info());
	var_dump($file->last_access());
	var_dump($file->last_change());
	var_dump($file->perms());
	var_dump($file->size());

	$file->delete();



cache.class.php - simple caching system for all
===============================================

	cache::$dir = "/tmp/site_cache"; // set writable dir for cache

	$query = cache::get("cache_key", 60); // get cache every 60 minutes

	if ($query === false) {
		$query = db::query("
			SELECT *
			FROM `t1`
			LEFT JOIN (`t2`) USING (`id`)
			ORDER by `name`
		");

		cache::set("cache_key", $query); // set cache
	}
