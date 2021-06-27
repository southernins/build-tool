<?php
/**
 * Created by PhpStorm.
 * User: nakie
 * Date: 5/9/21
 * Time: 10:12 PM
 */

namespace SouthernIns\BuildTool;


use Symfony\Component\Process\Process;

class ShellCommand
{

    protected $command;


    protected $processObject;


    public function  __construct( $command = null, $input = [], $deferred = false ){

        $this->commandFactory( $command );

        $this->handleCommandInput( $input );

        if( $deferred === false ){

            $this->runCommand( );

        }

        return $this->processObject;

    }

    public function runCommand(){

        $this->processObject->setTimeout( 0 );
        $this->processObject->mustRun();
    }


    /**
     * create Process Object based on $command argument type
     */
    protected function commandFactory( $command ){

        $executeFunction = 'executeCommand' . ucfirst( gettype( $command ) );

        $this->$executeFunction( $command );

    }


    protected function executeCommandArray( $command ){

        $this->processObject = new Process( $command );

    }


    protected function executeCommandString( $command ){

        $this->processObject = Process::fromShellCommandline( $command );

    }

    protected function handleCommandInput( $inputArray = [] ){

        foreach( $inputArray as $input ){

            $this->processObject->setInput( $input );
        }
    }



    public function __call( $name, $arguments ){

        return $this->processObject->$name( $arguments );

    }

}