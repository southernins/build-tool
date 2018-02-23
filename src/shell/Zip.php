<?php
/**
 * Created by PhpStorm.
 * User: Nathan
 * Date: 2/23/2018
 * Time: 10:34 AM
 */

namespace SouthernIns\BuildTool\shell;


class Zip {

    public function buildFile( $fileName, $includeFiles ){
//        $command = 'zip -r -q ' . $this->projectPath . '_v-' . $version .'.zip ./ ' . $include ;
        $command = 'zip -r -q ' . $fileName . ' ./ ' . $includeFiles ;

        $createBuild = new Process( $command  );
        $createBuild->run();

        if( !$createBuild->isSuccessful() ){

//            $this->handleCommandError();
            if( $createBuild->getExitCode() == 127 ){
                $this->terminateCommand( "Zip Command failed, please confirm it is installed ( sudo apt-get install zip )" );
            }

            throw new ProcessFailedException( $createBuild );

        }

        return $createBuild->getOutput();

    } //- END function buildFile()

} //- END class Zip
