<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        $projectPath = base_path();

        $dirArr = explode("/", $projectPath);

        $projectFolder = end($dirArr);


        $version = Carbon::now()->format('Y.m.d.Hi');

        $environment = App::environment();


        $this->info( "Creating $projectFolder - $environment Build verion $version" );

        $this->comment( "Clearing App Cache" );

        $this->call("cache:clear", [
            '--env' => $environment
        ]);

        // TEST this... it may be a  way to push
        // cached configs to server.
//        $this->call( "config:cache", [
//        '--env' => $environment
//        ] );

        $newEnv = $projectPath . "/environments/.env." . $environment;

        // copy specified environment to .env
        if( file_exists( $newEnv )){
            copy( $newEnv, $projectPath . "/.env" );
        }  //- END file_exists()

        // Removed php artisan optimize from Composer.json
        // Running this in composer ignored the current environment
        // set by --env
        // optimize is being depreciated in 5.5 and removed in 5.6...
        // TODO:: remove when laravel upgraded to 5.5
        $this->call( "optimize",[
            '--env' => $environment
        ] );

        // INSTALL git in Guest OS
        // sudo apt-get install git
        // Get current Git Branch name
        $gitBranch = new Process( "git rev-parse --abbrev-ref HEAD" );
        $gitBranch->run();

        if (!$gitBranch->isSuccessful()) {
            throw new ProcessFailedException($gitBranch);
        }

        // Set Current Git Branch Name from command output
        $curBranch = $gitBranch->getOutput();

        if( $environment == "production" ){

            $this->comment( "Removing Composer Dev Dependencies" );
            // Run composer install --no-dev to prevent Dev Deps from pushing t production
            $composer_prod = new Process('composer install --no-dev --optimize-autoloader --no-interaction');
            $composer_prod->start();
            $iterator = $composer_prod->getIterator($composer_prod::ITER_SKIP_ERR | $composer_prod::ITER_KEEP_OUTPUT);
            foreach ($iterator as $data) {
                echo $data."\n";
            }

            $restoreDev = true;

            if( $curBranch != "master" ){

                $this->error( "Creating a Production Deployment from a Branch other than Master" );
                if( !$this->confirm( "Are you sure this is what you would like to do?" )){
                    $this->error( "Build Process Terminated!" );
                    return;
                }
            }

            $this->comment( "Running NPM Production Script" );

            // Install Node
            // curl -sL https://deb.nodesource.com/setup_8.x | sudo -E bash -
            // sudo apt-get install -y nodejs
            $npmProduction = new Process( "npm run production" );
            $npmProduction->start();

            $iterator = $npmProduction->getIterator($npmProduction::ITER_SKIP_ERR | $npmProduction::ITER_KEEP_OUTPUT);
            foreach( $iterator as $data ){
                echo $data."\n";
            }

        } else {

            // Label non production builds with the current Environment
            $version = $version ."_" . $environment;

            $this->comment( "Running NPM Dev Script" );

            $npmProduction = new Process( "npm run dev" );
            $npmProduction->start();

            $iterator = $npmProduction->getIterator($npmProduction::ITER_SKIP_ERR | $npmProduction::ITER_KEEP_OUTPUT);
            foreach( $iterator as $data ){
                echo $data."\n";
            }

        } // END if production

        // delay a few seconds to ensure composer completion
        sleep( 10 );

        $this->comment( "Creating Build File" );

        $createBuild = new Process( 'zip -r -q ' . $projectPath . '_v-' . $version .'.zip ./ -i@build-include.list' );
        $createBuild->run();

        if( !$createBuild->isSuccessful() ){
            throw new ProcessFailedException( $createBuild );
        }

        echo $createBuild->getOutput();

        if( $restoreDev === true ){

            $this->comment( "Restoring Composer Dev Dependencies" );
            // Run composer install --no-dev to prevent Dev Deps from pushing t production
            $composerDev = new Process('composer install' );
            $composerDev->start();
            $iterator = $composerDev->getIterator($composerDev::ITER_SKIP_ERR | $composerDev::ITER_KEEP_OUTPUT) ;
            foreach ( $iterator as $data ) {
                echo $data."\n";
            }

        }

        // copy specified environment to .env
        $devEnv = $projectPath . "/environments/.env.dev";
        if( file_exists( $devEnv )){
            copy( $devEnv, $projectPath . "/.env" );
        }  //- END file_exists()

        $this->info( "Build Completed Successfully" );

    } // END function handle()

}
