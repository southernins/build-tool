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

    protected $restoreEnv = false;


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

        $this->checkConfig();

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

        $buildVersion   = $this->buildVersion( $environment );
        $buildName      = $this->buildName();

        $this->info( "Creating $buildName - $environment Build version $buildVersion" );

        $this->clearCache( $environment );

        $this->info( 'Setting Environment to - ' . $environment );
        $this->setEnvironmentFile( $environment );

        if( $this->isProduction( $environment )){

            $this->restoreEnv = true;

            // get Confrimation if user is deploying production
            // to a Git Branch other than 'master'
            $this->confirmMasterBranch();

            $this->comment( "Running NPM Production Script" );
            NPM::runProduction();

            $this->comment( "Removing Composer Dev Dependencies" );
            Composer::installNoDev();

        } else {

            $this->comment( "Running NPM Dev Script" );
            NPM::runDev();

        } // END if production

        // Create Build .zip Package
        $this->createBuildFile( $buildName, $buildVersion );

        // short delay to ensure composer completion
        sleep( 2 );

        // restore Dev Dependencies if they were removed
        if( $this->restoreEnv === true ){
            Composer::install();
        }

        $this->restoreEnvironmentFile( );

        $this->info( "Build Completed Successfully" );

    } //- END function build()


    /**
     *
     * Create the build deployment pacakge.
     *
     * @param $version string version to use when creating build file.
     */
    protected function createBuildFile( $build, $version ){

        $this->comment( "Creating Build File" );

        $include_list = Config::get( 'build-tool.include' );

        $include ='';
        if( count( $include_list ) > 0 ) {
            $include = '-i ' . implode( $include_list, ' ' );
        }

        $build_file = $build . '_v-' . $version .'.zip';

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



} //- END class BuildCommand{}
