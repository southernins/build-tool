<?php
/**
 * Created by PhpStorm.
 * User: nakie
 * Date: 11/25/17
 * Time: 4:05 PM
 */

namespace SouthernIns\BuildTool\Shell;

use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;


class Composer {

    // TODO:: Confirm composer is installed

    /**
     * run composer install
     *
     */
    static function install(){

        // Run composer install to restore Dev Dependencies
        $composerDev = new Process( ['composer', 'install'] );
        $composerDev->setTimeout(180);
        $composerDev->start();

        $iterator = $composerDev->getIterator( $composerDev::ITER_SKIP_ERR | $composerDev::ITER_KEEP_OUTPUT ) ;
        foreach ( $iterator as $data ) {
            echo $data."\n";
        }

    } //- END function Composer::install()


    /**
     * Run Composer Install --no-dev
     */
    static function installNoDev(){

        // Run composer install --no-dev to prevent Dev Deps from pushing t production
        $composer_prod = new Process( ['composer', 'install', '--no-dev', '--optimize-autoloader', '--no-interaction'] );
        $composer_prod->setTimeout(180);
        $composer_prod->start();

        $iterator = $composer_prod->getIterator( $composer_prod::ITER_SKIP_ERR | $composer_prod::ITER_KEEP_OUTPUT );
        foreach( $iterator as $data ){
            echo $data."\n";
        }

    } //- END function Composer::installNoDev()

    static function checkInstall(){
        return File::exists( base_path() . '/vendor');
    }

} //- END Class Composer{}
