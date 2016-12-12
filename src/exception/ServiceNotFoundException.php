<?php
/**
 * Created by PhpStorm.
 * User: yimeng
 * Date: 2016/12/12
 * Time: 下午4:37
 */

namespace MyDI\exception\Exception;

use Interop\Container\Exception\NotFoundException as InteropNotFoundException;

class ServiceNotFoundException extends \Exception implements InteropNotFoundException
{

}