<?php

namespace phangu\dNavel\Database;

/**
 * Description of MySQLDatabase
 *
 * @author Julien SAGOT
 */
abstract class PDODatabase extends Database
{
    /**
     * [setAttribute description]
     * @param  int    $attribute [description]
     * @param  mixed  $value     [description]
     * @return bool              [description]
     */
    public function setAttribute(int $attribute , $value)
    {
        return $this->db->setAttribute($attribute, $value);
    }

	/**
	 * [errorInfo function]
	 * @return string The PDO error info
	 */
    public function errorInfo()
    {
        return $this->db->errorInfo();
    }

	/**
	 * [lastInsertId function]
	 * @param  string $name [description]
	 * @return int The last id inserted
	 */
    public function lastInsertId($name = null)
    {
        return $this->db->lastInsertId($name);
    }

	/**
	 * [prepare function]
	 * @param  string $sql A string SQL query to prepare
	 * @return \PDOStatement [The PDOStatement for the prepared query]
	 */
    public function prepare($sql)
    {
        return $this->db->prepare($sql);
    }

	/**
	 * @inheritDoc
	 */
    public function disconnect()
    {
        $this->db = null;
    }
}
