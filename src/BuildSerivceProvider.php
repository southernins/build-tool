<?php

namespace SouthernIns\BuildTool;

use Illuminate\Support\ServiceProvider;
use SouthernIns\BuildTool\Commands\BuildCommand;
use SouthernIns\BuildTool\Commands\BuildDevCommand;
use SouthernIns\BuildTool\Commands\BuildProdCommand;
use SouthernIns\BuildTool\Commands\BuildStagingCommand;


class BuildServiceProvider extends ServiceProvider {

    public function boot(){
        // Boot runs after ALL providers are registered

    } //- END function boot()

    public function register(){

        if( $this->app->runningInConsole() ){
            $this->commands([

                BuildDevCommand::class,
                BuildCommand::class,
                BuildProdCommand::class,
                BuildStagingCommand::class

            ]);
        }

    } //- END function register()

} // - END class BuildServiceProvider{}
