<?php

namespace Incompass\AirbrakeBundle\Tests\DependencyInjection;

use Incompass\AirbrakeBundle\DependencyInjection\Configuration;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Config\Definition\ArrayNode;
use Symfony\Component\Config\Definition\NodeInterface;
use Symfony\Component\Config\Definition\ScalarNode;

/**
 * Class ConfigurationTest
 *
 * @package Incompass\AirbrakeBundle\Tests\DependencyInjection
 * @author  Joe Mizzi <joe@casechek.com>
 */
class ConfigurationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_has_a_configuration_tree()
    {
        $configuration = new Configuration();
        $treeBuilder = $configuration->getConfigTreeBuilder();
        /** @var ArrayNode $tree */
        $tree = $treeBuilder->buildTree();
        self::assertEquals('airbrake', $tree->getName());
        /** @var NodeInterface[] $children */
        $children = $tree->getChildren();
        self::assertInstanceOf(ScalarNode::class, $children['project_id']);
        self::assertInstanceOf(ScalarNode::class, $children['project_key']);
        self::assertInstanceOf(ScalarNode::class, $children['host']);
        self::assertEquals('api.airbrake.io', $children['host']->getDefaultValue());
        self::assertInstanceOf(ArrayNode::class, $children['ignored_exceptions']);
        $reflectionClass = new \ReflectionClass($children['ignored_exceptions']);
        $reflectionProperty = $reflectionClass->getProperty('prototype');
        $reflectionProperty->setAccessible(true);
        self::assertInstanceOf(ScalarNode::class, $reflectionProperty->getValue($children['ignored_exceptions']));
    }
}
