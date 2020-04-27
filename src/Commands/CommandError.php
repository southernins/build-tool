<?php
/**
 * Created by PhpStorm.
 * User: Nathan
 * Date: 2/23/2018
 * Time: 11:17 AM
 */

namespace SouthernIns\BuildTool\Commands;


//use Throwable;
use Illuminate\Console\Command;


class CommandError extends \RuntimeException {

    public function __construct( BuildCommand $artisanCommand, $message = '' ){

        if( $message ){
            $artisanCommand->error( $message );
        }

//        $artisanCommand->error( "Build Process Terminated!" );

        parent::__construct( 'Build command resulted in an error, Process Terminated!' );

    }

}