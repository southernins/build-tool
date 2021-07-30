<?php
/**
 * Created by PhpStorm.
 * User: nakie
 * Date: 5/22/21
 * Time: 8:08 PM
 */

namespace SouthernIns\BuildTool;


use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use SouthernIns\BuildTool\BuildRules\ComposerInstalledRule;
use SouthernIns\BuildTool\BuildRules\NpmInstalledRule;
use SouthernIns\BuildTool\BuildRules\ProtectedEnvironmentRule;
use SouthernIns\BuildTool\BuildRules\EnvFileExistsRule;
use SouthernIns\BuildTool\Contracts\PackageBuilder;
use SouthernIns\BuildTool\Exceptions\BuildValidationException;
use SouthernIns\BuildTool\Helpers\CacheManager;
use SouthernIns\BuildTool\Helpers\Composer;
use SouthernIns\BuildTool\Helpers\ElasticBeanstalkExtensionsManager;
use SouthernIns\BuildTool\Helpers\ElasticBeanstalkPlatformManager;
use SouthernIns\BuildTool\Helpers\EnvironmentManager;
use SouthernIns\BuildTool\Helpers\NPM;
use SouthernIns\BuildTool\Helpers\StorageManager;
use Symfony\Component\Process\Process;


class AwsPackageBuilder implements PackageBuilder
{


    protected $output;

    protected $environment;

    protected $environmentManager;

    protected $ebPlatformManager;

    protected $ebExtensionsManager;

    protected $cacheManager;

    protected $storageManager;

    protected $composerHelper;

    protected $npmHelper;


    public function __construct(
        EnvironmentManager $environmentManager,
        ElasticBeanstalkPlatformManager $ebPlatformManager,
        ElasticBeanstalkExtensionsManager $ebExtensionsManager,
        CacheManager $cacheManager,
        StorageManager $storageManager,
        Composer $composerHelper,
        NPM $npmHelper
    ) {

        $this->environmentManager   = $environmentManager;
        $this->ebPlatformManager    = $ebPlatformManager;
        $this->ebExtensionsManager  = $ebExtensionsManager;
        $this->cacheManager         = $cacheManager;
        $this->storageManager       = $storageManager;
        $this->composerHelper       = $composerHelper;
        $this->npmHelper            = $npmHelper;

        $this->environment = App::environment();

    } //- END function __construct(


    public function buildValidations( $force )
    {

        // Add ENV::CHeck ??

        // Add validations to run
        $validations = [
            new EnvFileExistsRule,
            new ComposerInstalledRule,
            new NpmInstalledRule
        ];

        // skipped if build runs  with --force flag
        if( $force === false ){
            $validations[] = new ProtectedEnvironmentRule;
        }

        $validator = Validator::make(
            [ 'build' => $this->environment ],
            [ 'build' => $validations ]
        );

        if( $validator->fails() ){
            throw new BuildValidationException( $validator->errors()->first( 'build' ) );
        }

    } //- END buildValidations()


    public function beforePackageBuild()
    {

        // Clear Caches
        $this->output->info( Lang::get('build-tool::messages.cache-clear') );
        $this->cacheManager->clearAll();

        // Clear Laravel Logs before production deployment
        if( $this->isProduction() ) {
            $this->output->info( 'Clearing Logs' );
            $this->storageManager->clearLogs();
        }

        $this->output->info( 'Backing up current environment' );
        // Backup Local Environment
        $this->environmentManager->backupEnvironmentFile();
        $this->ebPlatformManager->backupPlatform();
        $this->ebExtensionsManager->backupExtensions();

    } //- END function beforePackageBuild ()


    public function packageBuild()
    {

        $this->output->info( 'Starting build process.');
        // TODO: Implement packageBuild() method.

        // Generate Build Version
        $buildVersion = $this->buildVersion();

        // generate Build Name
        $buildName = $this->buildName();

        // Get list of files to include in build package
        // HAS to be done before cache is cleared.
        $buildFileList = Config::get( 'build-tool.include' );

        // Setup Build Environment
        $this->environmentManager->setBuildEnvironment( $this->environment );
        $this->ebPlatformManager->setPlatform( $this->environment );
        $this->ebExtensionsManager->setExtensions( $this->environment );

        // Run Composer/NPM
        if( $this->isProduction() ){

            $this->composerHelper->installNoDev();
            $this->npmHelper->runProduction();


        } else {

            $this->composerHelper->install();
            $this->npmHelper->runDev();
        }

        // Create Build .zip Package
        // shell class failed here because we uninstall this package in production
        // creating teh class above and executing the command here could work.77
        $this->createBuildFile( $buildName, $buildVersion, $buildFileList );


    } //- END function packageBuild ()


    public function afterPackageBuild()
    {

        // Restore Composer Dependencies
        if( $this->isProduction() ){
            $this->composerHelper->install();
        }

        // Restore ENV File
        $this->environmentManager->restoreEnvironmentFile();

        // Restore Ebextension Files
        $this->ebExtensionsManager->restoreExtensions();

        // Restore Platform Files
        $this->ebPlatformManager->restorePlatform();


    } //- END function afterPackageBuild()


    /**
     * returns true if current environment is set to "production"
     *
     * @param $environment laravel environment to use during build
     *
     * @return bool
     */
    protected function isProduction()
    {

        return ( $this->environment == Config::get( 'build-tool:production-env' ));

    } //- END isProduction()


    /**
     * Generate build version string from date.
     *
     * @param $environment laravel environment to use for build
     *
     * @return string  version of the current build
     */
    protected function buildVersion(){

        $version = Carbon::now()->format( 'Y.m.d.Hi' );

        // Label non production builds with the current Environment
        if( !$this->isProduction() ){
            $version = $version . "_" . $this->environment;
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

        $appName = Config::get( 'app.name' ) ?? "build";

        $buildName = ( !empty( $customName ) ) ? $customName : $appName;

        return Str::slug( $buildName, '_' );

    }

    /**
     * Create the build deployment package.
     *
     * @param $build   Build Name
     * @param $version string version to use when creating build file.
     */
    protected function createBuildFile( $build, $version, $include_list ){

        $this->output->info( "Creating Build File" );

        //        $include_list = Config::get( 'build-tool.include' );

        $include = '';
        if( count( $include_list ) > 0 ){
            $include = '-i ' . implode( ' ', $include_list );
        }

        $build_file = base_path() . '/../' . $build . '_v-' . $version . '.zip';

        // Command ran manually here, a Zip class will not be found after Composer uninstall.
        $command = 'zip -r -q ' . $build_file . ' ./ ' . $include;

        $createBuild = new Process( explode(' ', $command ) );
        $createBuild->setTimeout( 0 );
        $createBuild->run();


        foreach( $createBuild as $type => $data ){
            if( $createBuild::ERR === $type ){
                echo "\n=>" . $data;
            }else{ // $process::ERR === $type
                echo "\n" . $data;
            }
        }

    } //- END function createBuildFile()


    public function setOutput( $output ){

        $this->output = $output;

    } //- END function setOutput ()


} //- END class AWSPackageBuilder {}