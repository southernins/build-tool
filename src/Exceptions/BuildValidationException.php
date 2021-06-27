<?php
/**
 * Created by PhpStorm.
 * User: nakie
 * Date: 5/26/21
 * Time: 11:06 PM
 */

namespace SouthernIns\BuildTool\Exceptions;


use Throwable;

class BuildValidationException extends \Exception
{


    public function __construct( $message )
    {
        parent::__construct( $message );
    }

}