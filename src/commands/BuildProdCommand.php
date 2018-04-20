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
class BuildProdCommand extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'build:prod';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build Production Deployment';

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

        $environment = 'production';

        $this->info( 'Setting Environment to - ' . $environment );

        $this->setEnvironmentFile( $environment );

        $this->overrideEBConfig( $environment );

        // Call Build now that Env is set
        $this->call( "build" );

        // Restore Environment
        $this->restoreEBConfig();
//        $this->restoreEnvironmentFile();
        $this->restoreEnvironmentFile();
        
    } //- END function handle()

} //- END class BuildProdCommand {}
