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
	
		// Load the image...
		$captcha = new SmartCaptcha();
		$img = $captcha->draw();
		
		//	Just load some more data...
		$secretword	=	$captcha->getCheckText();
		$question	=	$captcha->getQuestion();
		
	}
	else
	{	//	Just load the image from the SESSION-data.
		
		$img		=	$_SESSION['img'];
		$question	=	$_SESSION['question'];
		$secretword	=	$_SESSION['secretword'];
		
	}

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