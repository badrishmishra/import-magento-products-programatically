<?php
set_time_limit(0);
ini_set('memory_limit', '1024M');
include_once "app/Mage.php";
include_once "downloader/Maged/Controller.php";

Mage::init();

$app = Mage::app('default');

//The category names should be exactly the same name from the csv file where the id is the corresponding category id in magento. This is done when the csv file doesn't contain ids for categories but the name of categories.
$categories = array(
    'Category 1' => 3,
    'Category 2' => 4,
    'Category 3' =>5,
    'Category 4'=>6,
 
    
);
$row = 0;

if (($handle = fopen("productsheet.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        echo 'Importing product: '.$data[0].'<br />';
        foreach($data as $d)
        {
            echo $d.'<br />';
        }
        $num = count($data);
        //echo "<p> $num fields in line $row: <br /></p>\n";
        $row++;
    
        if($row == 1){ continue;}
        
           $product = Mage::getModel('catalog/product');
 
        $product->setSku($data[0]);
        $product->setName($data[3]);
        $product->setDescription($data[6]);
        $product->setShortDescription('');
        $product->setManufacturer($data[20]);
        $product->setPrice($data[9]);
        $product->setTypeId('simple');
		
        
        $fullpath = 'media/import';
		//echo $data[10];
        $ch = curl_init($data[2]);
		 echo  $image_url = 'http://localhost/yoursite/media/import'.$data[2].'.jpg';
		curl_setopt($ch, CURLOPT_URL,$image_url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        $rawdata=curl_exec($ch);
        curl_close ($ch);
       echo  $fullpath = $fullpath.$data[2].'.jpg';
        /*if(file_exists($fullpath)) {
            unlink($fullpath);
        }*/
       // $fp = fopen($fullpath,'x+');
        file_put_contents($fullpath, $rawdata);
        //fclose($fp);
       $product->addImageToMediaGallery($fullpath, array('image', 'small_image', 'thumbnail'), false, false);
	   
	   
        
       /* $fullpath = 'media/import/';
        $ch = curl_init ($data[2]);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        $rawdata=curl_exec($ch);
        curl_close ($ch);
        $fullpath = $fullpath.$data[2].'.jpg';
        if(file_exists($fullpath)) {
            unlink($fullpath);
        }
        $fp = fopen($fullpath,'x');
        fwrite($fp, $rawdata);
        fclose($fp);
       $product->addImageToMediaGallery($fullpath, 'small_image', false);
        
        $fullpath = 'media/import/';
        $ch = curl_init ($data[2]);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        $rawdata=curl_exec($ch);
        curl_close ($ch);
		echo $data[2];
        echo $fullpath = $fullpath.$data[2].'.jpg';
        if(file_exists($fullpath)) {
            unlink($fullpath);
        }
        $fp = fopen($fullpath,'x');
        fwrite($fp, $rawdata);
        fclose($fp);
        $product->addImageToMediaGallery($fullpath, 'image', false);
        
        */
        
        $product->setAttributeSetId(4); // need to look this up
        $product->setCategoryIds(array($data[1])); // need to look these up
        $product->setWeight(0);
        $product->setTaxClassId(2); // taxable goods
        $product->setVisibility(4); // catalog, search
        $product->setStatus(1); // enabled
        
        // assign product to the default website
        $product->setWebsiteIds(array(Mage::app()->getStore(true)->getWebsite()->getId()));
         
     $product->save();      
      
$stockItem = Mage::getModel('cataloginventory/stock_item');
            $stockItem->assignProduct($product);
            $stockItem->setData('is_in_stock', 1);
            $stockItem->setData('stock_id', 1);
            $stockItem->setData('store_id', 1);
            $stockItem->setData('manage_stock', 1);
            $stockItem->setData('use_config_manage_stock', 0);
            $stockItem->setData('min_sale_qty', 1);
            $stockItem->setData('use_config_min_sale_qty', 0);
            $stockItem->setData('max_sale_qty', 1000);
            $stockItem->setData('use_config_max_sale_qty', 0);
            $stockItem->setData('qty', $data[4]);
            $stockItem->save();
			



       // $product->save();    
       
        
    }
    fclose($handle);
    
}


?>