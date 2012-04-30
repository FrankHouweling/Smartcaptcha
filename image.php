<?php

	error_reporting(E_ALL);
	ini_set('display_errors','On');

	require( "scaptcha/scaptcha.inc.php" );
	
	// ONLY FOR TESTING PURPOSES!!!! DELETE THE FOLLOWING PART IN REAL-LIFE USAGE
	
	if( isset( $_GET['reset'] ) )
	{
		
		session_destroy();
		
	}

	if( !isset( $_SESSION['img'] ) )
	{
	
		ob_start();
	
		$captcha = new SmartCaptcha();
		
		$captcha->setBgPlainColorFromHex( "#FFFFFF" );
		
		$captcha->setDefaultTextColorFromHex( "#000000" );
		
		$captcha->setAchtergrondRuis( false );
		
		$captcha->setVoorgrondRuis( false );
		
		$captcha->setNoTextShadow( true );
		
		$captcha->draw();
		
		$img = ob_get_clean();
	
		$_SESSION['img']	=	$img;
		$_SESSION['secretword']	=	$captcha->getCheckText();
		
	}
	else
	{
		
		$img	=	$_SESSION['img'];
		
	}

	echo base64_encode($img);

?>