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


    public function  __construct( $command ){

        $this->command = $command;

        $call = 'executeCommand' . ucfirst( gettype( $this->command ) );

        return $this->$call();


    }


    protected function executeCommandArray(){

        $process = new Process( $this->command );
        $process->setTimeout( 0 );
        $process->mustRun();

        $this->processObject = $process;

    }


    protected function executeCommandString(){

        $process = Process::fromShellCommandline( $this->command );
        $process->setTimeout( 0 );
        $process->mustRun();

        $this->processObject = $process;

    }


    public function __call( $name, $arguments ){

        return $this->processObject->$name( $arguments );

    }

}