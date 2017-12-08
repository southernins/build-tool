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

    /**
     * Run npm production
     */
    static function runProduction(){

        // Install Node
        // curl -sL https://deb.nodesource.com/setup_8.x | sudo -E bash -
        // sudo apt-get install -y nodejs
        $npmProduction = new Process( "npm run production" );
        $npmProduction->start();

        $iterator = $npmProduction->getIterator( $npmProduction::ITER_SKIP_ERR | $npmProduction::ITER_KEEP_OUTPUT );
        foreach( $iterator as $data ){
            echo $data."\n";
        }

    } //- END function runProduction()

    /**
     * Run npm dev
     */
    static function runDev(){

        $npmProduction = new Process( "npm run dev" );
        $npmProduction->start();

        $iterator = $npmProduction->getIterator( $npmProduction::ITER_SKIP_ERR | $npmProduction::ITER_KEEP_OUTPUT );
        foreach( $iterator as $data ){
            echo $data."\n";
        }

    } //- END function runDev()


} //- END Class NPM{}