<?php

	error_reporting(E_ALL);
	ini_set('display_errors','On');

	require( "scaptcha/scaptcha.inc.php" );

	if( !isset( $_SESSION['img'] ) )
	{
	
		ob_start();
	
		$captcha = new SmartCaptcha();
		
		$captcha->setBgPlainColorFromHex( "#FFFFFF" );
		
		$captcha->setAchtergrondRuis( false );
		
		$captcha->setNoTextShadow( true );
		
		$captcha->generateCheckText();
		
		$_SESSION['secretword']	=	$captcha->getCheckText();
		
		$captcha->draw();
		
		$img = ob_get_clean();
	
		$_SESSION['img']	=	$img;
		
	}
	else
	{
		
		$img	=	$_SESSION['img'];
		
	}

	echo base64_encode($img);

?>