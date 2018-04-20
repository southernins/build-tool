<?php

//namespace App\Console\Commands;
namespace SouthernIns\BuildTool\Commands;

use Illuminate\Console\Command;

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
        $this->call( "build" );

        // Restore Environment
        $this->restoreEBConfig();
        $this->restoreEnvironmentFile();

        // Testing Move to Sub Commands  Build now creates a pacakge from the current env
        $this->restoreEnvironmentFile();
        
    } //- END function handle()

} //- END class BuildProdCommand {}
