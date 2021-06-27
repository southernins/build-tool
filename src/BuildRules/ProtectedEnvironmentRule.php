<?php


namespace SouthernIns\BuildTool\BuildRules;


use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;

use Facades\SouthernIns\BuildTool\Helpers\Git;

class ProtectedEnvironmentRule implements Rule
{

    public function __construct(  )
    {

    }

    public function passes( $attribute, $value)
    {

        $protected_branches = Config::get('build-tool.protected' );

        if( !array_key_exists( $value, $protected_branches )) {
            return true;
        }

        return Git::isBranch( $protected_branches[ $value ] );

    } //- END passes()


    public function message()
    {

        return Lang::get( 'build-tool::validation.protected_branch' );

    } //- END messages()

}