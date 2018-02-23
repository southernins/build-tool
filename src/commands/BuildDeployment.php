<?php

namespace SouthernIns\BuildTool\Commands;

use SouthernIns\BuildTool\Shell\Git;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

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

        // Label non production builds with the current Environment
        if( !$this->isProduction( $environment ) ){
            $version = $version ."_" . $environment;
        }

        return $version;

    } //- END function buildVersion()

    protected function buildName(){

        return Config::get( 'build-tool.name' );

    }

    /**
     * Copy selected env file from environments folder to root project env
     * prior to build
     *
     * @param $environment laravel environment file to use
     */
    protected function setEnvironmentFile( $environment ){

        rename( base_path() . '/.env', base_path() . '/.env.previous' );

        $newEnv = base_path() . "/environments/.env." . $environment;

        copy( $newEnv, base_path() . "/.env" );


    } //- END function setEnvironmentFile()

    protected function restoreEnvironmentFile(){
//        $envFile = base_path() . '/.env.previous';

        rename( base_path() . '/.env.previous', base_path() . '/.env' );
    }

    protected function isNotBranch( $branchName ){
        return  Git::branchName() != $branchName;
    }

    /**
     * Check for App config values, sets default if not found
     *
     */
    protected function checkConfig(){

        // If no config file was found use defaults
        if( !Config::has( 'build-tool' ) ){
            $configArr = include __DIR__ . '/../config/build-tool.php';
            Config::set( 'build-tool', $configArr );
        }

    } //- END checkConfig()


} //- END trait BuildDeployment {}