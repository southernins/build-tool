<?php


namespace SouthernIns\BuildTool\BuildRules;


use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use SouthernIns\BuildTool\Helpers\NPM;

class NpmInstalledRule implements Rule
{


    public function passes( $attribute, $value)
    {

//        return File::exists(  base_path() . "/node_modules" );
        return NPM::checkInstall();

    }


    public function message()
    {
        return Lang::get( 'build-tool::validation.npm_check' );
    }


}