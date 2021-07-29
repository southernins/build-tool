<?php
/**
 * Created by PhpStorm.
 * User: nakie
 * Date: 7/4/21
 * Time: 7:35 AM
 */

namespace SouthernIns\BuildTool\Helpers;


use SouthernIns\BuildTool\Traits\FileSystemHelpers;


class ElasticBeanstalkPlatformManager
{

    use FileSystemHelpers;


    /**
     * ElasticBeanstalk Platform Directory
     * @var string
     */
    protected $platform_dir = '.platform';


    /**
     * Temp Directory to store current Configuration during build process
     * @var string
     */
    protected $backup_dir = '.platform.previous';


    /**
     * @param $environment
     */
    public function backupPlatform(){

        $backup_path    = base_path() . '/' . $this->backup_dir;

        $this->backupDirectory( $this->getPlatformFolder(), $backup_path );

    } //- END function backupPlatform


    /**
     * @param $environment
     */
    public function setPlatform( $environment ){

        $overrides_path = $this->getPlatformFolder( $environment );
        $platform_path  = $this->getPlatformFolder();

        $this->replaceDirectory( $platform_path, $overrides_path );

    } //- END function overrideEBConfig

    /**
     * Restore Previous .platform after Deployment Package Created
     */
    public function restorePlatform( ){

        $original_platform = base_path() . '/' . $this->backup_dir;

        $build_platform = base_path() . '/' . $this->platform_dir;

        $this->restoreDirectory( $build_platform, $original_platform );

    } //- END function restorePlatform()


    /**
     * @param null $buildEnvironment
     * @return string
     */
    public function getPlatformFolder( $buildEnvironment = null ){

        return base_path() . '/' . $this->getPlatformFolderName( $buildEnvironment );

    }


    /**
     * @param null $buildEnvironment
     * @return string
     */
    protected function getPlatformFolderName( $buildEnvironment = null ){

        if( is_null( $buildEnvironment )){
            return $this->platform_dir;
        }

        return $this->platform_dir . '.' . $buildEnvironment;
    }

}