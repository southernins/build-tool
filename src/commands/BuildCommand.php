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

        $this->build( $this->environment );

    } // END function handle()


    protected function build( $environment ){

        $version = $this->buildVersion( $environment );

        $this->info( "Creating $this->projectFolder - $environment Build verion $version" );

        $this->clearCache( $environment );

        if( $this->isProduction( $environment )){

            $restoreDev = true;

            $this->confirmMasterBranch();

            Composer::installNoDev();

            $this->comment( "Running NPM Production Script" );

            NPM::runProduction();

        } else {

            // Label non production builds with the current Environment
//            $version = $version ."_" . $environment;

            $this->comment( "Running NPM Dev Script" );
            NPM::runDev();

        } // END if production

        // delay a few seconds to ensure composer completion
        sleep( 10 );

        if( $restoreDev === true ){

            // Composer install goes here
            Composer::install();

        }

        // copy specified environment to .env
        $devEnv = $this->projectPath . "/environments/.env.dev";
        if( file_exists( $devEnv )){
            copy( $devEnv, $this->projectPath . "/.env" );
        }  //- END file_exists()

        $this->info( "Build Completed Successfully" );

    } //- END function build()



    protected function createBuildFile( $version ){

        $this->comment( "Creating Build File" );

        $createBuild = new Process( 'zip -r -q ' . $this->projectPath . '_v-' . $version .'.zip ./ -i@build-include.list' );
        $createBuild->run();

        if( !$createBuild->isSuccessful() ){
            throw new ProcessFailedException( $createBuild );
        }

        echo $createBuild->getOutput();

    } //- END function createBuildFile()


    protected function buildVersion( $environment ){

        $version = Carbon::now()->format('Y.m.d.Hi');

        if( !$this->isProduction( $environment ) ){

            // Label non production builds with the current Environment
            $version = $version ."_" . $environment;

        } //- END if( is production )

        return $version;

    } //- END function buildVersion()

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
     * @param
     *
     * @return bool
     */
    protected function isProduction( $environment ){

        return ( $environment == "production" );

    }

    /**
     * Check if git branch is "master"
     * and get confrimation for any other branch.
     *
     */
    protected function confirmMasterBranch(){

        if( Git::branchName() != "master" ){

            $this->error( "Creating a Production Deployment from a Branch other than Master" );
            if( !$this->confirm( "Are you sure this is what you would like to do?" )){
                $this->error( "Build Process Terminated!" );
                return;
            }
        }

    } //- END fuction confirmMasterBranch()

} //- END class BuildCommand{}
