<?php 

require_once '../lib/inc.php';

$xhr = new Xhr();

if(isset($_GET['a']))
{
	$action = $_GET['a'];
	
	if(method_exists($xhr,$action))
	{
		$xhr->$action();
	}
}


