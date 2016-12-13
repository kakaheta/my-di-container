<?php
use MyDI\Container\Container;
use MyDI\Container\Reference\ParameterReference;
use MyDI\Container\Reference\ServiceReference;

/**
 * Created by PhpStorm.
 * User: yimeng
 * Date: 2016/12/13
 * Time: 上午11:42
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testParameters() {
        $parameters = [
            'hello' => 'world',
            'first' => [
                'second' => 'foo',
                'third' => [
                    'fourth' => 'bar'
                ],
            ],
        ];

        $container = new Container([], $parameters);

        // Basic test
        $this->assertEquals('world', $container->getParameter('hello'));

        // Layered test
        $this->assertEquals('foo', $container->getParameter('first.second'));
        $this->assertEquals('bar', $container->getParameter('first.second.fourth'));
    }

    public function testContainer()
    {
        // Service definitions
        $services = [
            'service' => [
                'class' => MockService::class,
                'arguments' => array(
                    new ServiceReference('dependency'),
                    'foo',
                ),
                'calls' => [
                    [
                        'method' => 'setProperty',
                        'arguments' => [
                            new ParameterReference('group.param')
                        ]
                    ]
                ]
            ],
            'dependency' => [
                'class' => MockDependency::class,
                'arguments' => [
                    new ParameterReference('group.param')
                ],
            ],
        ];

        // Parameter definitions
        $parameters = [
            'group' => [
                'param' => 'bar',
            ],
        ];

        // Create container
        $container = new Container($services, $parameters);

        // Check retrieval of service
        $service = $container->get('service');
        $this->assertInstanceOf(MockService::class);

        // Check retrieval of dependency
        $dependency = $container->get('dependency');
        $this->assertSame($dependency, $service->getDenpendency());

        // Check that the dependency has been reused
        $this->assertSame($dependency, $service->getDenpendency());

        // Check the retrieval of container parameters
//        $this->assertTrue($container->hasParameter('gropu.param'));
//        $this->assertFalse($container->hasParameter('foo.bar'));


    }
}
