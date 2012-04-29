<?php

	session_start();

	/*
	 * 
	 * Scaptcha.inc.php
	 * 
	 * Smartcaptcha main class.
	 * 
	 */

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
		
		
		/*
		 * 
		 * Constructor
		 * 
		 */
		
		function __construct()
		{
			
			// Set default height and width
			
			$this->height		=	100;
			$this->width		=	300;
			$this->dummyWords	=	2;
			$this->setLanguage( "en" );
			$this->dataPath		=	"scaptcha/data/";
			$this->noShadow		=	false;
			
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
		
		public function draw()
		{
			
			header("content-type: image/png"); 
			
			// Create image if not yet created
			
			if( $this->image == NULL )
			{
				
				$this->createImage();
				
			}
			
			// Create background if color is given
			
			if( $this->bgPlainColor !== NULL )
			{
				
				$red = ImageColorAllocate(	
						$this->image, $this->bgPlainColor["r"], 
						$this->bgPlainColor["g"], 
						$this->bgPlainColor["b"]);

				ImageFillToBorder($this->image, 0, 0, $red, $red);	
				
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
			
			// And some distortion
			
			if( $this->bgPlainColor !== NULL )
			{
				
				//$this->drawText( "SOME DISTORTION AND STUFF TO TEST THIS OUT", $this->bgPlainColor, false, 20, 40 );
				
			}
			
			//Het plaatje aanmaken. 
			ImagePng($this->image); 
			
			//Het plaatje verwijderen uit het geheugen 
			ImageDestroy($this->image); 
			
		}

		/*
		 * 
		 * TODO
		 * 
		 */
		
		public function generateCheckText()
		{
			
			$gen	=	$this->generateDummyText(1);
			
			$this->checkText	=	"chair";
			
		}
		
		/*
		 * 
		 * TODO
		 * 
		 */
		
		private function drawText( $dummyWord, $tmpcolor = false, $shadow = true, $x = false, $y = false )
		{
			// First get the color
			
			$this->textsDrawn++;
			
			if( $tmpcolor == false )
			{
			
				$tmpcolor	=	$this->getRandomColor( $this->image );
				
			}
			else
			{
				
				$tmpcolor = imagecolorallocate($this->image, $tmpcolor["r"], $tmpcolor["g"], $tmpcolor["b"]);
				
			}
				
			// Then the font
			$this->dataPath . "/fonts/" . $this->getRandomFont();
				
			// The Y-position
			if( $y == false )
			{
			
				if( $this->lastY == NULL )
				{
					
					$y			=	  rand(25, ($this->height - 20));
					
				}
				else
				{
					
					if( $this->lastY < 25 )
					{
						
						$y	=	rand( $this->lastY + 25, ( $this->height - 25 ) );
						
					}
					else if( $this->lastY > ( $this->height - 20 ) )
					{
						
						$y	=	rand( 25, $this->lastY - 25 );
						
					}
					else
					{
						
						$y	=	rand(25, ( $this->height - 25 ) );
						
					}
					
					$y			=	rand(25, ($this->height - 25));
					
				}
				
				
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
					
					$x	=	( $this->textsDrawn - 1 ) * 40 + ( $this->lastTextLength * 10 ) + rand( 5 , 18 );
					
				}
				
				
			}
			
				
			$turn		=	rand(-13,12);
				
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
			
			echo "Hoi!";
			
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
				
				$aantal	=	 $this->dummyWords;
				
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
		
		public function error( $n, $txt )
		{
			
			echo $n . " - " . $txt;
			
		}
		
	}

?>