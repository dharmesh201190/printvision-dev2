<?php
namespace Biztech\EditablePdf\Controller\Index;

class generateSvg extends \Biztech\Productdesigner\Controller\Index\generateSvg {

    public function execute() {
        try {
            $data = json_decode(file_get_contents('php://input'), TRUE);
            $designId = $data['designId'];
            $imageId = $data['imageId'];
            
            $canvasSVG = isset($data['canvasSVG']) ? json_decode(base64_decode($data['canvasSVG'])) : '';
            $svgImagesNames = $this->generateSVGImages($canvasSVG, $designId, $imageId);
            $this->saveGenerateImages($svgImagesNames);
            $this->generatePDF($designId);
            $this->getResponse()->setBody(json_encode(array("status" => "success")));
        } catch (\Exception $e) {
            $response = $this->infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }
    }
    private function generateSVGImages($canvasSVG, $designId, $imageId) {
        $mediaPath = $this->dir->getPath('media');
        $svgDir = $mediaPath . '/productdesigner/designs/' . $designId . '/svg/';
        $time = substr(base64_encode(microtime()), rand(0, 26), 7);
        $index = 0;
        $svg_name = "/svg_" . $index++ . "_" . $time . ".svg";
        $svgImagesNames = array('name' => $svg_name, 'designId' => $designId, 'image_id' => $imageId);
        preg_match_all('/<image[^>]+>/i', $canvasSVG, $images);
        foreach ($images[0] as $image) {
            preg_match_all('~<image(.*?)xlink:href="([^"]+)"(.*?)>~', $image, $matches);
            foreach ($matches as $attrs) {
                $url = parse_url($attrs[0]);
                if (!empty($url['scheme']) && ($url['scheme'] == 'http' || $url['scheme'] == 'https')) {
                    $ext = pathinfo($attrs[0], PATHINFO_EXTENSION);
                    $filePath = $this->infoHelper->convertRelToAbsPath($attrs[0]);
                    $img = file_get_contents($filePath);
                    $base64Data = "";
                    if($ext == 'jpg' || $ext == 'JPG'){
                        $ext = 'jpeg';
                    }
                    if ($img !== false) {
                        $base64Data = 'data:image/' . $ext . ';base64,' . base64_encode($img);
                    }
                    $secureImg = str_replace($attrs[0], $base64Data, $image);
                    $canvasSVG = str_replace($image, $secureImg, $canvasSVG);
                }
            }
        }
        try {
            if (!file_exists($svgDir) && !empty($designId)) {
                mkdir($svgDir, 0777, true);
            }
            if(!empty($designId)){
                $fp = fopen($svgDir . $svg_name, 'w');
                fwrite($fp, $canvasSVG);
                fclose($fp);
            }
        } catch (\Exception $e) {
            $response = $this->infoHelper->throwException($e, self::class);
            $this->getResponse()->setBody(json_encode($response));
        }

        return $svgImagesNames;
    }
    public function generatePDF($designId){
        ob_start();
         $mediaPath = $this->dir->getPath('media');
        $pdfDir = $mediaPath . '/productdesigner/designs/' . $designId . '/pdf/';
        $pdf_name = $designId.'_designs_vector.pdf';

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $_storeManager = $objectManager->create('\Magento\Store\Model\StoreManagerInterface');
        $prod_design_dir = $_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'productdesigner'.'/designs/';
        $designImages = $objectManager->create('Biztech\Productdesigner\Model\Mysql4\Designimages\Collection')
                ->addFieldToFilter('design_id', array('eq' => $designId))->addFieldToFilter('design_image_type', array('neq' => 'base'))->getData();

        $designData = $objectManager->create('Biztech\Productdesigner\Model\Mysql4\Designs\Collection')
                ->addFieldToFilter('design_id', array('eq' => $designImages[0]['design_id']))->getFirstItem()->getData();

        $filesystem = $objectManager->get('Magento\Framework\Filesystem');
        $directoryList = $objectManager->get('Magento\Framework\App\Filesystem\DirectoryList');
        $media = $filesystem->getDirectoryWrite($directoryList::MEDIA);

        $product = $objectManager->create('Magento\Catalog\Model\Product')->load($designData['product_id']);


        if ($product->getTypeId() == 'configurable') {
            $associatedproduct = $objectManager->create('Magento\Catalog\Model\Product')->load($designData['associated_product_id']);
            list($img_width, $img_height) = getimagesize($media->getAbsolutePath('catalog/product' . $associatedproduct->getData('image')));
        } else {
            list($img_width, $img_height) = getimagesize($media->getAbsolutePath('catalog/product' . $product->getData('image')));
        }

        $img_width = $product->getOutputWidth() ? $product->getOutputWidth(): $img_width;
        $img_height = $product->getOutputHeight() ? $product->getOutputHeight(): $img_height;

        $pdfWidth = $img_width / 3.7795275591;
        $pdfHeight = $img_height / 3.7795275591;

        if ($img_height >= $img_width) {
            $pdf = new \TCPDF('P', 'MM', array($pdfWidth,$pdfHeight), true, 'UTF-8', false);
        } else {
            $pdf = new \TCPDF('L', 'MM', array($pdfWidth,$pdfHeight), true, 'UTF-8', false);
        }
        
        foreach ($designImages as $key => $designImage) {
            if ($designImage['design_image_type'] == 'svg'){
                $image_url = $prod_design_dir . $designId . '/svg' . $designImage['image_path'];
                $imageinfo = pathinfo($image_url);
                $svgurl_relative = $imageinfo['dirname'].'/'.$imageinfo['filename'].'.svg';
                
                $this->loadSVGFonts($image_url,$pdf);
                $cmyksvgurl = $this->generateCMYKSVG($image_url);
                preg_match("#viewbox=[\"']\d* \d* (\d*) (\d*)#i", file_get_contents($svgurl_relative), $d);
                $width = $d[1];
                $height = $d[2];
                
                $pdfWidth = $width / 3.7795275591;
                $pdfHeight = $height / 3.7795275591;

                $pdf->SetPrintHeader(false);
                $pdf->SetPrintFooter(false);
                $pdf->AddPage(); 
                $pdf->SetAutoPageBreak(false, 0);
                $pdf->ImageSVG($cmyksvgurl, 0, 0, $pdfWidth, $pdfHeight, '', '', '', 0, false);

                // $bMargin = $pdf->getBreakMargin();
                // // get current auto-page-break mode.
                // $auto_page_break = $pdf->getAutoPageBreak();
                // $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
            }
        }

        ob_end_clean();
        if (!file_exists($pdfDir)) {
            mkdir($pdfDir, 0777, true);
        }
        $pdf->Output($pdfDir.$pdf_name, 'F');
    }

