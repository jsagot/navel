<?php
/**
 * Summary of phangu\routing\Route
 */

/**
 * phangu\routing namespace description
 */
namespace Navel\Routing;

/**
 * Route object
 *
 * @author Julien SAGOT
 * @package Navel\Routing
 */
final class Route
{
    private $_name;
    private $_path;
    private $_middleware;
    private $_patterns = [];
    private $_params = [];

    /**
     * Get the Route name.
     *
     * @return string The Route name
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Get the Route path.
     *
     * @return string The Route path
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * Get the Route middleware function.
     *
     * @return callable The Route middleware
     */
    public function getMiddleware()
    {
        return $this->_middleware;
    }

    /**
     * Get the middleware parameters.
     *
     * @return string[] The Route parameters
     */
    public function getParameters()
    {
        return $this->_params;
    }

    /**
     * Get the patterns to find middleware parameters.
     *
     * @internal Never used, usefull ??
     * @return string[] PCRE Regex patterns
     */
    public function getPatterns()
    {
        return $this->_patterns;
    }

    /**
     *
     * Get the Route uri
     *
     * @param array $params
     * @return string The route URI
     */
    public function getUri($params = [])
    {
        $this->_patterns = [];
        $path = $this->_path;
        $tmp = preg_replace_callback(
            '#\{(.*?)\}#',
            [$this, 'extractPatterns'],
            $path
        );

		if(is_array($params)) {
			if(count($params) > 0) {
				$path = $tmp;
			}

			foreach ($params as $key => $value) {
				$path = str_replace(":$key", $value, $path);
			}
		}

        return '/'.$path;
    }

    /**
     * Search route for match and extract parameters
     *
     * @param string $uri
     * @return bool
     */
    public function match($uri)
    {
        // clean uri from extra '/'
        $uri = trim($uri, '/');

        // pass 1 extractPatterns
        $tmp = preg_replace_callback(
            '#\{(.*?)\}#',
            [$this, 'extractPatterns'],
            $this->_path
        );

        // route params keys
        $keys = explode('/', $tmp);
        array_shift($keys);
        array_walk($keys, function(&$value, $key) {
            $value = str_replace(':', '', $value);
        });

        // pass 2 extractParams
        $tmp = preg_replace_callback(
            '#:([\w]+)#',
            [$this, 'extractParams'],
            $tmp
        );

        $regex = "#^$tmp$#i";
        $params = [];
        if (!preg_match($regex, $uri, $params)) {
            return false;
        }

        // route params
        if(is_array($params)) {
			array_shift($params);
			if(count($keys) === count($params)) {
				$this->_params = array_combine($keys, $params);
			}
		}

        return true;
    }

    /**
     * Constructor
     *
     * @param string $path
     * @param callable $middleware
     * @param string $name
     */
    public function __construct($path, $middleware, $name)
    {
        $this->_path = trim($path, '/');
        $this->_middleware = $middleware;
        $this->_name = $name;
    }

    /**
     * @internal
     * @param array $match
     * @return string
     */
    private function extractPatterns(array $match)
    {
        $matches = explode(':', $match[1]);
        $this->_patterns[$matches[0]] = $matches[1];
        //echo '<pre>'.print_r($matches, true).'</pre>';
        return ':'.$matches[0];
    }

    /**
     * @internal
     * @param array $match
     * @return string
     */
    private function extractParams(array $match)
    {
        /*
        if (isset($this->_patterns[$match[1]])) {
            return '('.$this->_patterns[$match[1]].')';
        }

        // return '([^/]+)'; // Dead code ?
        */

        return '('.$this->_patterns[$match[1]].')';
    }
}
