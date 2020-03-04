<?php
/**
 *
 */

namespace SouthernIns\BuildTool\Commands;

use SouthernIns\BuildTool\Shell\Git;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

trait BuildDeployment {


    /**
     * Generate build version string from date.
     *
     * @param $environment laravel environment to use for build
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

        $customName = Config::get( 'build-tool.name' );

//        $buildName = env( 'APP_NAME', 'build' );
        $appName = Config::get( 'app.name' ) ?? "build" ;

        $buildName = ( !empty( $customName ) ) ? $customName : $appName;

        return  Str::slug( $buildName , '_' );

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

    /**
     * returns true if current environment is set to "production"
     *
     * @param $environment laravel environment to use during build
     * @return bool
     */
    protected function isProduction( $environment ){

        return ( $environment == "production" );

    } //- END isProduction()


} //- END trait BuildDeployment {}
