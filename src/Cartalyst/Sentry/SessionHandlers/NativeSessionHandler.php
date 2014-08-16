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

class NativeSessionHandler implements SessionHandlerInterface
{
	/**
	 * The session driver used by Sentry.
	 *
	 * @var \Cartalyst\Sentry\Sessions\SessionInterface
	 */
	protected $session;

	/**
	 * The cookie driver used by Sentry.
	 *
	 * @var \Cartalyst\Sentry\Cookies\CookieInterface
	 */
	protected $cookie;

	/**
	 * Construct a new SessionHandler
	 *
	 * @param Cartalyst\Sentry\Sessions\SessionInterface $session The sessiondriver
	 * @param Cartalyst\Sentry\Cookies\CookieInterface $cookie The CookieInterface
	 */
	public function __construct(
		SessionInterface $session = null,
		CookieInterface $cookie = null
	) {
		$this->session = $session ?: new NativeSession;
		$this->cookie  = $cookie ?: new NativeCookie;
	}

	/**
	 * Sets a value in the session
	 */
	public function set($key, $value)
	{
		$values = $this->session->get();
		$values[$key] = $value;
		$this->session->put($values);
	}

	public function get($key)
	{
		$values = $this->session->get();
		!isset($values[$key]) && $values = (array)$this->cookie->get();
		return isset($values[$key]) ? $values[$key] : null;		
	}

	/**
	 * Destroys the session
	 */
	public function destroy()
	{
		$this->session->forget();
		$this->cookie->forget();
	}

	public function setSession(SessionInterface $session) 
	{
		$this->session = $session;
	}

	public function getSession()
	{
		return $this->session;
	}

	public function forever()
	{
		// Make sure that the values that are forever are not lost
		$cookie = $this->cookie->get();
		$session = $this->session->get();
		if ($cookie == null) {
			$cookie = $session;
		} else if($session != null) {
			$cookie = array_merge($cookie, $this->session->get());
		}

		$this->cookie->forever($cookie);
	}
}
