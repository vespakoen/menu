<?php 
namespace Menu;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\ParameterBag;

class Request
{

	/**
	 * The Symfony HttpFoundation Request instance.
	 *
	 * @var HttpFoundation\Request
	 */
	public static $foundation;

	public function start()
	{
		static::$foundation = static::createFromGlobals();
	}

	/**
     * Creates a new request with values from PHP's super globals.
     *
     * @return Request A new request
     *
     * @api
     */
	public static function createFromGlobals()
	{
		$request = new SymfonyRequest($_GET, $_POST, array(), $_COOKIE, $_FILES, $_SERVER);

		if (0 === strpos($request->server->get('CONTENT_TYPE'), 'application/x-www-form-urlencoded')
		    && in_array(strtoupper($request->server->get('REQUEST_METHOD', 'GET')), array('PUT', 'DELETE', 'PATCH'))
		) {
		    parse_str($request->getContent(), $data);
		    if (magic_quotes()) $data = array_strip_slashes($data);
		    $request->request = new ParameterBag($data);
		}

		if (0 === strpos($request->server->get('CONTENT_TYPE'), 'application/json')
		    && in_array(strtoupper($request->server->get('REQUEST_METHOD', 'GET')), array('POST', 'PUT', 'DELETE', 'PATCH'))
		) {
		    $data = $request->getContent() ? json_decode($request->getContent(), TRUE) : array();
		    if (magic_quotes()) $data = array_strip_slashes($data);
		    $request->request = new ParameterBag($data);
		} 

		return $request;
	}

	/**
	 * Get the Symfony HttpFoundation Request instance.
	 *
	 * @return HttpFoundation\Request
	 */
	public static function foundation()
	{
		return static::$foundation;
	}

	/**
	 * Pass any other methods to the Symfony request.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(static::foundation(), $method), $parameters);
	}

}