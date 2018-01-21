<?php

namespace Keboola\Processor\EscapeCharacter;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ConfigDefinition implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root("parameters");

        $rootNode
            ->children()
                ->scalarNode("escaped_by")
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
