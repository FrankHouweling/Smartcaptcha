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
			<?php require("image.php"); ?>
			<h1><?php echo $question;?></h1>
			<img src="data:image/png;base64,<?php echo $img?>" alt="Captcha" />
			<p>
				<a href="new.php" target="_blank">Antwoord</a>
			</p>
		</center>
		
	</body>
	
</html>