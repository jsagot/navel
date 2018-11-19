<?php

namespace Navel\Database;

/**
 * Description of DatabaseInterface
 *
 * @author Julien SAGOT
 */
interface DatabaseInterface
{
	/**
	 * [connect function]
	 */
	public function connect();

	/**
	 * [disconnect function]
	 */
	public function disconnect();
}
