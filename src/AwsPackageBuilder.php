<?php
/**
 * Created by PhpStorm.
 * User: nakie
 * Date: 5/22/21
 * Time: 8:08 PM
 */

namespace SouthernIns\BuildTool;


use SouthernIns\BuildTool\Contracts\PackageBuilder;


class AwsPackageBuilder implements PackageBuilder
{


    public function __construct()
    {

    }

    public function buildValidations(){

        // confirm .env.xxxx exists

        // confirm composer installed

        // confirm npm installed

        // ENV::CHeck

    }


    public function beforePackageBuild()
    {
        // TODO: Implement beforePackageBuild() method.




        // Clear Caches

        // Backup ENV File

        // backup Ebextensions
    }


    public function packageBuild()
    {
        // TODO: Implement packageBuild() method.

        // Generate Build Version

        // generate Build Name

        // Set Build Environment File


        // Set Build EB extension Files


        // if production confirm master branch

        // Composer Install

        // Npm Run

        // Create Build Package
    }


    public function afterPackageBuild()
    {
        // TODO: Implement afterPackageBuild() method.


        // Restore Composer Dependencies

        // Restore ENV File

        // Restore Ebextension Files


    }
    

}