<?php

namespace SouthernIns\BuildTool\Commands;

use SouthernIns\BuildTool\Shell\Git;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

//use Dotenv;
//use InvalidArgumentException;

trait ManageEnvironment {


    /**
     * Copy selected env file from environments folder to root project env
     * prior to build
     *
     * @param $environment laravel environment file to use
     * @throws \Exception  Stops build process if there is no .env file for the given $environment.
     */
    protected function setEnvironmentFile( $environment ){

        $newEnv = base_path() . "/.env." . $environment;

        // rename and copy new env IF file exists.
        if( file_exists( $newEnv )) {

            rename( base_path() . '/.env', base_path() . '/.env.previous' );

            // Try and restore .env file if copy fails.
            try{
                copy( $newEnv, base_path() . "/.env" );
            } catch( \Exception $exception ){
                $this->restoreEnvironmentFile();
                $this->terminateCommand( $exception->getMessage() );
            }

        } else {
            
            // if .env.$environment does NOT exist kill command
            $this->terminateCommand( "Environment file for " . $environment . " was not found!" );
        }

    } //- END function setEnvironmentFile()

    /**
     * Restore Previous Environment file after Deployment Package created
     *
     */
    protected function restoreEnvironmentFile(){

        rename( base_path() . '/.env.previous', base_path() . '/.env' );

    } //- END function restoreEnvironmentFile()


    /**
     * Look for .ebextensions folder for current envrionment.
     * move env specific ebconfig files into Main .ebextensions folder before
     * deployment package is created.
     *
     * @param $environment
     * @throws ProcessFailedException  if Command Processes do not complete successfully
     *
     */
    protected function overrideEBConfig( $environment ){

        $ebOverrides = base_path() . '/.ebextensions.' . $environment;

        // If ebOverrides folder exists for current environment.
        if( file_exists( $ebOverrides )) {

            // Copy .ebextensions folder to .ebextensions_previous
            $cpCommand = 'cp -R .ebextensions .ebextensions.previous';

            $cpyFiles = new Process( $cpCommand  );
            $cpyFiles->setTimeout( 0 );
            $cpyFiles->run();

            if( !$cpyFiles->isSuccessful() ){
                throw new ProcessFailedException( $cpyFiles );
            }

            //Move Files from EBOverride folder into .ebextensions
            echo $cpyFiles->getOutput();

            // Put Override Files into .ebextensions
            $overrideCommand = 'yes | cp -Rf ' . $ebOverrides . '/. .ebextensions/' ;

            $overwriteFiles = new Process( $overrideCommand  );
            $overwriteFiles->setTimeout( 0 );
            $overwriteFiles->run();

            if( !$overwriteFiles->isSuccessful() ){

                throw new ProcessFailedException( $overwriteFiles );

            }

            echo $overwriteFiles->getOutput();

        } //- END if ebOverrides  exists


    } //- END function overrideEBConfig

    /**
     * Restore Previous .ebextensions after Deployment Package Created
     */
    protected function restoreEBConfig( ){

        $previousConfig = base_path() . '/.ebextensions.previous';

        if( file_exists( $previousConfig )){

            // Put Override Files into .ebextensions
            $removeConfigCommand = 'rm -rf ' . base_path() . '/.ebextensions' ;

            $removeConfig = new Process( $removeConfigCommand  );
            $removeConfig->setTimeout( 0 );
            $removeConfig->run();

            if( !$removeConfig->isSuccessful() ){

                throw new ProcessFailedException( $removeConfig );

            }

            echo $removeConfig->getOutput();


            rename( base_path() . '/.ebextensions.previous', base_path() . '/.ebextensions' );

        }

    } //- END function restoreEBConfig()

} //- END trait BuildDeployment {}