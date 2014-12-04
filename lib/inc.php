<?php 

error_reporting(E_ALL);
ini_set('display_errors','1');

function __autoload($class_name)
{
	// Customize this to your root Flourish directory
	$flourish_root = $_SERVER['DOCUMENT_ROOT'] . '/../lib/flourishlib/';

	$file = $flourish_root . $class_name . '.php';

	if (file_exists($file)) {
		include $file;
		return;
	}

	throw new Exception('The class ' . $class_name . ' could not be loaded');
}

require_once '../lib/functions.php';
require_once '../lib/db.php';
require_once '../lib/xhr.php';

Db::init();