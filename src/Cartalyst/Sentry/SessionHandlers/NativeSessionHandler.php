<?php namespace Cartalyst\Sentry\SessionHandlers;
use Cartalyst\Sentry\Cookies\CookieInterface;
use Cartalyst\Sentry\Cookies\NativeCookie;
use Cartalyst\Sentry\Sessions\NativeSession;
use Cartalyst\Sentry\Sessions\SessionInterface;
/**
 * General session class. Will handle sessions more easily
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
		!isset($values[$key]) && $values = $this->cookie->get();

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
