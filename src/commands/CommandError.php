<?php
/**
 * Created by PhpStorm.
 * User: Nathan
 * Date: 2/23/2018
 * Time: 11:17 AM
 */

namespace SouthernIns\BuildTool\commands;


//use Throwable;
use Illuminate\Console\Command;

class CommandError extends \RuntimeException {

    public function __construct( Command $artisanCommand, $message = [] ){

        foreach( $message as $line ){
            $artisanCommand->error( $line );
        }

        $artisanCommand->error( "Build Process Terminated!" );

        parent::__construct( 'Build command resulted in an error, Process Terminated!' );

    }

}