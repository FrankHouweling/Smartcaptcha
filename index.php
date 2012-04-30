<?php

	error_reporting(E_ALL);
	ini_set('display_errors','On');

	require( "scaptcha/scaptcha.inc.php" );
	
	// ONLY FOR TESTING PURPOSES!!!! DELETE THE FOLLOWING PART IN REAL-LIFE USAGE
	
	if( isset( $_GET['reset'] ) )
	{
		
		session_destroy();
		
		session_start();
		
	}

	if( !isset( $_SESSION['img'] ) )
	{
	
		ob_start();
	
		$captcha = new SmartCaptcha();
		
		$captcha->setLanguage( "nl" );
		
		$captcha->draw();
		
		$img = ob_get_clean();
	
		$secretword	=	$captcha->getCheckText();;
	
		$_SESSION['img']		=	$img;
		$_SESSION['secretword']	=	$secretword;
		$question				=	$captcha->getQuestion();
		
		$_SESSION['question']	=	$question;
		
	}
	else
	{
		
		$img		=	$_SESSION['img'];
		$question	=	$_SESSION['question'];
		$secretword	=	$_SESSION['secretword'];
		
	}

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
				
				<a href="index.php?reset">New captcha!</a>
				
			</p>
		</center>
		
	</body>
	
</html>