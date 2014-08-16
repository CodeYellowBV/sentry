<?php namespace Cartalyst\Sentry\SessionHandlers;
use Cartalyst\Sentry\Cookies\CookieInterface;
use Cartalyst\Sentry\Cookies\NativeCookie;
use Cartalyst\Sentry\Sessions\NativeSession;
use Cartalyst\Sentry\Sessions\SessionInterface;

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

interface SessionHandlerInterface
{

	/**
	 * Sets a value in the session
	 *
	 * @param string $key The key of the value to be set
	 * @param mixed $value The value to be set
	 * @return void
	 */
	public function set($key, $value);

	/**
	 * Get a value in the session
	 *
	 * @param string $key The key of the value to get
	 * @return void
	 */
	public function get($key);

	/**
	 * Completely destroy the session
	 *
	 * @return true;
	 */
	public function destroy();

	/**
	 * Set the session interface for this handler
	 *
	 * @param Cartalyst\Sentry\Sessions\SessionInterface $session The Sessioninterface to set
	 * @return void
	 */
	public function setSession(SessionInterface $session);

	/**
	 * Get the sessiondriver
	 *
	 * @return Cartalyst\Sentry\Sessions\SessionInterface
	 */
	public function getSession();

	/**
	 * Make the session be kept forever
	 *
	 * @return void
	 */
	public function forever();
}