    public function loadSVGFonts($imageurl,$pdf){

        $dom = new \DOMDocument();
        $dom->load($imageurl);
        $child = $dom->getElementsByTagName('text');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $filesystem = $objectManager->get('Magento\Framework\Filesystem');
        $reader = $filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $path = $reader->getAbsolutePath();

        $imageinfo = pathinfo($imageurl);
        $absoluteurl = $objectManager->create('Biztech\Productdesigner\Helper\Info')->convertRelToAbsPath($imageinfo['dirname']);
        $svgurl = $absoluteurl.'/'.$imageinfo['filename'].'.svg';
        
        foreach ($child as $element) {
            $fontfamily = $element->getAttribute('font-family');

            $fontCollection = $objectManager->create('Biztech\Productdesigner\Model\Mysql4\Fonts\Collection')->addFieldToFilter('font_label', ['eq' => $fontfamily])->getData();
            $fontfamily_absolute_path = $path.$fontCollection[0]['font_file'];
            if(file_exists($fontfamily_absolute_path)){
                $fontname = \TCPDF_FONTS::addTTFfont($fontfamily_absolute_path, 'TrueTypeUnicode', '', 96);
                $pdf->SetFont($fontname, '', 80, '', false);
                // \TCPDF_FONTS::addTTFfont($fontfamily_absolute_path, 'TrueTypeUnicode', '', 96);
            }
        }

        // background color object
        $rectangle_object = $dom->getElementsByTagName('rect');
        foreach ($rectangle_object as $element) {
            $element_width = $element->getAttribute('width');
            $element_height = $element->getAttribute('height');
            if($element_width == '100%' || $element_height == '100%'){
                preg_match("#viewbox=[\"']\d* \d* (\d*) (\d*)#i", file_get_contents($svgurl), $d);
                $width = $d[1];
                $height = $d[2];
                $element->setAttribute('width',$width);
                $element->setAttribute('height',$height);
            }
        }
        $dom->save($svgurl);  
    }
    
