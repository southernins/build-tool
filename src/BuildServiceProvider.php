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

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'build-tool');

        $this->publishes([
                             __DIR__ . '/../config/build-tool.php' => config_path('build-tool.php'),
                         ]);

    } //- END function boot()


    public function register(){

        if( $this->app->runningInConsole() ){
            $this->commands([
                BuildCommand::class,
            ]);
        }

        $this->mergeConfigFrom(
            __DIR__ . '/../config/build-tool.php', 'build-tool'
        );

//        $this->app->bind(
//            'SouthernIns\BuildTool\Contracts\PackageBuilder',
//            'SouthernIns\BuildTool\AwsPackageBuilder'
//        );

    } //- END function register()


} // - END class BuildServiceProvider{}
