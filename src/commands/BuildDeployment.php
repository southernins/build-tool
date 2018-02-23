<?php

namespace SouthernIns\BuildTool\Commands;

use SouthernIns\BuildTool\Shell\Git;

trait BuildDeployment {


    /**
     * returns true if current environment is set to "production"
     *
     * @param $environment laravel environment to use during build
     *
     * @return bool
     */
    protected function isProduction( $environment ){

        return ( $environment == "production" );

    } //- END isProduction()

    /**
     * Generate build version string from date.
     *
     * @param $environment laravel envrionment to use for build
     * @return string  version of the current build
     */
    protected function buildVersion( $environment ){

        $version = Carbon::now()->format('Y.m.d.Hi');

        if( !$this->isProduction( $environment ) ){

            // Label non production builds with the current Environment
            $version = $version ."_" . $environment;

        } //- END if( is production )

        return $version;

    } //- END function buildVersion()

    protected function buildName(){

        return $name;
    }

    /**
     * Copy selected env file from environments folder to root project env
     * prior to build
     *
     * @param $environment laravel environment file to use
     */
    protected function setEnvironmentFile( $environment ){

        $newEnv = base_path() . "/environments/.env." . $environment;

        // copy specified environment to .env
        if( file_exists( $newEnv )){
            copy( $newEnv, base_path() . "/.env" );
        }  //- END file_exists()

    } //- END function setEnvironmentFile()

    protected function isNotBranch( $branchName ){

        return  Git::branchName() != $branchName;

    }





} //- END trait BuildDeployment {}