<?php
/**
 * Created by PhpStorm.
 * User: Nathan
 * Date: 2/22/2018
 * Time: 1:54 PM
 */
namespace SouthernIns\BuildTool;
class PostInstall {

    // Initialization function to run following composer install
    public function init(){
        $config_file = base_path() . '/config/build-tool.php';

        if( !file_exists( $config_file ) ){

            copy( './config/build-tool.php', base_path() . '/config/build-tool.php' );
        }


    } //- END function init()

} //- END class PostInstall {}