<?php

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
		
		private $bgPlainColor;
		
		private $lang;
		private $dummyWords;
		
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
			$this->dummyWords	=	3;
			$this->setLanguage( "en" );
			
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
			
			var_dump( $this->generateDummyText() );
			
			die();
			
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
		
		public function setDummyWords( $cnt )
		{
			
			$this->dummyWords	=	(int)$cnt;
			
		}
		
		/*
		 * 
		 * 
		 * 
		 */
		
		private function generateDummyText()
		{
			
			// First load the file..
			
			$tmp	=	file( "data/" . $this->lang . "/dict.txt" );
			
			$retAr	=	array();
			
			while( true )
			{
				
				$retAr[]	=	$tmp[ array_rand( $tmp ) ];
				
				if( count($retAr) >= $this->dummyWords )
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
		
	}

?>