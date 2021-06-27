<?php
/**
 * Created by PhpStorm.
 * User: nakie
 * Date: 11/25/17
 * Time: 4:53 PM
 */

namespace SouthernIns\BuildTool\Helpers;

use SouthernIns\BuildTool\Exceptions\GitException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


class Git {

    // TODO:: Confirm git is installed


    /**
     * function to get the Current Branch Name
     *
     * @return string
     */
    static function branchName(){

        // INSTALL git in Guest OS
        // sudo apt-get install git
        // Get current Git Branch name
        $gitBranch = new Process( ['git', 'rev-parse', '--abbrev-ref', 'HEAD'] );
        $gitBranch->setTimeout(180);
        $gitBranch->run();

        if( !$gitBranch->isSuccessful() ){
            throw new ProcessFailedException( $gitBranch );
        }

        // return Current Git Branch Name from command output
        return trim( $gitBranch->getOutput() );

    } //- END function branchName()


    static function isBranch( $branchName ){

        try{

            return static::branchName() == $branchName;

        } catch ( \Exception $e ){
            throw new GitException( 'Git::isBranch check caused an exception' );
        }
    }

} //- END class Git{}