    public function generateCMYKSVG($imageurl){

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $dom = new \DOMDocument();
        $dom->load($imageurl);
        $child = $dom->getElementsByTagName('text');

        $imageinfo = pathinfo($imageurl);
        $absoluteurl = $objectManager->create('Biztech\Productdesigner\Helper\Info')->convertRelToAbsPath($imageinfo['dirname']);
        $svgurl = $absoluteurl.'/'.$imageinfo['filename'].'_cmyk.svg';
        if(!file_exists($svgurl)){
            foreach ($child as $element) {
                // extract all css properties
                $element_style = $element->getAttribute('style');
                // get postions of tspan tags and set to text tag

                // extract undline attribute and trim space
                $text_decoration = $element->getAttribute('text-decoration');
                if(trim($text_decoration) == 'underline'){
                    $element->setAttribute('text-decoration','underline');
                }
                $properties = explode(';', $element_style);
                $style = '';
                foreach ($properties as $key => $value) {
                    // extract specific css property
                    $css_property = explode(':', $value);
                    $pattern = "/(\d{1,3})\,?\s?(\d{1,3})\,?\s?(\d{1,3})/";
                    if(isset($css_property[1])){
                        // check for color property
                        if ( preg_match( $pattern, $css_property[1], $matches ) ) {
                            $hexcolor = $this->convertrgbtohex($css_property[1]);
                            $loadColors = $objectManager->create('Biztech\Productdesigner\Model\Printablecolor')->getCollection()->addFieldToFilter('color_code', array('eq' => $hexcolor))->getFirstItem()->getData();
                            if(!empty($loadColors) && $loadColors['pantone_color'] == 0){
                                $rgbcolor = $this->converthextorgb($loadColors['color_code']);

                                $colorpoperty = $css_property[0].': '.$rgbcolor;
                                $cmykcolor = $css_property[0].': '.'cmyk('.$loadColors['color_c'].','.$loadColors['color_m'].','.$loadColors['color_y'].','.$loadColors['color_k'].')';
                                $style .= $cmykcolor.";";
                            }else{
                                $color=$this->rgb2cmyk($this->hex2rgb($hexcolor));
                                $cmykcolor = $css_property[0].': '.'cmyk('.$color['c'].','.$color['m'].','.$color['y'].','.$color['k'].')';
                                $style .= $cmykcolor.";";
                            }
                        }else{
                            $style .= $value.";";
                        }
                    }
                }
                $element->setAttribute('style',$style);
            }

            // background color object
            $rectangle_object = $dom->getElementsByTagName('rect');
            foreach ($rectangle_object as $element) {
                $element_width = $element->getAttribute('width');
                $element_height = $element->getAttribute('height');
                $fillcolor = $element->getAttribute('fill');
                $color=$this->rgb2cmyk($this->hex2rgb($fillcolor));
                if($element_width == '100%' || $element_height == '100%'){
                    preg_match("#viewbox=[\"']\d* \d* (\d*) (\d*)#i", file_get_contents($imageurl), $d);
                    $width = $d[1];
                    $height = $d[2];
                    $cmyk_color = 'cmyk('.$color['c'].','.$color['m'].','.$color['y'].','.$color['k'].')';
                    $element->setAttribute('width',$width);
                    $element->setAttribute('height',$height);
                    $element->setAttribute('fill',$cmyk_color);
                }
            }
        
            $dom->save($svgurl);
        }
        return $svgurl;
    }

    public function convertrgbtohex($color) {
        $pattern = "/(\d{1,3})\,?\s?(\d{1,3})\,?\s?(\d{1,3})/";
        // Only if it's RGB
        if ( preg_match( $pattern, $color, $matches ) ) {
          $r = $matches[1];
          $g = $matches[2];
          $b = $matches[3];
          $color = sprintf("#%02x%02x%02x", $r, $g, $b);
        }
        return $color;
    }

    public function hex2rgb($hex) {
       // $color = str_replace('#','',$hex);
       list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
       $rgb = array(
          'r' => $r,
          'g' => $g,
          'b' => $b,
       );
       return $rgb;
    }

    public function rgb2cmyk($var1,$g=0,$b=0) {
        if (is_array($var1)) {
            $r = $var1['r'];
            $g = $var1['g'];
            $b = $var1['b'];
        } else {
                $r = $var1;
        }
        $cyan = 1 - $r/255;
        $magenta = 1 - $g/255;
        $yellow = 1 - $b/255;
        $black = min($cyan, $magenta, $yellow);
        $cyan = @floor(($cyan - $black) / (1 - $black) * 100);
        $magenta = @floor(($magenta - $black) / (1 - $black) * 100);
        $yellow = @floor(($yellow - $black) / (1 - $black) * 100);
        $black = floor($black * 100);
        return array(
            'c' => $cyan,
            'm' => $magenta,
            'y' => $yellow,
            'k' => $black,
        );
    }

    public function converthextorgb($hex){
        list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
        return 'rgb('.$r.','.$g.','.$b.')';
    }

    private function saveGenerateImages($svgImagesNames) {
        $designImagesCollection = $this->designImagesFactory->create()->getCollection()
        ->addFieldToFilter('design_id', $svgImagesNames['designId'])
        ->addFieldToFilter('product_image_id', $svgImagesNames['image_id'])
        ->addFieldToFilter('design_image_type', array('in' => array('svg')));
        $designImagesCollection->walk('delete');

        $designImagesModel = $this->designImagesFactory->create();
        $designImagesModel->setDesignId($svgImagesNames['designId'])
        ->setDesignImageType('svg')
        ->setProductImageId($svgImagesNames['image_id'])
        ->setImagePath(str_replace('\\', '/', $svgImagesNames['name']));
        $designImagesModel->save();

        // set flag value
        $designOrderCollectionModel = $this->designOrderCollection->create()->addFieldToFilter('design_id', ['eq' => $svgImagesNames['designId']]);
        foreach ($designOrderCollectionModel as $value) {
            if ($value->getFlag() == 1) {
                $value->setFlag(0);
            }
        }
        $designOrderCollectionModel->save();
    }
}
