<?php

namespace SouthernIns\BuildTool;

use Illuminate\Support\ServiceProvider;

class BuildServiceProvider extends ServiceProvider {

    public function boot(){

        if( $this->app->runningInConsole() ){
            $this->commands([
                Commands\BuildCommand::class
            ]);
        }

    } //- END function boot()

    public function register(){
        if( $this->app->runningInConsole() ){
            $this->commands([
                Commands\BuildDevCommand::class
            ]);
        }

    } //- END function register()

} // - END class BuildServiceProvider{}
