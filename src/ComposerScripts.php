<?php
/**
 * Created by PhpStorm.
 * User: Nathan
 * Date: 2/22/2018
 * Time: 1:54 PM
 */
namespace SouthernIns\BuildTool;


class ComposerScripts {



    public function preInstallCmd ( Event $event ) {
        $event->getIO()->write( "preInstallCmd" );
    }
    public function postInstallCmd ( Event $event ) {
        $event->getIO()->write( "postInstallCmd" );
    }
    public function preUpdateCmd ( Event $event ) {
        $event->getIO()->write( "preUpdateCmd" );
    }
    public function postUpdateCmd ( Event $event ) {
        $event->getIO()->write( "postUpdateCmd" );
    }
    public function postStatusCmd ( Event $event ) {
        $event->getIO()->write( "postStatusCmd" );
    }
    public function preArchiveCmd ( Event $event ) {
        $event->getIO()->write( "preArchiveCmd" );
    }
    public function postArchiveCmd ( Event $event ) {
        $event->getIO()->write( "postArchiveCmd" );
    }
    public function preAutoloadDump ( Event $event ) {
        $event->getIO()->write( "preAutoloadDump" );
    }
    public function postAutoloadDump ( Event $event ) {
        $event->getIO()->write( "postAutoloadDump" );
    }
    public function postRootPackage ( Event $event ) {
        $event->getIO()->write( "postRootPackage" );
    }
    public function postCreateProject ( Event $event ) {
        $event->getIO()->write( "postCreateProject" );
    }
    public function preDependenciesSolving ( Event $event ) {
        $event->getIO()->write();
    }
    public function postDependenciesSolving ( Event $event ) {
        $event->getIO()->write( "preDependenciesSolving" );
    }
    public function prePackageInstall ( Event $event ) {
        $event->getIO()->write( "prePackageInstall" );
    }
    public function postPackageInstall ( Event $event ) {
        $config_file = base_path() . '/config/build-tool.php';

        if( !file_exists( $config_file ) ){

            copy( './config/build-tool.php', base_path() . '/config/build-tool.php' );
        }
        $event->getIO()->write( "postPackageInstall" );

    }
    public function prePackageUpdate ( Event $event ) {
        $event->getIO()->write( "prePackageUpdate" );
    }
    public function postPackageUpdate ( Event $event ) {
        $event->getIO()->write( "postPackageUpdate" );
    }
    public function prePackageUninstall ( Event $event ) {
        $event->getIO()->write( "prePackageUninstall" );
    }
    public function postPackageUninstall ( Event $event ) {
        $event->getIO()->write( "postPackageUninstall" );
    }

} //- END class PostInstall {}