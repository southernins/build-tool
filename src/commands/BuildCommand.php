<?php

//namespace App\Console\Commands;
namespace SouthernIns\BuildTool\Commands;

use Carbon\Carbon;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

//use SouthernIns\BuildTool\Shell\Composer;

use SouthernIns\BuildTool\Shell\NPM;
use SouthernIns\BuildTool\Shell\Zip;


class BuildCommand extends Command {

    use BuildDeployment;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'build';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Production Build File for Deployment';


    /**
     * Environment to deploy
     * @var string
     */
    protected $envionrment = 'dev';

    /**
     *
     * @var string
     */
    protected $projectPath = '';

    protected $projectFolder = '';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {

        parent::__construct();

        // Set build envrionment from app envrionment
        // Set with --env on artisan command
        $this->environment = App::environment();

        // Set folderName and Path for project
        $this->projectPath = base_path();
        $dirArr = explode("/", $this->projectPath) ;

        $this->projectFolder = end( $dirArr );

        if( !Config::has( 'build-tool' ) ){

            $configArr = include __DIR__ . '/../config/build-tool.php';
            Config::push( 'build-tool', $configArr );
        }
//        $build_config = Config::get( 'build-tool' );

    } //- END __construct()


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        $this->info( $this->environment );

        $this->build( $this->environment );

    } // END function handle()


    /**
     * run the entire build process
     *
     * @param $environment string laravel environment to use
     *
     */
    protected function build( $environment ){

        $version = $this->buildVersion( $environment );

        $this->info( "Creating $this->projectFolder - $environment Build version $version" );

        $this->clearCache( $environment );

        $this->info( $environment );

        if( $this->isProduction( $environment )){

            $restoreDev = true;

            $this->confirmMasterBranch();

            // relocated this to run BEFORE composer installNoDev
            // uninstalling composer dev deps removes this... so DUH
            // the NPM Class is no longer available.
            // Question is whill anything work after composer installNoDev.
            // This package may NEED to be Production..
            // But does not need to be deployed... how do i manaage that??
            $this->comment( "Running NPM Production Script" );
            NPM::runProduction();

            $this->comment( "Removing Composer Dev Dependencies" );
//            Composer::installNoDev();

        } else {

            // Label non production builds with the current Environment
//            $version = $version ."_" . $environment;

            $this->comment( "Running NPM Dev Script" );
            NPM::runDev();

        } // END if production

        // Create Build .zip Package
        $this->createBuildFile( $version );

        // delay a few seconds to ensure composer completion
        sleep( 10 );

        if( $restoreDev === true ){

            // Composer install goes here
//            Composer::install();

        }

        // copy specified environment to .env
        $devEnv = $this->projectPath . "/environments/.env.dev";

        if( file_exists( $devEnv )){
            copy( $devEnv, $this->projectPath . "/.env" );
        }  //- END file_exists()

        $this->info( "Build Completed Successfully" );

    } //- END function build()


    /**
     *
     * Create the build deployment pacakge.
     *
     * @param $version string version to use when creating build file.
     */
    protected function createBuildFile( $version ){

        $this->comment( "Creating Build File" );


        $include_list = Config::get( 'build-tool.include' );
        $config_file = Config::has( 'build-tool' );
        $build_config = Config::get( 'build-tool' );
//
//        dump( $config_file );
//        dump( $build_config );
//
//        dump( Config::all() );
//
//        dd( "stoped" );


        $build_file = $this->projectPath . '_v-' . $version .'.zip';

///$this->projectPath . '_v-' . $version .'.zip
///
///

        $include ='';
        if( count( $include_list ) > 0 ) {
            $include = '-i ' . implode( $include_list, ' ' );
        }

        Zip::buildFile( $build_file, $include_list );


    } //- END function createBuildFile()


    /**
     * call laravel cache:clear artisan command
     *
     * @param $environment laravel environment to use
     *
     */
    protected function clearCache( $environment ){

        // TEST this... it may be a  way to push
        // cached configs to server.
//        $this->call( "config:cache", [
//        '--env' => $environment
//        ] );
        $this->comment( "Clearing App Cache" );

        $this->call( "cache:clear", [
            '--env' => $environment
        ]);

    } //- END function clearCache()

    /**
     * Check if git branch is "master"
     * and get confirmation for any other branch.
     *
     */
    protected function confirmMasterBranch(){

        if( $this->isNotBranch( 'master' )){

            $this->error( "Creating a Production Deployment from a Branch other than Master" );

            if( !$this->confirm( "Are you sure this is what you would like to do?" )){
                $this->terminateCommand();
            }

        } //- END if isNotBranch 'master'

    } //- END function confirmMasterBranch()

    /**
     * terminateCommand
     * displays message and exit script
     *
     * @param string $message
     */
    protected function terminateCommand( $message = '' ) {

        if( $message != '' ){
            $this->error( $message );
        }
        $this->error( "Build Process Terminated!" );
        exit();
    }

    protected function testConfig(){
        $include_list = Config::get( 'build-tool.include' );
        $config_file = Config::has( 'build-tool' );
        $build_config = Config::get( 'build-tool' );

        dump( $config_file );
        dump( $build_config );

        dump( Config::all() );


        throw new CommandError($this, 'stopped' );
    }

} //- END class BuildCommand{}
