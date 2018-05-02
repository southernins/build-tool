<?php
/**
 * Created by PhpStorm.
 * User: nakie
 * Date: 11/25/17
 * Time: 4:09 PM
 */

namespace SouthernIns\BuildTool\Shell;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


class NPM {


    // TODO:: Confirm Node AND npm are installed

    // TODO:: Confirm NPM Install has been ran.... check for node_modules???


    /**
     * Run npm production
     */
    static function runProduction(){

        self::linuxSetup();

        // Install Node
        // curl -sL https://deb.nodesource.com/setup_8.x | sudo -E bash -
        // sudo apt-get install -y nodejs
        $npmProduction = new Process( "npm run production" );
        $npmProduction->setTimeout(180);
        $npmProduction->start();


        foreach ($npmProduction as $type => $data) {
            if ($npmProduction::ERR === $type) {
                echo "\n=>".$data;
            } else { // $process::OUT === $type
                echo "\n".$data;
            }
        }

//        $iterator = $npmProduction->getIterator( $npmProduction::ITER_SKIP_ERR | $npmProduction::ITER_KEEP_OUTPUT );
//        foreach( $iterator as $data ){
//            echo $data."\n";
//        }

    } //- END function runProduction()

    /**
     * Run npm dev
     */
    static function runDev(){

        self::linuxSetup();

        $npmDev = new Process( "npm run dev" );
        $npmDev->setTimeout(180);
        $npmDev->start();

        foreach ($npmDev as $type => $data) {
            if ($npmDev::ERR === $type) {
                echo "\n=>".$data;
            } else { // $process::ERR === $type
                echo "\n".$data;
            }
        }

    } //- END function runDev()

    /**
     * Linux Setup script for NPM
     * Currently Installs optipng-bin vendor dependencies for Linux
     * Install on Windows only downloads optipng.exe
     */
    static function linuxSetup(){

        if( !file_exists( base_path() . '/node_modules/optipng-bin/vendor/optipng' ) ){

            $linuxSetup = new Process( "node " . base_path() . "/node_modules/optipng-bin/lib/install.js" );
            $linuxSetup->setTimeout(180);
            $linuxSetup->start();

            foreach ($linuxSetup as $type => $data) {
                if ($linuxSetup::ERR === $type) {
                    echo "\n=> ".$data;
                } else { // $process::OUT === $type
                    echo "\n".$data;
                }
            }

        }

    } //- END function linuxSetup()


} //- END Class NPM{}