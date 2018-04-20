<?php

//namespace App\Console\Commands;
namespace SouthernIns\BuildTool\Commands;

use Illuminate\Console\Command;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Class BuildProdCommand
 *
 * Shortcut for php artisan build --env production
 *
 */
class BuildStagingCommand extends Command {

    use ManageEnvironment;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'build:staging';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build Staging Deployment';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

//        $this->info( 'Setting Environment to - ' . $environment );

        $environment = 'staging';

        $this->info( 'Setting Environment to - ' . $environment );

        $this->setEnvironmentFile( $environment );

        $this->overrideEBConfig( $environment );

        // Call Build now that Env is set
//        $this->call( "build" );

        $buildCommand = 'php artisan build' ;

        $buildProcess = new Process( $buildCommand  );
        $buildProcess->setTimeout( 0 );
        $buildProcess->run();

        if( !$buildProcess->isSuccessful() ){

            throw new ProcessFailedException( $buildProcess );

        }

        echo $buildProcess->getOutput();

        // Restore Environment
        $this->restoreEBConfig();
//        $this->restoreEnvironmentFile();
        $this->restoreEnvironmentFile();

    } //- END function handle()

} //- END class BuildProdCommand {}
