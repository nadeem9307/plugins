<?php
$baseDir =  SC_FUNNEL_DIR;

$autolocaclass =  array(
    'SCFunnelbuilder\\Menu\\ScFunnel_Menus' => $baseDir . 'includes/core/classes/class-scfunnel-admin-menus.php',
    'SCFunnelbuilder\\CPT\\ScFunnel_CPT' => $baseDir . 'includes/core/classes/class-scfunnel-register-funnel.php',
    'SCFunnelbuilder\\Base_Manager' => $baseDir . 'includes/core/classes/abstact-scfunnel-manager.php',
    'SCFunnelbuilder\\Modules\\ScFunnel_Modules_Manager' => $baseDir . 'includes/core/classes/class-scfunnel-module-manager.php',
    'SCFunnelbuilder\\Admin\\Module\\ScFunnel_Admin_Module' => $baseDir . 'admin/modules/abstract-scfunnel-admin-modules.php',
    'SCFunnelbuilder\\Traits\\SingletonTrait' => $baseDir . 'includes/core/traits/singleton-trait.php',
    'SCFunnelbuilder\\Modules\\Admin\\CreateFunnel\\Module' => $baseDir . 'admin/modules/createFunnel/class-scfunnel-create-funnel.php',
    'SCFunnelbuilder\\Modules\\Admin\\Funnels\\Module' => $baseDir . 'admin/modules/funnels/class-scfunnel-funnels.php',
    'SCFunnelbuilder\\Modules\\Admin\\Funnel\\Module' => $baseDir . 'admin/modules/funnel/class-scfunnel-funnel.php',
    'SCFunnelbuilder\\Modules\\Admin\\Settings\\Module' => $baseDir . 'admin/modules/settings/class-scfunnel-settings.php',

    'SCFunnelbuilder\\ScFunnel_functions' => $baseDir . 'includes/class-scfunnel-functions.php',
    
    'SCFunnelbuilder\\Store_Data\\ScFunnel_Store_Data' => $baseDir . 'includes/core/store-data/interface-scfunnel-store-data.php',
    'SCFunnelbuilder\\Store_Data\\ScFunnel_Abstract_Store_data' => $baseDir . 'includes/core/store-data/abstact-scfunnel-store-cpt.php',
    
    'SCFunnelbuilder\\Store_Data\\ScFunnel_Funnel_Store_Data' => $baseDir . 'includes/core/store-data/class-scfunnel-store-funnel-data.php',
    'SCFunnelbuilder\\Store_Data\\ScFunnel_Steps_Store_Data' => $baseDir . 'includes/core/store-data/class-scfunnel-store-steps-data.php',
    'SCFunnelbuilder\\Metas\\ScFunnel_Step_Meta_keys' => $baseDir . 'includes/core/store-data/class-scfunnel-step-metas.php',

    'SCFunnelbuilder\\Exception\\ScFunnel_Api_Exception' => $baseDir . 'includes/core/exception/class-scfunnel-api-exception.php',
    'SCFunnelbuilder\\Exception\\ScFunnel_Data_Exception' => $baseDir . 'includes/core/exception/class-scfunnel-data-exception.php',

    'db1766888a4f96ab813d6f6a38125eb9' => $baseDir . 'includes/core/wp-ajax-helper/src/functions.php',
    'PhilipNewcomer\\SC_Ajax_Helper\\Frontend' => $baseDir . 'includes/core/wp-ajax-helper/src/components/Frontend.php',
    'PhilipNewcomer\\SC_Ajax_Helper\\Handler' => $baseDir . 'includes/core/wp-ajax-helper/src/components/Handler.php',
    'PhilipNewcomer\\SC_Ajax_Helper\\Responder' => $baseDir . 'includes/core/wp-ajax-helper/src/components/Responder.php',
    'PhilipNewcomer\\SC_Ajax_Helper\\Utility' => $baseDir . 'includes/core/wp-ajax-helper/src/components/Utility.php',
    'PhilipNewcomer\\SC_Ajax_Helper\\Validations' => $baseDir . 'includes/core/wp-ajax-helper/src/components/Validations.php',
    'PhilipNewcomer\\SC_Ajax_Helper\\Validator' => $baseDir . 'includes/core/wp-ajax-helper/src/components/Validator.php',


    'SCFunnelbuilder\\Rest\\Controllers\\ScFunnel_REST_Controller' => $baseDir . 'includes/core/rest/Controllers/class-scfunnel-rest-controller.php',
    'SCFunnelbuilder\\Rest\\Rest_Server' => $baseDir . 'includes/core/rest/class-rest-server.php',
    'SCFunnelbuilder\\Rest\\Controllers\\ScFunnelController' => $baseDir . 'includes/core/rest/Controllers/class-scfunnel-controller.php',
    // 'SCFunnelbuilder\\Rest\\Controllers\\GutenbergCSSController' => $baseDir . '/includes/core/rest-api/Controllers/class-gutenberg-css-controller.php',
    // 'SCFunnelbuilder\\Rest\\Controllers\\OrderBumpController' => $baseDir . '/includes/core/rest-api/Controllers/class-orderbump-controller.php',
    'SCFunnelbuilder\\Rest\\Controllers\\ScProductsController' => $baseDir . 'includes/core/rest/Controllers/class-scfunnel-products-controller.php',
    'SCFunnelbuilder\\Rest\\Controllers\\ScRemoteTemplatesController' => $baseDir . 'includes/core/rest/Controllers/class-scfunnel-remote-templates-controller.php',
    'SCFunnelbuilder\\Rest\\Controllers\\PercentageSplitController' => $baseDir . 'includes/core/rest/Controllers/class-scfunnel-percentage-split-controller.php',
);
$GLOBALS['autolocaclass'] = $autolocaclass;

function scfunnel_load_classes($cls){
    global $autolocaclass;
    foreach ($autolocaclass as $key => $value) {
        scfunnel_autoloader($value);
    }
    // die;
}

function scfunnel_autoloader($class_name) {
    $file_name =  $class_name;
    // echo "<br />".$file_name;   
    if (file_exists($file_name)) {
      require_once($file_name);
    }
  }
spl_autoload_register('scfunnel_load_classes');

?>