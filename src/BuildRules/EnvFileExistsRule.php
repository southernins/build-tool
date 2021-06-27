<?php


namespace SouthernIns\BuildTool\BuildRules;


use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use SouthernIns\BuildTool\Shell\Git;

class EnvFileExistsRule implements Rule
{

    protected $message;

    public function __construct(  )
    {

    }

    public function passes( $attribute, $value)
    {

        return File::exists(  base_path() . "/.env." . $value );

    }


    public function message()
    {
        return Lang::get( 'build-tool::validation.env_file_not_found' );
    }

}