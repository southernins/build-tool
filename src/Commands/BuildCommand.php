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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

use SouthernIns\BuildTool\Shell\Composer;

use SouthernIns\BuildTool\Shell\NPM;


//use SouthernIns\BuildTool\Shell\Zip;
//
//use \Illuminate\Cache\FileStore;
//
//use \Illuminate\Filesystem\Filesystem;


class BuildCommand extends Command{

    use BuildDeployment;
    use ManageEnvironment;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'build {--path=} {--ignore-branch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Build File for Deployment Use --env to specify environment for Build package';


    /**
     * Environment to deploy
     *
     * @var string
     */
    protected $environment = 'local';

    /**
     *
     * @var string
     */
    protected $projectPath = '';

    protected $projectFolder = '';

    protected $restoreComposer = FALSE;


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(){

        parent::__construct();

        // Set build environment from app envrionment
        // Set with --env on artisan command
        $this->environment = App::environment();

        // Set folderName and Path for project
        $this->projectPath = base_path();

        $dirArr = explode( "/", $this->projectPath );
        $this->projectFolder = end(  $dirArr );

        $this->checkConfig();

    } //- END __construct()


    /**
     * Execute the console command.
     *
     */
    public function handle(): int
    {

        $this->info( $this->environment );

        $this->build( $this->environment );

        return 0;

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
    protected function build( $environment ): int
    {

        $buildVersion = $this->buildVersion( $environment );

        // Get list of files to include in build package
        // HAS to be done before cache is cleared.
        $buildFileList = Config::get( 'build-tool.include' );

        $this->clearCache( $environment );

        $this->info( 'Setting Environment to - ' . $environment );

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

                $this->comment( "Removing Composer Dev Dependencies" );
                Composer::installNoDev();

            }else{

                $this->comment( "Running NPM Dev Script" );
                NPM::runDev();

            } // END if production

            // Create Build .zip Package
            $this->createBuildFile( $buildName, $buildVersion, $buildFileList );

            // short delay to make sure everything is done.
            sleep( 2 );

            //            $this->call( "config:clear" );

            $this->info( "Build Completed Successfully" );

        }catch( \Exception $exception ){

            //            $this->call( "config:clear" );
//            $this->restoreEnvironmentFile();
            $this->terminateCommand( $exception->getMessage() );

        }finally{

            // restore Dev Dependencies if they were removed
            if( $this->restoreComposer === TRUE ){
                Composer::install();
            }

            // Restore Environment
            $this->restoreEBConfig();
            $this->restoreEnvironmentFile();
        }

        return 0;

    } //- END function build()


    /**
     * Create the build deployment package.
     *
     * @param $build   Build Name
     * @param $version string version to use when creating build file.
     */
    protected function createBuildFile( $build, $version, $include_list ){

        $this->comment( "Creating Build File" );

        $buildPath = $this->option( 'path' ) ?? Config::get( 'build-tool.destination' );

        if( !is_writable( $buildPath )){
            throw new \Exception( 'Error Writing to build path.  Check path and permissions' );
        }

        $include = '';
        if( count( $include_list ) > 0 ){
            $include = '-i ' . implode( ' ', $include_list );
        }

        $build_file = $buildPath . $build . '_v-' . $version . '.zip';

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


    /**
     * Check if git branch is "master"
     * and get confirmation for any other branch.
     *
     */
    protected function confirmMasterBranch(){

        $confirmed = false;
        $failed = false;


        if( $this->option( 'ignore-branch' ) == true ){
            return true;
        }

        try{

            $confirmed = $this->isBranch( 'master' );

        } catch( ProcessFailedException $exception){ // Catches error in underlying Git call

            $failed = true;

        }

        if( $confirmed === false || $failed === true ){

            if( $failed === true ){

                // todo: relocate into catch statement above.
                $this->error( "Git command failed while creating production Deployment." );
                $this->error( "Master branch could not be confirmed. Practice Caution!" );
            }else{
                $this->error( "Creating a Production Deployment from a Branch other than Master" );
            }

            // see the confrimable trait.  i may be able to
            // https://github.com/laravel/framework/blob/8.x/src/Illuminate/Console/ConfirmableTrait.php
            if( !$this->confirm( "Are you sure this is what you would like to do?" ) ){
                $this->terminateCommand();
                exit();
            }

        } // Error conformation

        return $confirmed;

    } //- END function confirmMasterBranch()


    /**
     * terminateCommand
     * displays message and exit script
     *
     * @param string $message
     */
    protected function terminateCommand( $message = '' ){

        if( $message != '' ){
            $this->error( $message );
        }
        $this->error( "Build Process Terminated!" );
//        exit();
//        return 1;
    }


    protected function composerCheck(){

        if( Composer::checkInstall() === FALSE ){
            $this->terminateCommand( "vendor Folder not found. Run composer install" );
            exit();
        }
    }


    protected function npmCheck(){

        if( NPM::checkInstall() === FALSE ){
            $this->terminateCommand( "node_modules Folder not found. Run npm install" );
            exit();
        }

    }


} //- END class BuildCommand{}
