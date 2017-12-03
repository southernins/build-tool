<?php

//namespace App\Console\Commands;
namespace SouthernIns\BuildTool\Commands;

use Illuminate\Console\Command;

/**
 * Class BuildDevCommand
 *
 * Shortcut for php artisan build --env dev
 */

class BuildDevCommand extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'build:dev';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build Dev Deployment for Testing/Staging';

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
        //

        $this->info( App::environment() );
//        $this->call( "build", [
//            '--env' => 'dev'
//        ]);

    } //- END function handle()

} //- END class BuildDevCommand {}
