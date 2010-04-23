db.class.php - php class for MySQL DB with debug
================================================

	require_once "db.class.php";

	db::setConfig(
		array(
			'db_host' => "localhost",
			'db_post' => 3306,
			'db_name' => "test_base",
			'db_user' => "user_name",
			'db_psswd' => "password123"
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