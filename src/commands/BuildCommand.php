<?php

//namespace App\Console\Commands;
namespace SouthernIns\BuildTool\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

use SouthernIns\BuildTool\Shell\Composer;
use SouthernIns\BuildTool\Shell\Git;
use SouthernIns\BuildTool\Shell\NPM;


class BuildCommand extends Command {

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
            Composer::installNoDev();

        } else {

            // Label non production builds with the current Environment
//            $version = $version ."_" . $environment;

            $this->comment( "Running NPM Dev Script" );
            NPM::runDev();

        } // END if production

        // Create Build .zip Package
        $this->createBuildFile( $version );

//        $this->comment( "Build Zip File Created - Now Sleeping" );

        // delay a few seconds to ensure composer completion
        sleep( 10 );

//        $this->comment( " Slept, Now restoring if neccessary" );

        if( $restoreDev === true ){

            // Composer install goes here
            Composer::install();
//            $this->comment( "Dev deeps Restored" );

        }

//        $this->comment( "REstoring env file" );
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

        // find and check build-include.list exists
        if( file_exists($this->projectFolder . "/build-include.list" ) ){
            $include_file = $this->projectFolder . "/build-include.list";
            $this->info( "Using Build Include list overridden by project." );

        } else {
            $include_file = __DIR__ . "/../build-include.list";
            $this->info( "Using Lib build-include.list." );
            $this->info( "to override the list place a build-include.list in the project directory." );
        }

        $createBuild = new Process( 'zip -r -q ' . $this->projectPath . '_v-' . $version .'.zip ./ -i' . $include_file );
        $createBuild->run();

        if( !$createBuild->isSuccessful() ){

            if( $createBuild->getExitCode() == 127 ){
                $this->termiateCommand( "Zip Command failed, please confirm it is installed ( sudo apt-get install zip )" );
            }

            throw new ProcessFailedException( $createBuild );

        }

        echo $createBuild->getOutput();

    } //- END function createBuildFile()


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

    /**
     * call laravel cache:clear artisan command
     *
     * @param $environment laravel envrionment to use
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
     * Copy selected env file from environments folder to root project env
     * prior to build
     *
     * @param $environment laraven environment file to use
     */
    protected function setEnvironmentFile( $environment ){

        $newEnv = $this->projectPath . "/environments/.env." . $environment;

        // copy specified environment to .env
        if( file_exists( $newEnv )){
            copy( $newEnv, $this->projectPath . "/.env" );
        }  //- END file_exists()

        // Removed php artisan optimize from Composer.json
        // Running this in composer ignored the current environment
        // set by --env
        // optimize is being depreciated in 5.5 and removed in 5.6...
        // TODO:: remove when laravel upgraded to 5.5
        $this->call( "optimize",[
            '--env' => $environment
        ] );

    } //- END function setEnvironmentFile()

    /**
     * returns true if current envrionment is set to "production"
     *
     * @param $environment laravel envrionment to use during build
     *
     * @return bool
     */
    protected function isProduction( $environment ){

        return ( $environment == "production" );

    }

    /**
     * Check if git branch is "master"
     * and get confirmation for any other branch.
     *
     */
    protected function confirmMasterBranch(){

        if( Git::branchName() != "master" ){

            $this->error( "Creating a Production Deployment from a Branch other than Master" );
            if( !$this->confirm( "Are you sure this is what you would like to do?" )){
//                $this->error( "Build Process Terminated!" );
//                exit();
//                return;

                $this->termiateCommand();
            }
        }

    } //- END function confirmMasterBranch()

    protected function termiateCommand( $message = '' ) {
        if( $message != '' ){
            $this->error( $message );
        }
        $this->error( "Build Process Terminated!" );
        exit();
    }

} //- END class BuildCommand{}
