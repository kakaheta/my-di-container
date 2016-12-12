<?php
/**
 * Created by PhpStorm.
 * User: yimeng
 * Date: 2016/12/12
 * Time: 下午5:20
 */

namespace MyDI\Container\Reference;


class ServiceReference extends AbstractReference
{
    private $name;


    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

}