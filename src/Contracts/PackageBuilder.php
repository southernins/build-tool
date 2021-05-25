<?php
/**
 * Created by PhpStorm.
 * User: nakie
 * Date: 5/22/21
 * Time: 1:53 PM
 */

namespace SouthernIns\BuildTool\Contracts;


interface PackageBuilder
{


    public function beforePackageBuild();

    public function packageBuild();

    public function afterPackageBuild();


}