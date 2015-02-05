<?php namespace Cartalyst\Sentry\Tests;
/**
 * Part of the Sentry package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Sentry
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Sentry\SessionHandlers\NativeSessionHandler;
use Mockery as m;
use PHPUnit_Framework_TestCase;
use stdClass;

class NativeSessionHAndlerTest extends PHPUnit_Framework_TestCase {

	protected $sessionHandler;
	protected $session;
	protected $cookie;
	/**
	 * Close mockery.
	 *
	 * @return void
	 */
	public function tearDown()
	{
		m::close();
	}

	/**
	 * Setup resources and dependencies.
	 *
	 * @return void
	 */
	public function setUp()
	{
		$this->sessionHandler = new NativeSessionHandler(
			$this->session = m::mock('Cartalyst\Sentry\Sessions\SessionInterface'),
			$this->cookie = m::mock('Cartalyst\Sentry\Cookies\CookieInterface')
		);
	}

	/**
	 * Check if a session is set correctly if the session is empty before
	 */
	public function testSetWithEmptySession() {
		$this->session->shouldReceive('get')->once()->andReturn(null);
		$this->session->shouldReceive('put')->once()->with(array('test' => 1));
		$this->sessionHandler->set('test', 1);
	}

	/**
	 * check if a session is set correctly if the session was not empty before
	 */
	public function testSetWithNonEmptySession() {
		$this->session->shouldReceive('get')->once()->andReturn(array('foo' => 'bar'));
		$this->session->shouldReceive('put')->once()->with(array('test' => 1, 'foo' => 'bar'));

		$this->sessionHandler->set('test', 1);
	}

	/**
	 * Check if get($key) return null if the key does not exists
	 */
	public function testGetReturnsNullIfValueDoesNotExist() {
		$this->session->shouldReceive('get')->once()->andReturn(array('foo' => 'bar'));
		$this->cookie->shouldReceive('get')->once()->andReturn(array('foo' => 'bar'));
		$this->assertNull($this->sessionHandler->get('bizz'));
	}

	/**
	 * Check if get($key) return null if no session is set
	 */
	public function testGetReturnsNullIfNoSessionIsSet() {
		$this->session->shouldReceive('get')->once()->andReturn(null);
		$this->cookie->shouldReceive('get')->once()->andReturn(null);
		$this->assertNull($this->sessionHandler->get('bizz'));
	}

	/**
	 * check if get($key) returns the correct value if it is set in session
	 */
	public function testGetReturnsIfIsSetInSession() {
		$this->session->shouldReceive('get')->once()->andReturn(array('foo' => 'bar'));
		$this->assertEquals('bar', $this->sessionHandler->get('foo'));
	}

	/**
	 * check if get($key) returns the correct value if it is set in Cookie, and not in session
	 */
	public function testGetReturnsIfIsSetInCookieNoSession() {
		$this->session->shouldReceive('get')->once()->andReturn(null);
		$this->cookie->shouldReceive('get')->once()->andReturn(array('foo' => 'bar'));
		$this->assertEquals('bar', $this->sessionHandler->get('foo'));
	}

	/**
	 * check if get($key) returns the correct value if it is set in Cookie, and session is set
	 * but with different values
	 */
	public function testGetReturnsIfIsSetInCookieWithSession() {
		$this->session->shouldReceive('get')->once()->andReturn(array('fuzz' => 'bizz'));
		$this->cookie->shouldReceive('get')->once()->andReturn(array('foo' => 'bar'));
		$this->assertEquals('bar', $this->sessionHandler->get('foo'));
	}

	/** 
	 * Checks if the destroy method destroys both the session and cookie
	 */
	public function testDestroyShouldForgetBothSessionAndCookie() {
		$this->session->shouldReceive('forget')->once();
		$this->cookie->shouldReceive('forget')->once();
		$this->sessionHandler->destroy();
	}

	/**
	 * Check if setSession/getSession works
	 */
	public function testSetGetSession() {
		$newSession = m::mock('Cartalyst\Sentry\Sessions\SessionInterface');
		$this->sessionHandler->setSession($newSession);

		$newSession->shouldReceive('put')->with(array('foo' => 'bar'))->once();
		$newSession->shouldReceive('get')->andReturn(null);
		$this->sessionHandler->set('foo', 'bar');
		$this->assertEquals($newSession, $this->sessionHandler->getSession());
	}

	/** 
	 * Forever should push the current session to the cookie
	 */
	public function testForeverPushesSessionToCookie() {
		$this->cookie->shouldReceive('get')->andReturn(null);
		$this->session->shouldReceive('get')->andReturn(array('foo' => 'bar'));
		$this->cookie->shouldReceive('forever')->with(array('foo' => 'bar'));
		$this->sessionHandler->forever();
	}

	/** 
	 * Forever should push the current session to the cookie, but keep the existing values
	 */
	public function testForeverKeepsCookieValues() {
		$this->cookie->shouldReceive('get')->andReturn(array('fizz' => 'buzz'));
		$this->session->shouldReceive('get')->andReturn(array('foo' => 'bar'));
		$this->cookie->shouldReceive('forever')->with(array('foo' => 'bar', 'fizz' => 'buzz'));
		$this->sessionHandler->forever();
	}

	/** 
	 * Forever should do nothing if the session is null
	 */
	public function testForeverKeepsCookieValuesIfSessionIsNull() {
		$this->cookie->shouldReceive('get')->andReturn(array('fizz' => 'buzz'));
		$this->session->shouldReceive('get')->andReturn(null);
		$this->cookie->shouldReceive('forever')->with(array('fizz' => 'buzz'));
		$this->sessionHandler->forever();
	}


	/** 
	 * Forever should do nothing if the session is null and cookie is null
	 */
	public function testForeverDoesNothingIfSessionAndCookieAreNull() {
		$this->cookie->shouldReceive('get')->andReturn(null);
		$this->session->shouldReceive('get')->andReturn(null);
		$this->cookie->shouldReceive('forever')->with(null);
		$this->sessionHandler->forever();
	}

	/** 
	 * If a value exists in both the cookie as well as in the session, the cookie should be overridden
	 */
	public function testForeverSessionShouldOverrideCookie() {
		$this->cookie->shouldReceive('get')->andReturn(array('foo' => 'bar1'));
		$this->session->shouldReceive('get')->andReturn(array('foo' => 'bar2'));
		$this->cookie->shouldReceive('forever')->with(array('foo' => 'bar2'));
		$this->sessionHandler->forever();
	}

}
