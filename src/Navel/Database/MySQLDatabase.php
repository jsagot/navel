<?php

namespace Navel\Database;

/**
 * Description of MySQLDatabase
 *
 * @author Julien SAGOT
 */
class MySQLDatabase extends PDODatabase
{
	/**
	 * @inheritDoc
	 */
    public function connect()
    {
        if(is_null($this->db))
        {
            $dsn = "mysql:host=$this->host;dbname=$this->name;charset=UTF8";
            try {
                $this->db = new \PDO($dsn, $this->user, $this->pass, $this->options);
                unset($dsn);
            } catch (\PDOException $e) {
                die("DATABASE Error : " . $e->getMessage());
                // use a logger
            }
        }
    }
}
