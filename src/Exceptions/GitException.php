<?php
/**
 * Created by PhpStorm.
 * User: Nathan
 * Date: 2/23/2018
 * Time: 11:17 AM
 */

namespace SouthernIns\BuildTool\Exceptions;


class GitException extends \RuntimeException {

    public function __construct( $message = '' ){

        parent::__construct( 'Git Failed: ' . $message );

    }

}