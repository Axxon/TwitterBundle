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
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class BITTwitterExtension extends Extension
{
  protected $resources = array( 'google' => 'google.xml', 'security' => 'security.xml' );
  
  public function load( array $configs, ContainerBuilder $container )
  {
    $processor = new Processor( );
    $configuration = new Configuration( );
    $config = $processor->processConfiguration( $configuration, $configs );
    
    $this->loadDefaults( $container );
    
    if ( isset( $config[ 'alias' ] ) )
      $container->setAlias( $config[ 'alias' ], 'bit_twitter.api' );
    
    foreach ( array( 'api', 'helper', 'twig' ) as $attribute )
      $container->setParameter( 'bit_twitter.' . $attribute . '.class', $config[ 'class' ][ $attribute ] );
    
    foreach ( array( 'app_name', 'client_id', 'client_secret', 'state', 'access_type', 'approval_prompt', 'scopes' ) as $attribute )
      $container->setParameter( 'bit_twitter.' . $attribute, $config[ $attribute ] );
    
    /* if ( array_key_exists( 'callback_route', $config ) )
      $container->setParameter( 'fos_twitter.' . $attribute, $config['callback_route'] );
    else */
    $container->setParameter( 'bit_twitter.callback_url', $config[ 'callback_url' ] );
  }
  
  protected function loadDefaults( $container )
  {
    $loader = new XmlFileLoader( $container, new FileLocator( __DIR__ . '/../Resources/config'));
    
    foreach ( $this->resources as $resource )
    {
      $loader->load( $resource );
    }
  }
}
