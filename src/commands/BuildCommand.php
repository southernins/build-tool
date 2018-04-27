<?php
/**
 *
 */

//namespace App\Console\Commands;
namespace SouthernIns\BuildTool\Commands;

use Carbon\Carbon;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

use SouthernIns\BuildTool\Shell\Composer;

use SouthernIns\BuildTool\Shell\NPM;
//use SouthernIns\BuildTool\Shell\Zip;


class BuildCommand extends Command {

    use BuildDeployment;
    use ManageEnvironment;

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
    protected $description = 'Create Build File for Deployment Use --env to specify environment for Build package';


    /**
     * Environment to deploy
     * @var string
     */
    protected $environment = 'local';

    /**
     *
     * @var string
     */
    protected $projectPath = '';

    protected $projectFolder = '';

    protected $restoreComposer = false;


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
     *
     *
     * @param $environment string laravel environment to use
     *
     */
    /**
     * run the entire build process
     *
     * @param $environment
     */
    protected function build( $environment ){

        $buildVersion = $this->buildVersion( $environment );

        $this->clearCache( $environment );

        $this->info( 'Setting Environment to - ' . $environment );

        $this->setEnvironmentFile( $environment );

        try{

            $this->overrideEBConfig( $environment );

            // Generate Build name
            $buildName = $this->buildName();
            $this->info( "Creating $buildName - $environment Build version $buildVersion" );

            if( $this->isProduction( $environment )){

                $this->restoreComposer = true;

                // get Confirmation if user is deploying production
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

            // short delay to make sure everything is done.
            sleep( 2 );

            // restore Dev Dependencies if they were removed
            if( $this->restoreComposer === true ){
                Composer::install();
            }

            // Restore Environment
            $this->restoreEBConfig();
            $this->restoreEnvironmentFile();

            $this->info( "Build Completed Successfully" );

        }catch( \Exception $exception ){

            $this->restoreEnvironmentFile();
            $this->terminateCommand( $exception->getMessage() );

        }

    } //- END function build()


    /**
     * Create the build deployment package.
     *
     * @param $build Build Name
     * @param $version string version to use when creating build file.
     */
    protected function createBuildFile( $build, $version ){

        $this->comment( "Creating Build File" );

        $include_list = Config::get( 'build-tool.include' );

        $include = '';
        if( count( $include_list ) > 0 ) {
            $include = '-i ' . implode( $include_list, ' ' );
        }

        $build_file =  base_path() . '/../' . $build . '_v-' . $version .'.zip';

        // Command ran manually here, a Zip class will not be found after Composer uninstall.
        $command = 'zip -r -q ' . $build_file . ' ./ ' . $include ;

        $createBuild = new Process( $command  );
        $createBuild->setTimeout( 0 );
        $createBuild->run();


        foreach( $createBuild as $type => $data ){
            if( $createBuild::OUT === $type ) {
                echo "\n-> ".$data;
            } else { // $process::ERR === $type
                echo "\n=> ".$data;
            }
        }

//        if( !$createBuild->isSuccessful() ){
//
////            $this->handleCommandError();
//            if( $createBuild->getExitCode() == 127 ){
////                $this->terminateCommand( "Zip Command failed, please confirm it is installed ( sudo apt-get install zip )" );
//            }
//
//            throw new ProcessFailedException( $createBuild );
//
//        }
//
//        echo $createBuild->getOutput();

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

        // Flush Application Cache
        $this->call( "cache:clear", [
            '--env' => $environment
        ]);

        // Remove Cached Views
        $this->call( "view:clear", [
            '--env' => $environment
        ]);

        // Remove Config Cache File
        $this->call( "config:clear", [
            '--env' => $environment
        ]);

//        // Remove Route Cache file
//        $this->call( "route:clear", [
//            '--env' => $environment
//        ]);

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
