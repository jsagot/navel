<?php

namespace Navel\Database;

/**
 * Description of SQLiteDatabase
 *
 * @author Julien SAGOT
 */
class SQLiteDatabase extends PDODatabase
{
    /**
     * [query description]
     * @param  string $sql [description]
     * @return mixed       [description]
     */
    public function query($sql)
    {
        return $this->db->exec($sql);
    }

	/**
	 * @inheritDoc
	 */
    public function connect()
    {
        if(!file_exists($this->name))
        {
            file_put_contents($this->name,'');
        }
        if(is_null($this->db))
        {
            $dsn = 'sqlite:'.$this->name;
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
