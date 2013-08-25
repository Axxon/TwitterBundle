<?php

/*
 * This file is part of the BITTwitterBundle package.
 *
 * (c) bitgandtter <http://bitgandtter.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace BIT\TwitterBundle\Twitter;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Session\Session;
use Codebird\Codebird;

/**
 * Implements Symfony2 session persistence for Twitter.
 *
 */
class TwitterSessionPersistence extends Codebird
{
  const PREFIX = '_bit_twitter_';
  
  private $session;
  private $prefix;
  private $config;
  
  public function __construct( Array $config, Session $session, $prefix = self::PREFIX )
  {
    Codebird::setConsumerKey( $config[ 'consumer_key' ], $config[ 'consumer_secret' ] );
    
    $this->config = $config;
    $this->session = $session;
    $this->prefix = $prefix;
    $this->session->start( );
  }
  
  public function getAuthorizeUrl( )
  {
    // get the request token
    $reply = $this->oauth_requestToken( array( 'oauth_callback' => $this->config[ 'callback_url' ] ) );
    
    try
    {
      // store the token
      $this->setToken( $reply->oauth_token, $reply->oauth_token_secret );
      $this->session->set( 'oauth_token', $reply->oauth_token );
      $this->session->set( 'oauth_token_secret', $reply->oauth_token_secret );
      $this->session->set( 'oauth_verify', true );
      
      return $this->oauth_authorize( );
    }
    catch ( \Exception $e )
    {
      return null;
    }
  }
  
  public function authenticate( )
  {
    // verify the token
    $this->setToken( $this->session->get( 'oauth_token' ), $this->session->get( 'oauth_token_secret' ) );
    $this->session->remove( 'oauth_verify' );
    
    // get the access token
    $reply = $this->oauth_accessToken( array( 'oauth_verifier' => $this->session->get( 'oauth_verifier' ) ) );
    
    // store the token (which is different from the request token!)
    $this->session->set( 'oauth_token', $reply->oauth_token );
    $this->session->set( 'oauth_token_secret', $reply->oauth_token_secret );
    
    $this->setToken( $reply->oauth_token, $reply->oauth_token_secret );
  }
  
  /**
   * Stores the given ($key, $value) pair, so that future calls to
   * getPersistentData($key) return $value. This call may be in another request.
   *
   * @param string $key
   * @param array $value
   *
   * @return void
   */
  
  protected function setPersistentData( $key, $value )
  {
    if ( !in_array( $key, self::$kSupportedKeys ) )
    {
      self::errorLog( 'Unsupported key passed to setPersistentData.' );
      return;
    }
    
    $this->session->set( $this->constructSessionVariableName( $key ), $value );
  }
  
  /**
   * Get the data for $key
   *
   * @param string $key The key of the data to retrieve
   * @param boolean $default The default value to return if $key is not found
   *
   * @return mixed
   */
  
  protected function getPersistentData( $key, $default = false )
  {
    if ( !in_array( $key, self::$kSupportedKeys ) )
    {
      self::errorLog( 'Unsupported key passed to getPersistentData.' );
      return $default;
    }
    
    $sessionVariableName = $this->constructSessionVariableName( $key );
    if ( $this->session->has( $sessionVariableName ) )
      return $this->session->get( $sessionVariableName );
    
    return $default;
    
  }
  
  /**
   * Clear the data with $key from the persistent storage
   *
   * @param string $key
   * @return void
   */
  
  protected function clearPersistentData( $key )
  {
    if ( !in_array( $key, self::$kSupportedKeys ) )
    {
      self::errorLog( 'Unsupported key passed to clearPersistentData.' );
      return;
    }
    
    $this->session->remove( $this->constructSessionVariableName( $key ) );
  }
  
  /**
   * Clear all data from the persistent storage
   *
   * @return void
   */
  
  protected function clearAllPersistentData( )
  {
    foreach ( $this->session->all( ) as $k => $v )
    {
      if ( 0 !== strpos( $k, $this->prefix ) )
        continue;
      
      $this->session->remove( $k );
    }
  }
  
  protected function constructSessionVariableName( $key )
  {
    return $this->prefix . implode( '_', array( 't', $this->getAppId( ), $key, ) );
  }
  
  public function post($data)
  {
    if(!empty($this->config['access_token']) && !empty($this->config['access_token_secret']))
    {
      $this->setToken( $this->config['access_token'], $this->config['access_token_secret'] );
      return $this->_callApi('POST', 'statuses/update', 'statuses/update', $data);
    }
    
    return false;
  }
}
