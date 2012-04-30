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
		
		$captcha->setLanguage( "nl" );
		
		$captcha->draw();
		
		$img = ob_get_clean();
	
		$_SESSION['img']		=	$img;
		$_SESSION['secretword']	=	$captcha->getCheckText();
		$question				=	$captcha->getQuestion();
		
		$_SESSION['question']	=	$question;
		
	}
	else
	{
		
		$img		=	$_SESSION['img'];
		$question	=	$_SESSION['question'];
		
	}

	$img	=	 base64_encode($img);

?>