<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BuildDevCommand extends BuildCommand {
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
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        //
    }
}
