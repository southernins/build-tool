<?php
/**
 *
 */

namespace SouthernIns\BuildTool\Commands;

use Exception;
use Illuminate\Console\Command;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;

use Illuminate\Support\Facades\Lang;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

use SouthernIns\BuildTool\BuildTool;
use SouthernIns\BuildTool\Helpers\Composer;

use SouthernIns\BuildTool\Helpers\NPM;

use SouthernIns\BuildTool\Traits\BuildDeployment;
use SouthernIns\BuildTool\Traits\ManageEnvironment;


class BuildCommand extends Command{

    use BuildDeployment;
    use ManageEnvironment;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'build {--force} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Build File for Deployment Use --env to specify environment for Build package';



    /** @var BuildTool
     *  BuildTool Class
     */
    protected $buildTool;


    protected $projectPath = '';

    protected $restoreComposer = FALSE;

    protected $builder;


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct( BuildTool $buildTool){

        parent::__construct();

        $this->buildTool = $buildTool;


//        // Set folderName and Path for project
//        $this->projectPath = base_path();
//
//        $dirArr = explode( "/", $this->projectPath );

        $this->checkConfig();

    } //- END __construct()


    /**
     * Execute the console command.
     *
     */
    public function handle(){


//        try {

//            $this->buildTool->setBuildEnvironment( App::environment() );

            $this->buildTool->createBuildPackage( $this->output, $this->option( 'force' ) );

            $this->info( Lang::get( 'build-tool::messages.build-successful') );


//        }catch( Exception $e ){
//
//            $this->buildTool->after
//
//            $this->terminateCommand( $e->getMessage() );
//
//        }

        return 0; // assert 0 with success and error

    } // END function handle()


    /**
     * run the entire build process
     *
     * @param $environment string laravel environment to use
     */
    protected function build( $environment ){

        $buildVersion = $this->buildVersion( $environment );

        // Get list of files to include in build package
        // HAS to be done before cache is cleared.
        $buildFileList = Config::get( 'build-tool.include' );

        $this->clearCache( $environment );

        $this->comment( 'Building environment: ' . $environment );

        $this->setEnvironmentFile( $environment );

        $this->composerCheck();
        $this->npmCheck();

        try{

            $this->overrideEBConfig( $environment );

            // Generate Build name
            $buildName = $this->buildName();

            $this->info( "Creating $buildName - $environment Build version $buildVersion" );

            if( $this->isProduction( $environment ) ){

                $this->restoreComposer = TRUE;

                // get Confirmation if user is deploying production
                // to a Git Branch other than 'master'
                $this->confirmMasterBranch();

                $this->comment( "Running NPM Production Script" );
                NPM::runProduction();

                // required-dev dependencies removed here classes
                $this->comment( "Removing Composer Dev Dependencies" );
                Composer::installNoDev();

            }else{

                $this->comment( "Running NPM Dev Script" );
                NPM::runDev();

            } // END if production

            // Create Build .zip Package
            // shell class failed here because we uninstall this package in production
            // creating teh class above and executing the command here could work.77
            $this->createBuildFile( $buildName, $buildVersion, $buildFileList );

            // short delay to make sure everything is done.
            sleep( 2 );

            // restore Dev Dependencies if they were removed
            if( $this->restoreComposer === TRUE ){
                Composer::install();
            }

            // Restore Environment
            $this->restoreEBConfig();
            $this->restoreEnvironmentFile();

            //            $this->call( "config:clear" );

            $this->info( Lang::get( 'build-tool::messages.build-successful') );

        }catch( \Exception $exception ){

            //            $this->call( "config:clear" );
            $this->restoreEnvironmentFile();
            $this->terminateCommand( $exception->getMessage() );

        }

    } //- END function build()


    /**
     * Create the build deployment package.
     *
     * @param $build   Build Name
     * @param $version string version to use when creating build file.
     */
    protected function createBuildFile( $build, $version, $include_list ){

        $this->comment( "Creating Build File" );

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
        $this->comment( "Clearing Local Caches" );

        // Flush Application Cache for local environment
        // before creating $environment build
        //        $this->call( "cache:clear", [
        //            '--env' => "local"
        //        ]);
        //        $localCachePath = storage_path('framework/cache/data');
        //        $fileClass = new Filesystem();
        //        $localCache = $this->repository(new FileStore( $fileClass, $localCachePath ));
        //        $localCache->flush();

        Cache::store( 'file' )->flush();

        // TODO:: Clear All of Storage from local environment before deploying

        // Remove Cached Views
        $this->call( "view:clear" );

        // Remove Config Cache File
        $this->call( "config:clear" );

        // Calling Build with --env="" will overwrite the
        // cache with the config of the environment being deployed..
        // CANNOT CACHE Config in local env before deployment
        //        $this->call( "config:cache" );

        $this->info( "Route Caching - Disabled" );
        //      // Remove Route Cache file
        // Route Caching fails due to Closures in Routes
        // PHP Cannot serialize routes with closures.
        //      $this->call( "route:clear", [
        //          '--env' => $environment
        //      ]);

    } //- END function clearCache()


//    /**
//     * Check if git branch is "master"
//     * and get confirmation for any other branch.
//     *
//     */
//    protected function confirmMasterBranch(){
//
//        $confirmed = false;
//
//        try{
//
//            $confirmed = $this->isBranch( 'master' );
//
//        } catch( ProcessFailedException $exception){ // Catches error in underlying Git call
//
//            $this->error( "Git command failed while creating production Deployment." );
//            $this->error( "Master branch could not be confirmed. Practice Caution!" );
//
//        }
//
//        if( $confirmed === false ){
//
//                $this->error( "Detected a Production Deployment from a Branch other than Master" );
//
//            if( !$this->confirm( Lang::get( 'build-tool::messages.confirmation') ) ){
//                $this->terminateCommand();
//            }
//
//        } // Error conformation
//
//        return $confirmed;
//
//    } //- END function confirmMasterBranch()


    /**
     * terminateCommand
     * displays message and exit script
     *
     * @param string $message
     */
    protected function terminateCommand( $message = '' ){

//        dump( $message );
        if( $message != '' ){
            $this->error( $message );
        }

        $this->error( Lang::get( 'build-tool::messages.terminated' ) );

//        exit(1);
    }


//    protected function composerCheck(){
//
//        if( Composer::checkInstall() === FALSE ){
//            $this->terminateCommand(  Lang::get( 'build-tool::messages.error.composer_check' ));
//        }
//    }
//
//
//    protected function npmCheck(){
//
//        if( NPM::checkInstall() === FALSE ){
//            $this->terminateCommand(  Lang::get( 'build-tool::messages.error.npm_check' ));
//        }
//
//    }


} //- END class BuildCommand{}
