<?php

namespace MyDI\exception\Exception;

use Interop\Container\Exception\ContainerException as InteropContainerException;

/**
 * Created by PhpStorm.
 * User: yimeng
 * Date: 2016/12/12
 * Time: 下午4:33
 */
class ContainerException extends \Exception implements InteropContainerException
{

}