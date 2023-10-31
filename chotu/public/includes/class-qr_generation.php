<?php
/**
 * GdImage example for displaying additional text under the QR Code
 *
 * @link https://github.com/chillerlan/php-qrcode/issues/35
 *
 * @created      22.06.2019
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2019 Smiley
 * @license      MIT
 *
 * @noinspection PhpIllegalPsrClassPathInspection, PhpComposerExtensionStubsInspection
 */

use chillerlan\QRCode\{QRCode, QROptions};
use chillerlan\QRCode\Output\{QROutputInterface, QRGdImagePNG};

/*
 * Class definition
 */

class QRImageWithText extends QRGdImagePNG{

	/**
	 * @inheritDoc
	 */
	public function dump(string $file = null, string $text = null):string{
		// set returnResource to true to skip further processing for now
		$this->options->returnResource = true;

		// there's no need to save the result of dump() into $this->image here
		parent::dump($file);

		// render text output if a string is given
		if($text !== null){
			$this->addText($text);
		}

		$imageData = $this->dumpImage();
       
		$this->saveToFile($imageData, $file);

		if($this->options->outputBase64){
			$imageData = $this->toBase64DataURI($imageData);
		}

		return $imageData;
	}

	protected function addText(string $text):void{
		// save the qrcode image
		$qrcode = $this->image;

		// options things
		$textSize  = 2; // see imagefontheight() and imagefontwidth()
		$textBG    = [255, 255, 255];
		$textColor = [50, 50, 50];

		$bgWidth  = $this->length;
		$bgHeight = ($bgWidth + 20); // 20px extra space

		// create a new image with additional space
		$this->image = imagecreatetruecolor($bgWidth, $bgHeight);
		$background  = imagecolorallocate($this->image, ...$textBG);

		// allow transparency
		if($this->options->imageTransparent && $this->options->outputType !== QROutputInterface::GDIMAGE_JPG){
			imagecolortransparent($this->image, $background);
		}

		// fill the background
		imagefilledrectangle($this->image, 0, 0, $bgWidth, $bgHeight, $background);

		// copy over the qrcode
		imagecopymerge($this->image, $qrcode, 0, 0, 0, 0, $this->length, $this->length, 100);
        
		imagedestroy($qrcode);

		$fontColor = imagecolorallocate($this->image, ...$textColor);
		$w         = imagefontwidth($textSize);
		$x         = round(($bgWidth - strlen($text) * $w) / 2);

		// loop through the string and draw the letters
		foreach(str_split($text) as $i => $chr){
			imagechar($this->image, $textSize, (int)($i * $w + $x), $this->length, $chr, $fontColor);
		}
	}

}


/*
 * Runtime
 */
/**
 * chotu_generate_qr_code
 * chotu generate QR code with passing url in paramemeters 
 * @param  mixed $atts
 * @return void
 */
function chotu_generate_qr_with_text($atts) {
    global $chotu_current_captain;
	if($chotu_current_captain){
		$options = new QROptions;
		$options->version      = 10;
		$options->scale        = 4;
		$options->eccLevel     = QRCode::ECC_H;
		$options->outputBase64 = true;
	
	
		$qrcode = new QRCode($options);
		$url = esc_url($atts['url']);
	
		if($url){
			$qrcode->addByteSegment($url);
			// invoke the custom output interface manually
			$qrOutputInterface = new QRImageWithText($options, $qrcode->getQRMatrix());
			// add text in bottom of QR 
			$out = $qrOutputInterface->dump(null, $chotu_current_captain->user_login);
			printf('<a href="'.$out.'" download="chotuQR'.$chotu_current_captain->user_login.'.png"><img alt="%s" src="%s" /></a>', 'QR', $out);
		  
		}
	}
}
add_shortcode('generate_qr', 'chotu_generate_qr_with_text');
