<?php

	error_reporting(E_ALL);
	ini_set('display_errors','On');

	require( "scaptcha/scaptcha.inc.php" );

	$captcha = new SmartCaptcha();
	
	$captcha->setBgPlainColorFromHex( "#086A87" );
	
	$captcha->draw();

?>