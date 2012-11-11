<?php

class Image
{
    var $image, $text, $width, $height, $font, $ColorRand, $ColorRandAlpha;
    
    /**
     * Do image.
     *
     * @param integer $width
     * @param integer $height
     */
    public function addImage( $width = "120", $height = "50" )
    {
    	
    	if( !is_numeric( $width ) ) $width = 120;
    	if( !is_numeric( $height ) ) $height = 50;
    	
    	$this->width = $width;
    	$this->height = $height;
        $this -> image = imagecreate( $width, $height );
        $colorBody = imagecolorallocatealpha($this -> image, 255,255,255, rand( 0,100 ) ); // rand( 0,40 ), rand( 40, 80 ), rand( 80, 120 )
        $colorShadow = imagecolorallocate($this -> image, 0x33, 0x33, 0x33);
        $colorHighlight = imagecolorallocate($this -> image, 0xCC, 0xCC, 0xCC);
        $colorRand = imagecolorallocate( $this->image,  0,0,0);//rand( 120, 160 ), rand( 160, 200 ), rand( 200,255 )
        $this -> ColorRand = $colorRand;
        
        imagefilledrectangle( $this -> image, 1, 1, $width-2, $height-2, $colorBody );
	}
    
	public function AddHendler( $name, $value )
	{
		$this->$name = $value;
	}
	
	/**
	 * Add bokder on the image.
	 *
	 */
    public function addBorder()
    {
    	if( $this -> image ) 
    	{
    		$colorShadow = imagecolorallocate($this -> image, rand( 0, 255 ) , rand( 0, 255 ) , rand( 0, 255 ) );
        	$colorHighlight = imagecolorallocate($this -> image, rand( 0, 255 ) , rand( 0, 255 ) , rand( 0, 255 ) );
        	
        	imageline( $this -> image, 0, $this->height-1, $this->width-1, $this->height-1, $colorShadow );
	        imageline( $this -> image, $this->width-1, 1, $this->width-1, $this->height-1, $colorShadow) ;
	        imageline( $this -> image, 0, 0, $this->width-1, 0, $colorHighlight );
	        imageline( $this -> image, 0, 0, 0, $this->height-2, $colorHighlight ); 
    	}
    	else 
    	{
  			print 'error. ( $class -> new Button( "100", "30", $word, rand( 11, 13 )  ); ) ';
  			exit;
    	}
    }
    
    /**
     * Add text on the image.
     *
     * @param integer $alphavit
     * @param integer $font
     */
    public function addText( $alphavit = "0", $font )
    {
    	if( $alphavit == 0 )
    	{
    		$alphavit = 'qwertyuiopasdfghjklzxcvbnm';
    	}
    	elseif ( $alphavit == 1 )
    	{
    		$alphavit = '1234567890';	
    	}
    	else 
    	{
    		$alphavit = 'qwertyuiopasdfghjklzxcvbnm1234567890';	
    	}
    	

		for( $i = 0; $i<6; $i++) 
		{
			$word .=  $alphavit{ rand(0, strlen( $alphavit )-1 ) };
		}
		
	$labelHeight = imagefontheight( $font );
        $labelWidth = imagefontwidth( $font ) * strlen( $word );
        $labeXrndNum = rand( 10, 20 );
        $labelX = ( $this->width - $labelWidth ) / 2 - $labeXrndNum;
        $labelY = ( $this->height - $labelHeight ) / 2 + 15;
        
        $fonts
        	 = array(
		'font1.otf',
		'font2.ttf',
	     	'font3.ttf',
		'font5.ttf',
       	);
        
       	
        $text = imagettftext( $this -> image, $font, rand(-5, 12), $labelX, $labelY, $this -> ColorRand, 'fonts/'.$fonts[ rand( 0, count( $fonts ) -1 ) ], $word );
        
        $this -> text =  $word;
    }
    
    
    /**
     * Add noise on captcha
     *
     * @param integer $count
     */
    public function AddNoise( $count = "100" )
    {
    	if( !is_numeric( $count ) )
    	{
    		$count = 100;	
    	}
    	
    	if( $this -> image )
    	{
    		for( $i=0; $i<$count; $i++ ) 
    		{
    			$Color = imagecolorallocate( $this -> image, rand( 0, 255 ), rand( 0, 255 ), rand( 0, 255 ) );
    			$x = rand( 0, imagesx( $this -> image ) );
    			$y = rand( 0, imagesy( $this -> image ) );
    			imageline( $this -> image, $x, $y, $x+1, $y+1, $Color );
    		}
    	}
    }
    
