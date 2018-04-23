<?php
/**
 *
 */

namespace SouthernIns\BuildTool;

use Illuminate\Support\ServiceProvider;
use SouthernIns\BuildTool\Commands\BuildCommand;


class BuildServiceProvider extends ServiceProvider {

    public function boot(){
        // Boot runs after ALL providers are registered

    } //- END function boot()

    public function register(){

        if( $this->app->runningInConsole() ){
            $this->commands([
                BuildCommand::class,
            ]);
        }

    } //- END function register()

} // - END class BuildServiceProvider{}
