<?php
/**
 * Created by PhpStorm.
 * User: Nathan
 * Date: 2/22/2018
 * Time: 1:54 PM
 */
namespace SouthernIns\BuildTool;
class ComposerScripts {



    public function preInstallCmd () {}
    public function postInstallCmd () {}
    public function preUpdateCmd () {}
    public function postUpdateCmd () {}
    public function postStatusCmd () {}
    public function preArchiveCmd () {}
    public function postArchiveCmd () {}
    public function preAutoloadDump () {}
    public function postAutoloadDump () {}
    public function postRootPackage () {}
    public function postCreateProject () {}
    public function preDependenciesSolving () {}
    public function postDependenciesSolving () {}
    public function prePackageInstall () {}
    public function postPackageInstall () {
        $config_file = base_path() . '/config/build-tool.php';

        if( !file_exists( $config_file ) ){

            copy( './config/build-tool.php', base_path() . '/config/build-tool.php' );
        }
    }
    public function prePackageUpdate () {}
    public function postPackageUpdate () {}
    public function prePackageUninstall () {}
    public function postPackageUninstall () {}

} //- END class PostInstall {}