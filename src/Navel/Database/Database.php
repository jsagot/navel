<?php

namespace Navel\Database;

/**
 * Description of Database
 *
 * @author Julien SAGOT
 */
abstract class Database implements DatabaseInterface
{
    /**
     * @var string
     */
    protected $pass;
    /**
     * @var string
     */
    protected $user;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $host;
    /**
     * @var array
     */
    protected $options = [];
    /**
     * @var mixed
     */
    protected $db;

    /**
     * @inheritDoc
     */
    abstract public function connect();

	/**
     * @inheritDoc
     */
    abstract public function disconnect();

    /**
     *
     * @param string $host
     * @param string $name
     * @param string $user
     * @param string $pass
     * @param array $options
     */
    public function __construct($host, $name, $user, $pass, $options)
    {
        $this->host = $host; // Validator::validate($host, REG_HOST_PATTERN);
        $this->name = $name;
        $this->user = $user;
        $this->pass = $pass;
        $this->options = $options;
    }
}
