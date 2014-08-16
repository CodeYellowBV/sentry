<?php namespace Cartalyst\Sentry\SessionHandlers;
use Cartalyst\Sentry\Cookies\CookieInterface;
use Cartalyst\Sentry\Cookies\NativeCookie;
use Cartalyst\Sentry\Sessions\NativeSession;
use Cartalyst\Sentry\Sessions\SessionInterface;
/**
 * General session class. Will handle sessions more easily
 */
interface SessionHandlerInterface
{

	/**
	 * Sets a value in the session
	 */
	public function set($key, $value);

	public function get($key);

	public function destroy();

	public function setSession(SessionInterface $session);

	public function getSession();

	public function forever();
}
