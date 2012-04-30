<?php

	error_reporting(E_ALL);
	ini_set('display_errors','On');

	// Include the Simple-captcha class

	require( "scaptcha/scaptcha.inc.php" );
	
	// ONLY FOR TESTING PURPOSES!!!! DELETE THE FOLLOWING PART IN REAL-LIFE USAGE
	
	if( isset( $_GET['reset'] ) )
	{
		
		session_destroy();
		
		session_start();
		
	}
	
	// END DELETE IN REAL USAGE

	// Check if the image is already in the session

	if( !isset( $_SESSION['img'] ) )
	{	// First time SESSION
	
		ob_start();
	
		$captcha = new SmartCaptcha();
		
		// Captcha-settings
		
		$captcha->setSize( 400, 180 );
		
		$captcha->setAmoundDummyWords( 1 );
		
		$captcha->setLanguage( "nl" );
		
		$captcha->draw();
		
		//	load the image from the OB into the $img var.
		
		$img = ob_get_clean();
		$secretword	=	$captcha->getCheckText();
		$question				=	$captcha->getQuestion();
		
		// Put it in the SESSION-data for later use.
	
		$_SESSION['img']		=	$img;
		$_SESSION['secretword']	=	$secretword;
		$_SESSION['question']	=	$question;
		
	}
	else
	{	//	Just load the image from the SESSION-data.
		
		$img		=	$_SESSION['img'];
		$question	=	$_SESSION['question'];
		$secretword	=	$_SESSION['secretword'];
		
	}
	
	/*
	 * 
	 * BASE 64 is a really easy way to use an image in the same page.
	 * 
	 * <img src="data:image/png;base64,{ your data }" />
	 * 
	 */

	$img	=	 base64_encode($img);

?>
<!doctype>
<html>
	
	<head>
		<title>CAPTCHA</title>
	</head>
	
	<body>
		
		<br />
		<br />
		<br />
		<br />
		
		<center>
			<h1><?php echo $question;?></h1>
			<img src="data:image/png;base64,<?php echo $img?>" alt="Captcha" />
			<p>
				Antwoord: <?php echo $secretword; ?>
			</p>
			
			<p>
				
				<a href="demo.php?reset">New captcha!</a>
				
			</p>
		</center>
		
	</body>
	
</html>