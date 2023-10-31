<?php
$baseDir =  CHOTU_START_BASE_DIR;

$auto_load_class =  array(
  'Chotu_Start_Product_Admin' => $baseDir . 'admin/includes/class-chotu-start_product.php',
  'Chotu_Start_Products'      => $baseDir . 'public/includes/class-chotu-start-products.php',
  'Chotu_Start_Checkout'      => $baseDir . 'public/includes/class-chotu-start-checkout.php'
);
$GLOBALS['auto_load_class'] = $auto_load_class;

function woo_load_classes($cls){
    global $auto_load_class;
    foreach ($auto_load_class as $key => $value) {
      woo_auto_loader($value);
    }
}

function woo_auto_loader($class_name) {
    $file_name =  $class_name;
    if (file_exists($file_name)) {
      require_once($file_name);
    }
}

spl_autoload_register('woo_load_classes');

?>