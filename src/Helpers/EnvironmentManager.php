<?php
/**
 * Created by PhpStorm.
 * User: nakie
 * Date: 7/3/21
 * Time: 12:29 PM
 */

namespace SouthernIns\BuildTool\Helpers;



class EnvironmentManager
{


    /**
     * @var string
     */
    public  $backupFile = '.env.previous';


    /**
     * @var string
     */
    public  $envFile = '.env';


    /**
     * @param null $buildEnvironment
     * @return string
     */
    public function getEnvironmentFile( $buildEnvironment = null ){

        return base_path() . '/' . $this->getEnvironmentFileName( $buildEnvironment );

    }


    /**
     * @param null $buildEnvironment
     * @return string
     */
    public function getEnvironmentFileName( $buildEnvironment = null ){

        if( is_null( $buildEnvironment )){
            return $this->envFile;
        }

        return $this->envFile . '.' . $buildEnvironment;

    }


    /**
     * @param null $buildEnvironment
     */
    public function setBuildEnvironment( $buildEnvironment = null ){

        copy(
            $this->getEnvironmentFile( $buildEnvironment ),
            base_path() . "/.env"
        );

    } //- END function setBuildEnvironment()


    /**
     * Backup local Environment file before creating deployment pacakge
     */
    public function backupEnvironmentFile(){

        rename( base_path() . '/.env', base_path() . '/.env.previous' );

    } //- END function backupEnvironmentFile()


    /**
     * Restore Previous Environment file after Deployment Package created
     *
     */
    public function restoreEnvironmentFile(){

        rename( base_path() . '/.env.previous', base_path() . '/.env' );

    } //- END function restoreEnvironmentFile()


}