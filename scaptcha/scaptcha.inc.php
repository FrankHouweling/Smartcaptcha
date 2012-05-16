<?php
	/*
	 * 
	 * Scaptcha.inc.php
	 * 
	 * Smartcaptcha main class.
	 * 
	 */
	
	// Thanks Chris
	if( !isset( $_SESSION ) )
	{
				
		session_start();
			  
	}
	

	// Disable direct opening of the file.

	if( __FILE__ == $_SERVER['SCRIPT_FILENAME'] )
		die( "This file can't be opened directly." );

	class SmartCaptcha
	{
		
		private $image;
		
		private $height;
		private $width;
		
		private $fonts;
		
		private $bgPlainColor;
		
		private $lang;
		private $amoundDummyWords;
		
		private $dataPath;
		
		private $dummyWords;
		private $checkText;
		
		private $noShadow;
		
		private $textsDrawn;
		private $lastY;
		private $lastTextLength;
		
		private $achtergrondRuis;
		private $voorgrondRuis;
		
		private $defaultTextColor;
		
		private $question;
		
		
		/*
		 * 
		 * Constructor
		 * 
		 */
		
		function __construct()
		{
			
			
			// Set default height and width
			
			$this->height		=	110;
			$this->width		=	330;
			$this->amoundDummyWords	=	2;
			$this->setLanguage( "en" );
			$this->dataPath		=	"scaptcha/data/";
			$this->noShadow		=	false;
			$this->achtergrondRuis	=	true;
			$this->voorgrondRuis	=	false;
			
		}
		
		/*
		 * 
		 * Function createImage
		 * No arguments.
		 * Public.
		 * 
		 * Makes a php-gd object for the object with the current width and height.
		 * 
		 */
		
		public function createImage()
		{
		
			$this->image = ImageCreate( $this->width , $this->height );
			
		}
		
		/*
		 * 
		 * Function draw
		 * No arguments.
		 * Public.
		 * 
		 * Draw's the final image on screen.
		 * 
		 */
		
		public function draw( $encode = true )
		{
			
			if( isset( $_SESSION['img'] ) )
			{
				
				if( $encode == true )
				{
					
					return base64_encode( $_SESSION['img'] );
					
				}
				else
				{
					
					return $_SESSION['img'];
					
				}
				
			}
			else{

					
				ob_start();
				
				// Create image if not yet created
				
				if( $this->image == NULL )
				{
					
					$this->createImage();
					
				}
				
				// Create background if color is given
				
				if( $this->bgPlainColor !== NULL AND $this->bgPlainColor !== false )
				{
					
					$red = ImageColorAllocate(	
							$this->image, $this->bgPlainColor["r"], 
							$this->bgPlainColor["g"], 
							$this->bgPlainColor["b"]);
	
					ImageFillToBorder($this->image, 0, 0, $red, $red);	
					
				}
				else
				{
						
					$clr	=	$this->getRandomColor( $this->image );
					
					ImageFillToBorder( $this->image, 0, 0, $clr, $clr);
				
				}
				
				
				// Achtergrondruis..
				
				// Lijnen
				
				if( $this->achtergrondRuis == true )
				{
					
					for( $i = 0; $i < rand(10, 30); $i++ )
					{
						
						switch( rand(1,2) )
						{
							case 1:
								imagearc( $this->image, rand(0,$this->width),rand(0,$this->height), rand(0,$this->width), rand(0,$this->height),  0, rand(10,360), $this->getRandomColor( $this->image ));
							break;
							case 2:
								imageline( $this->image, rand(0,$this->width), rand(0,$this->height),rand(0,$this->width), rand(0,$this->height) , $this->getRandomColor( $this->image ) );
							break;
						}					
						
					}
					
					
				}
				
				
				// Now draw the check text
				
				if( $this->checkText == NULL )
				{
					
					$this->generateCheckText();
					
				}
				
				
				// Check if dummytext is aleady generated
				
				if( !is_array($this->dummyWords) )
				{
					
					$this->dummyWords = $this->generateDummyText();
					
				}
				
				
				// Draw the check text on the image
				$this->dummyWords[]	=	$this->checkText;
				
				shuffle( $this->dummyWords );
				
				// Draw them dummywords!
				
				foreach( $this->dummyWords as $dummyWord )
				{
					
					$this->drawText( $dummyWord );
					
				}
				
				// Voorgrondruis
	
				
				if( $this->voorgrondRuis == true )
				{
					
					for( $i = 0; $i < rand(30, 50); $i++ )
					{
						
						switch( rand(1,2) )
						{
							case 1:
								imagearc( $this->image, rand(0,$this->width),rand(0,$this->height), rand(0,$this->width), rand(0,$this->height),  0, rand(10,360), ImageColorAllocate(	
							$this->image, $this->bgPlainColor["r"], 
							$this->bgPlainColor["g"], 
							$this->bgPlainColor["b"]));
							break;
							case 2:
								imageline( $this->image, rand(0,$this->width), rand(0,$this->height),rand(0,$this->width), rand(0,$this->height) , ImageColorAllocate(	
							$this->image, $this->bgPlainColor["r"], 
							$this->bgPlainColor["g"], 
							$this->bgPlainColor["b"]) );
							break;
						}					
						
					}
					
					
				}
				
				
				//Het plaatje aanmaken. 
				ImagePng($this->image); 
				
				//Het plaatje verwijderen uit het geheugen 
				ImageDestroy($this->image); 
				
				// Create SESSION-data
				
				$img = ob_get_clean();
				
				$_SESSION['img']		=	$img;
				$_SESSION['secretword']	=	$this->getCheckText();
				$_SESSION['question']	=	$this->getQuestion();
				
				if( $encode == true )
				{
					
					/*
					 * 
					 * BASE 64 is a really easy way to use an image in the same page.
					 * 
					 * <img src="data:image/png;base64,{ your data }" />
					 * 
					 */
					
					return base64_encode($img);
					
				}
				else
				{
						
					return $img;	
					
				}
				
			}
			
		}

		/*
		 * 
		 * TODO
		 * 
		 */
		
		public function setSize( $width = false , $height = false )
		{
			
			if( $width !== false )
			{
				
				$this->width	=	$width;
				
			}
			
			if( $height !== false )
			{
				
				$this->height	=	$height;
				
			}
			
		}

		/*
		 * 
		 * TODO
		 * 
		 */
		
		public function generateCheckText()
		{
			
			require_once( $this->dataPath . $this->lang . "/dat.php" );
			
			// TODO how do I do this in a better way?
			
			$n	=	rand( 0, count($sets) - 1 );
			
			foreach( $sets as $key => $value )
			{
				
				if( $key == $n )
				{
				
					$word			=	$value[ array_rand($value) ];
					
					$this->question	=	str_replace("{word}", $key, $question);
					
					break;
					
				}
				
			}
			
			$this->checkText	=	$word;
			
		}
		
		/*
		 * 
		 * TODO
		 * 
		 */
		
		private function drawText( $dummyWord, $tmpcolor = false, $shadow = true, $x = false, $givenY = false )
		{
			// First get the color
			
			$this->textsDrawn++;
			
			if( $tmpcolor == false )
			{
				if( $this->defaultTextColor !== NULL )
				{
					
					$tmpcolor = imagecolorallocate($this->image, $this->defaultTextColor["r"], $this->defaultTextColor["g"], $this->defaultTextColor["b"]);
					
				}
				else
				{
					
					$tmpcolor	=	$this->getRandomColor( $this->image );
					
				}
				
			}
			else
			{
				
				$tmpcolor = imagecolorallocate($this->image, $tmpcolor["r"], $tmpcolor["g"], $tmpcolor["b"]);
				
			}
			
				
			// Then the font
			$this->dataPath . "/fonts/" . $this->getRandomFont();
				
			// The Y-position
			if( $givenY == false )
			{
			
				if( $this->lastY == NULL )
				{
					
					// Start somewhere on the image...
					
					$y			=	  rand(25, ($this->height - 25));
					
				}
				else
				{
					// Make sure it does not overlap other texts
					
					// echo "<br /><br />Last: " . $this->lastY . "<br /.<br />";
					
					if( $this->lastY < 60 )
					{
						
						// echo "Begin...";
						
						$y	=	rand( 60, ( $this->height - 25 ) );
						
					}
					else if( $this->lastY > ( $this->height - 60 ) )
					{
						
						// echo "Einde...";
						
						$y	=	rand( 25, $this->height - 60 );
						
					}
					else
					{
						
						// echo "Midden...";
						
						// Vorige in het midden.. dan nu het begin of einde he!
						
						switch( rand(1,2) )
						{
							
							case 1:
								
								// echo " dus begin.";
								
								$y	=	rand(25, 45 );
								
							break;
							case 2:
								
								// echo " dus einde.";
								
								$y	=	rand( ($this->height - 25 ), ( $this->height - 55 ) );
								
							break;
							
						}
						
					}
					
					// echo "<br /> New Y:" . $y . "<br /><br /.";
					
				}
				
				
			}
			else
			{
				
				$y	=	$givenY;
				
			}
			
			$this->lastY	=	$y;
			
			// The X-position
			if( $x == false )
			{
				
				if( $this->textsDrawn == 1 )
				{
					
					$x	=	rand(10,25);
					
				}
				else
				{
					
					$x	=	( $this->textsDrawn - 1 ) * ( $this->width / 8 ) + ( $this->lastTextLength * 3 ) + rand( 5 , 18 );
					
				}
				
				
			}
			
				
			$turn		=	rand(-5,8);
				
			$fontSize	=	rand(20,30);
			
			$font		=	$this->getRandomFont();
			
			if( $shadow == true && $this->noShadow == false )
			{
				
				imagettftext(
						$this->image , 
						$fontSize + 1,	// Font size
						$turn,	//	Turn it a little?
						$x-2,
						$y-2,
						imagecolorallocate($this->image, 0, 0, 0), 
						$this->dataPath . "/fonts/" . $font, 
						$dummyWord);
				
			}
				
			imagettftext( 
					$this->image , 
					$fontSize,	// Font size
					$turn,	//	Turn it a little?
					$x,
					$y,
					$tmpcolor, 
					$this->dataPath . "/fonts/" . $font, 
					$dummyWord);
					
					
		
			$this->lastTextLength	=	strlen($dummyWord);
			
		}
		
		/*
		 * 
		 * TODO
		 * 
		 */
		
		private function getRandomColor( $im )
		{
			
			return imagecolorallocate($im, mt_rand(150,240), mt_rand(150,240), mt_rand(150,240));
			
		}
		
		/*
		 * 
		 * TODO
		 * 
		 */
		
		private function getRandomFont()
		{
			
			if( $this->fonts == NULL )
			{
			
				$this->updateFonts();
				
			}
			
			return $this->fonts[ array_rand( $this->fonts ) ];
					
		}
		
		/*
		 * 
		 * TODO
		 * 
		 */
		
		private function updateFonts()
		{
			
			$this->fonts	=	$this->getDirectoryList( $this->dataPath . "/fonts/", ".ttf" );
			
			// Give an error when there are no font files.
			
			if( count( $this->fonts ) == 0 )
			{
				
				$this->error( 1 , "No font files (.ttf) found in " . $this->dataPath . "/fonts" );
				
			}
			
		}
		
		/*
		 * 
		 * TODO
		 * 
		 */
		
		function getDirectoryList ( $directory , $filetype = false ) 
		  {
		
		    // create an array to hold directory list
		    $results = array();
		
		    // create a handler for the directory
		    $handler = opendir($directory);
		
		    // open directory and walk through the filenames
		    while ($file = readdir($handler)) {
		
		      // if file isn't this directory or its parent, add it to the results
		      if ($file != "." && $file != "..") {
		      	
		      	if( $filetype !== false && strpos($file, $filetype) !== false )
		      	{
		        	$results[] = $file;
				}
				
		      }
		
		    }
		
		    // tidy up: close the handler
		    closedir($handler);
		
		    // done!
		    return $results;
		
		  }
			
		/*
		 * 
		 * TODO
		 * 
		 */
		
		public function setBgPlainColor( $r, $g, $b )
		{	//	FOR RGB
		
		
			$this->bgPlainColor	=	array( "r" => $r, "g" => $g, "b" => $b );
			
		}
		
		/*
		 * 
		 * TODO
		 * 
		 */
		
		private function hex2rgb( $hex )
		{
			
			// Check if first character is #
			
			if( $hex[0] !== "#" )
			{
				
				// If it isn't, add it!
				
				$hex	=	"#" . $hex;
				
			}
			
			// ALle characters op een rijtje...
			$chars = preg_split('//', $hex, -1, PREG_SPLIT_NO_EMPTY);
			
			$color = array();
			
			
			//maak array
			
			$cnt	=	0;
			
			foreach( range(0,2) as $g )
			{
				
				$color[$g]	=	hexdec($chars[$cnt+1].$chars[$cnt+2]);
				$cnt	+=	2; 
				
			}
			
			
			//berekend alles
			return $color;
			
		}
		
		/*
		 * 
		 * TODO
		 * 
		 */
		
		public function setBgPlainColorFromHex( $hex )
		{
			
			$color	=	$this->hex2rgb( $hex );
			
			$this->setBgPlainColor( $color[0], $color[1], $color[2] );
			
		}
		
		/*
		 * 
		 * TODO
		 * 
		 */
		
		public function getBgPlainColor()
		{
			
			return $this->bgPlainColor;
			
		}
		
		/*
		 * 
		 * TODO
		 * 
		 */
		
		public function setDefaultTextColorFromHex( $hex )
		{
			
			$color	=	$this->hex2rgb( $hex );
			
			$this->setDefaultTextColor( $color[0], $color[1], $color[2] );
			
		}
		
		/*
		 * 
		 * TODO
		 * 
		 */
		
		public function setDefaultTextColor( $r, $g, $b )
		{	//	FOR RGB
		
		
			$this->defaultTextColor	=	array( "r" => $r, "g" => $g, "b" => $b );
			
		}
		
		public function setAmoundDummyWords( $cnt )
		{
			
			$this->amoundDummyWords	=	(int)$cnt;
			
		}
		
		/*
		 * 
		 * 
		 * 
		 */
		
		private function generateDummyText( $hoeveel = false )
		{
			
			if( $hoeveel !== false )
			{
				
				$aantal	=	$hoeveel;
				
			}
			else
			{
				
				$aantal	=	 $this->amoundDummyWords;
				
			}
			// First load the file..
			
			$tmp	=	file( $this->dataPath . $this->lang . "/dict.txt" );
			
			$retAr	=	array();
			
			// Loop thill we have one...
			
			while( true )
			{
					
				// TODO check if not in array with "good words"
				
				$retAr[]	=	strtolower( $tmp[ array_rand( $tmp ) ] );
				
				// Stop looping if we have enough words!
				if( count($retAr) >= $aantal )
				{
				
					break;
					
				}
				
			}
			
			return $retAr;
			
		}
		
		/*
		 * 
		 * TODO
		 * 
		 */
		
		public function setLanguage( $langcode )
		{
			
			if( strlen($langcode) == 2 )
			{
				
				$this->lang	=	$langcode;
				
			}
			else
			{
				
				return false;
				
			}
			
		}
		
		/*
		 * 
		 * TODO
		 * 
		 */
		
		public function getCheckText()
		{
			
			return $this->checkText;
			
		}
		
		/*
		 * 
		 * TODO
		 *
		 */
		
		public function setAchtergrondRuis( $bool )
		{
			
			if( is_bool($bool) )
			{
				
				$this->achtergrondRuis	=	$bool;
				
				return true;
				
			}
			else
			{
			
				return false;
			
			}
			
		}
		
		/*
		 * 
		 * TODO
		 *
		 */
		
		public function setVoorgrondRuis( $bool )
		{
			
			if( is_bool($bool) )
			{
				
				$this->voorgrondRuis	=	$bool;
				
				return true;
				
			}
			else
			{
			
				return false;
			
			}
			
		}
		
		
		/*
		 * 
		 * TODO
		 *
		 */
		
		public function setTextShadow( $bool )
		{
			
			if( is_bool($bool) )
			{
				
				$this->noShadow	=	!$bool;
				
				return true;
				
			}
			else
			{
			
				return false;
			
			}
			
		}
		
		/*
		 * 
		 * TODO
		 * 
		 */
		
		public function error( $n, $txt )
		{
			
			echo $n . " - " . $txt;
			
		}
		
		/*
		 * 
		 * TODO
		 * 
		 */
		
		public function getQuestion()
		{
			
			return $this->question;
			
		}
		
	}

?>