<?php
/**
 * Created by PhpStorm.
 * User: nakie
 * Date: 7/7/21
 * Time: 12:15 PM
 */

namespace SouthernIns\BuildTool\Traits;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

trait FileSystemHelpers
{


    protected function backupDirectory( $directory, $backupTo ){

        $cp_command = 'cp -R ' . $directory . ' ' . $backupTo;

        $CpyFiles = new Process(explode(' ', $cp_command));
        $CpyFiles->setTimeout(0);
        $CpyFiles->run();

        if (!$CpyFiles->isSuccessful()) {
            throw new ProcessFailedException($CpyFiles);
        }

        //Move Files from EBOverride folder into .ebextensions
//        echo $CpyFiles->getOutput();

    } //- END function backupDirectory()


    protected function replaceDirectory( $directory, $overrideWith ){

        // If overrideWith  folder exists
        if( file_exists( $overrideWith )) {

            // Put Override Files into .ebextensions overwriting any existing files.
            $override_command = 'yes | cp -Rf ' . $overrideWith . '/. ' . $directory . '/' ;

            $OverwriteFiles = Process::fromShellCommandline( $override_command );
            $OverwriteFiles->setTimeout( 0 );
            $OverwriteFiles->run();

            if( !$OverwriteFiles->isSuccessful() ){

                throw new ProcessFailedException( $OverwriteFiles );

            }

            echo $OverwriteFiles->getOutput();

        } //- END if ebOverrides  exists


    } //- END function replaceDirectory()


    protected function restoreDirectory( $directory, $backupDirectory ){

        if( file_exists( $backupDirectory )){

            $remove_config_cmd = 'rm -rf ' . $directory ;

            $removeConfig = new Process( explode(' ', $remove_config_cmd)  );
            $removeConfig->setTimeout( 0 );
            $removeConfig->run();

            if( !$removeConfig->isSuccessful() ){

                throw new ProcessFailedException( $removeConfig );

            }

            echo $removeConfig->getOutput();

            rename( $backupDirectory, $directory );

        }

    } //- END function restoreDirectory()


}