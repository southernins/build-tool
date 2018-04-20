<?php

namespace SouthernIns\BuildTool\Commands;

use SouthernIns\BuildTool\Shell\Git;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

trait BuildDeployment {


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

    /**
     * Create Build name from App Name
     *
     * @return mixed
     */
    protected function buildName(){

        return Config::get( 'build-tool.name' );

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