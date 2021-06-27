<?php


namespace SouthernIns\BuildTool\BuildRules;


use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use SouthernIns\BuildTool\Helpers\Composer;

class ComposerInstalledRule implements Rule
{


    public function passes( $attribute, $value)
    {

//        return File::exists(  base_path() . "/vendor" );

        return Composer::checkInstall();

    }


    public function message()
    {
        return Lang::get( 'build-tool::validation.composer_check' );
    }


}