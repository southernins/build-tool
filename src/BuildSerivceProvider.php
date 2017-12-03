<?php

namespace SouthernIns\BuildTool;

use Illuminate\Support\ServiceProvider;
use SouthernIns\BuildTool\Commands\BuildCommand;
use SouthernIns\BuildTool\Commands\BuildDevCommand;
use SouthernIns\BuildTool\Commands\BuildProdCommand;


class BuildServiceProvider extends ServiceProvider {

    public function boot(){

        if( $this->app->runningInConsole() ){
            $this->commands([
                BuildCommand::class
            ]);
        }

    } //- END function boot()

    public function register(){
        if( $this->app->runningInConsole() ){
            $this->commands([
                BuildDevCommand::class
            ]);
        }

    } //- END function register()

} // - END class BuildServiceProvider{}
