<?php

class Upload{

	private $_error;
	private $_fieldName;
	private $_image;
	private $_newImage;

	//FILE
	private $_extension;
	private $_tmp;
	private $_size;
	private $_type;
	private $_width;
	private $_height;
	private $_ratio;

	public function __construct($file) {

		$this->getFileInfo($file);
	}

	private function getFileInfo($file){

		$this->_fieldName = key($file);

		$temp = explode(".", $file[$this->_fieldName]['name']);
		$this->_extension = end($temp); //get last element in array
		$this->_tmp = $file[$this->_fieldName]['tmp_name'];
		$this->_size = $file[$this->_fieldName]['size'];
		$this->_type = $file[$this->_fieldName]['type'];

		$temp = getimagesize($this->_tmp);
		$this->_width = $temp[0];
		$this->_height = $temp[1];
		$this->_ratio = $temp[0] / $temp[1];
		
	}

	public function load(){

		if( $this->_type == 'image/jpeg' || $this->_type == 'image/jpg' ) {   
			$this->_image = imagecreatefromjpeg($this->_tmp); 
		}elseif($this->_type == 'image/gif' ) {   
			$this->_image = imagecreatefromgif($this->_tmp); 
		}elseif($this->_type == 'image/png' ) {   
			$this->_image = imagecreatefrompng($this->_tmp); 
		}else{
    	throw new Exception("The file you're trying to open is not supported");
    }
	}

	public function cropImage($newHeight, $newWidth){

		if ($this->_width > $this->_height){
		  $y = 0;
		  $x = ($this->_width - $this->_height) / 2;
		  $smallestSide = $this->_height;
		}else{
		  $x = 0;
		  $y = ($this->_height - $this->_width) / 2;
		  $smallestSide = $this->_width;
		}

		$thumb = imagecreatetruecolor($newWidth, $newHeight);
		imagecolortransparent($thumb, imagecolorallocate($thumb, 0, 0, 0));
		imagealphablending($thumb, false);
		imagesavealpha($thumb, true);

		imagecopyresampled($thumb, $this->_image, 0, 0, $x, $y, $newWidth, $newHeight, $smallestSide, $smallestSide);
		$this->_newImage = $thumb;
	}

	public function scaleImage($newHeight, $newWidth){

		if($this->_ratio > 1){
		  $width = $newWidth;
		  $height = $newHeight/$this->_ratio;
		}
		else{
		  $width = $newWidth*$this->_ratio;
		  $height = $newHeight;
		}

		$new_image = imagecreatetruecolor($width, $height);

		imagecolortransparent($new_image, imagecolorallocate($new_image, 0, 0, 0));
		imagealphablending($new_image, false);
		imagesavealpha($new_image, true);
		
		imagecopyresampled($new_image, $this->_image, 0, 0, 0, 0, $width, $height, $this->_width, $this->_height);
		$this->_newImage = $new_image;
	}

	function save($filename, $quality=75){   

		if( $this->_type == 'image/jpeg' ) { 
			imagejpeg($this->_newImage, UPLOAD_FOLDER. $filename  . '.' . $this->_extension ,$quality); 
		} elseif( $this->_type == 'image/gif' ) {   
			imagegif($this->_newImage, UPLOAD_FOLDER. $filename  . '.' . $this->_extension ); 
		} elseif( $this->_type == 'image/png' ) {   
			imagepng($this->_newImage, UPLOAD_FOLDER. $filename  . '.' . $this->_extension );
		} 
	}
}

?>