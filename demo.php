<?php
ini_set('memory_limit', '64M');

error_reporting(E_ALL);
ini_set('display_errors', 'On');


// Include the Simple-captcha class

require( "scaptcha/scaptcha.inc.php" );


$captcha = new SmartCaptcha();

if (isset($_GET['reset'])) {

	$captcha->reset();
    
}

$captcha->setLanguage( "NL" );

$img = $captcha->draw();

//	Just load some more data...
$secretword = $captcha->getCheckText();
$question = $captcha->getQuestion();
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
        <h1><?php echo $question; ?></h1>
        <img src="data:image/png;base64,<?php echo $img ?>" alt="Captcha" />
        <p>
            Antwoord: <?php echo $secretword; ?>
        </p>

        <p>

            <a href="demo.php?reset">New captcha!</a>

        </p>
    </center>

</body>

</html>