<?php
/**
 * Created by PhpStorm.
 * User: nakie
 * Date: 7/4/21
 * Time: 7:35 AM
 */

namespace SouthernIns\BuildTool\Helpers;


use SouthernIns\BuildTool\Traits\FileSystemHelpers;


class ElasticBeanstalkExtensionsManager
{

    use FileSystemHelpers;


    /**
     * ElasticBeanstalk configuration Directory
     * @var string
     */
    protected $extensions_dir = '.ebextensions';


    /**
     * Temp Directory to store current Configuration during build process
     * @var string
     */
    protected $backup_dir = '.ebextensions.previous';


    /**
     * move ebextensions folder to backup location
     */
    public function backupExtensions(){

        $backup_path = base_path() . '/' . $this->backup_dir;

        $this->backupDirectory( $this->getExtensionsFolder() , $backup_path );

    } //- END function backupExtensions()


    /**
     *
     * @param $environment
     */
    public function setExtensions( $environment ){

        $overrides_path  = $this->getExtensionsFolder( $environment ) ;
        $extensions_path = $this->getExtensionsFolder();

        $this->replaceDirectory( $extensions_path, $overrides_path );

    } //- END function setExtensions


    /**
     * Restore Previous .ebextensions after Deployment Package Created
     */
    public function restoreExtensions( ){

        $original_config = base_path() . '/' . $this->backup_dir;
        $build_config    = base_path() . '/' . $this->extensions_dir;

        $this->restoreDirectory( $build_config, $original_config );

    } //- END function restoreExtensions()


    /**
     * @param null $buildEnvironment
     * @return string
     */
    public function getExtensionsFolder( $buildEnvironment = null ){

        return base_path() . '/' . $this->getExtensionsFolderName( $buildEnvironment );

    }


    /**
     * @param null $buildEnvironment
     * @return string
     */
    public function getExtensionsFolderName( $buildEnvironment = null ){

        if( is_null( $buildEnvironment )){
            return $this->extensions_dir;
        }

        return $this->extensions_dir . '.' . $buildEnvironment;

    }


}