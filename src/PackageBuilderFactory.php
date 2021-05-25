<?php
/**
 * Created by PhpStorm.
 * User: nakie
 * Date: 5/23/21
 * Time: 5:21 PM
 */

namespace SouthernIns\BuildTool;


use Illuminate\Support\Facades\Config;

class PackageBuilderFactory
{


    static function makeBuilder(){

        $buildClass = Config::get( 'build-tool.build-class' );
        return new $buildClass();
    }


}