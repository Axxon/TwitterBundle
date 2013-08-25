<?php

/*
 * This file is part of the BITTwitterBundle package.
 *
 * (c) bitgandtter <http://bitgandtter.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace BIT\TwitterBundle\DependencyInjection;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
  /**
   * Generates the configuration tree.
   *
   * @return TreeBuilder
   */
  
  public function getConfigTreeBuilder( )
  {
    $treeBuilder = new TreeBuilder( );
    $rootNode = $treeBuilder->root( 'bit_twitter' );
    
    $rootNode->children( )// childrens
        ->scalarNode( 'consumer_key' )->isRequired( )->cannotBeEmpty( )->end( ) // consumer key
        ->scalarNode( 'consumer_secret' )->isRequired( )->cannotBeEmpty( )->end( ) // consumer secret
        ->scalarNode( 'access_token' )->defaultNull( )->end( ) // access token
        ->scalarNode( 'access_token_secret' )->defaultNull( )->end( ) // access token secret
        ->scalarNode( 'callback_url' )->defaultNull( )->end( ) // calback url
        ->scalarNode( 'callback_route' )->defaultNull( )->end( ) // callback route
        ->arrayNode( 'class' )->addDefaultsIfNotSet( )->children( ) // clasess
        ->scalarNode( 'api' )->defaultValue( 'BIT\TwitterBundle\Twitter\TwitterSessionPersistence' )->end( ) // api
        ->scalarNode( 'helper' )->defaultValue( 'BIT\TwitterBundle\Templating\Helper\TwitterHelper' )->end( ) // template helper
        ->scalarNode( 'twig' )->defaultValue( 'BIT\TwitterBundle\Twig\Extension\TwitterExtension' )->end( ) // twig ext
        ->end( ) // end clasess
        ->end( )->end( );
    
    return $treeBuilder;
  }
}