    /**
     * Apply filter on the image.
     *
     * @param string $filter
     * @param integer $args
     * @return boolean
     */
    public function AddFilter( $filter, $args = "" )
    {
    	
    	$status = false;
    	
    	if( $args == "" )
    	{
    		$args = 0;
    	}
    	
    	if( $filter == "contrast" )
    	{
    		if( $this -> image )
    		{
    			$status = imagefilter( $this -> image, $filter, $args); // Changes the contrast of the image.
    		}
    	}
    	
    	if( $filter == "negate" )
    	{
    		if( $this -> image )
    		{
    			$status = imagefilter( $this -> image, IMG_FILTER_NEGATE ); // Reverses all colors.
    		}
    	}
    	
    	if( $filter == "grayscale" )
    	{
    		if( $this -> image )
    		{
    			$status = imagefilter( $this -> image, IMG_FILTER_GRAYSCALE ); // Converts the image into grayscale.
    		}
    	}
    	
    	if( $filter == "brightness" )
    	{
    		if( $this -> image )
    		{
    			$status = imagefilter( $this -> image, IMG_FILTER_BRIGHTNESS, $args ); // Changes the brightness of the image.
    		}
    	}
    	
    	if( $filter == "edgedetect" )
    	{
    		if( $this -> image )
    		{
    			$status = imagefilter( $this -> image, IMG_FILTER_EDGEDETECT ); // Uses edge detection to highlight the edges in the image.
    		}
    	}
    	
    	if( $filter == "emboss" )
    	{
    		if( $this -> image )
    		{
    			$status = imagefilter( $this -> image , IMG_FILTER_EMBOSS ); // Embosses the image.
    		}
    	}
    	
    	if( $filter == "gaussian_blur")
    	{
    		if( $this -> image )
    		{
    			$status = imagefilter( $this -> image, IMG_FILTER_GAUSSIAN_BLUR ); // Blurs the image using the Gaussian method.
    		}
    	}
    	
		if( $filter == "blur" )     	
		{
			if( $this -> image )
			{
				$status = imagefilter( $this -> image, IMG_FILTER_SELECTIVE_BLUR ); // Blurs the image.
			}
		}
		
		if( $filter == "meanremoval" )
		{
			if( $this -> image )
			{
				$status = imagefilter( $this -> image, IMG_FILTER_MEAN_REMOVAL );
			}
		}
		
		if( $filter == "smooth" )
		{
			if( $this -> image )
			{
				$status = imagefilter( $this -> image, IMG_FILTER_SMOOTH, $args ); // Makes the image smoother. Use arg1  to set the level of smoothness.
			}
		}
		
		return $status;
    }
    
    public function draw()
    {
    	if( !is_array( $this->imagetype ) )
    	{
    		$ifunc = 'image'.$this->imagetype;	
    		if( !function_exists( $ifunc ) )
    		{
    			header( 'Content-type: image/png' );
    			imagepng( $this -> image );
    		}
    		else 
    		{
    			header( 'Content-type: image/'.$this->imagetype );
    			$ifunc( $this->image );
    		}
    	}
    	else 
    	{
    		if( $this->imagetype[0] != "jpeg" ) exit;
    		
    		$ifunc = 'image'.$this->imagetype[0];	
    		if( !function_exists( $ifunc ) ) exit;
    		header( 'Content-type: image/'.$this->imagetype[0] );
    		$ifunc( $this -> image, '', $this->imagetype[1] );
    	}
    	
   }
   
    
    public function getCaptchaText()
   	{
    	return $this->text;
    }
}

