<?php

namespace Navel\Database;

/**
 * Description of SQLBuilder
 *
 * @author Julien SAGOT
 */
final class SQLBuilder
{
    /**
     *
     * @param string[] $binds
     * @return string
     */
    private function getBinds($binds)
    {
        $tmp = '';
        foreach ($binds as $bind) {
            $tmp .= ":$bind, ";
        }
        $sql = trim(trim($tmp, " "), ",");
        unset($tmp);
        return $sql;
    }
    /**
     *
     * @param string[]|string $orders
     * @return string
     */
    private function getOrder($orders)
    {
        $tmp = '';
        foreach ($orders as $name => $way) {
            $expl = explode('.', $name);
            $tmp .= " `".$expl[0]."`.`".$expl[1]."` ".$way;
        }
        $sql = trim($tmp, " ");
        unset($tmp);
        return $sql;
    }
    /**
     *
     * @param string[]|string $conditions
     * @return string
     */
    private function getConditions($conditions)
    {
        $tmp = '';
        foreach ($conditions as $name => $sign) {
            $tmp .= " AND ".$name.' '.$sign." :$name";
        }
        $sql = trim($tmp, " ");
        unset($tmp);
        return $sql;
    }
    /**
     *
     * @param string[]|string $tables
     * @return string
     */
    private function getTables($tables)
    {
		/*
        if(!is_array($tables))
        {
            return "`$tables`";
        } */
        $tmp = '';
        foreach ($tables as $table) {
           $tmp .= " `".$table."`,";
        }
        $sql = trim(trim($tmp, " "), ",");
        unset($tmp);
        unset($table);
        unset($tables);
        return $sql;
    }
    /**
     *
     * @param string[]|string $fields
     * @return string
     */
    private function getFields($fields)
    {
		/*
        if(!is_array($fields))
        {
            return $fields;
        } */
        $tmp = '';
        foreach ($fields as $field) {
           $tmp .= " `".$field."`,";
        }
        $sql = trim(trim($tmp, " "), ",");
        unset($tmp);
        unset($field);
        unset($fields);
        return $sql;
    }
    /**
     *
     * @param string[]|string $fields
     * @return string
     */
    private function getUpdateFields($fields)
    {
		/*
        if(!is_array($fields))
        {
            return " $fields = :$fields";
        } */
        $tmp = '';
        foreach ($fields as $field) {
           $tmp .= " $field = :$field,";
        }
        $sql = trim(trim($tmp, " "), ",");
        unset($tmp);
        unset($field);
        unset($fields);
        return $sql;
    }
    /**
     *
     * @param array $descriptor
     */
    private function select(array $descriptor)
    {
        $sql = 'SELECT ';
        $sql .= $this->getFields($descriptor['fields']);
        $sql .= ' FROM '.$this->getTables($descriptor['tables']);
        if(isset($descriptor['conditions']))
        {
            $sql .= ' WHERE 1 '.$this->getConditions($descriptor['conditions']);
        }
        if(isset($descriptor['order']))
        {
            $sql .= ' ORDER BY '.$this->getOrder($descriptor['order']);
        }
        if(isset($descriptor['limit']))
        {
            $sql .= " LIMIT ".$descriptor['limit'];
        }

        return $sql;
    }

    /**
     *
     * @param array $descriptor
     */
    private function insert(array $descriptor)
    {
        // INSERT INTO `categories` (`id`, `name`, `position`) VALUES (NULL, 'Test2', '5');
        $sql = 'INSERT INTO '.$this->getTables($descriptor['tables']);
        $sql .= ' ('.$this->getFields($descriptor['fields']).')';
        $sql .= ' VALUES ('.$this->getBinds($descriptor['binds']).')';

        return $sql;
    }

    /**
     *
     * @param array $descriptor
     */
    private function update(array $descriptor)
    {
        //UPDATE `categories` SET `name` = 'Longboad', `position` = '12' WHERE `categories`.`id` = 16
        $sql = 'UPDATE '.$this->getTables($descriptor['tables']);
        $sql .= ' SET '.$this->getUpdateFields($descriptor['fields']).' WHERE id = :id';
        return $sql;
    }

    /**
     *
     * @param array $descriptor
     */
    private function count(array $descriptor, $name)
    {
        $sql = 'SELECT COUNT(*) AS rowCount FROM '.$this->getTables($descriptor['tables']);
        if(isset($descriptor['conditions']))
        {
            $sql .= ' WHERE 1 '.$this->getConditions($descriptor['conditions']);
        }
        return $sql;
    }

    /**
     *
     * @param array $descriptor
     */
    private function delete(array $descriptor)
    {
        return 'DELETE FROM '.$this->getTables($descriptor['tables']).' WHERE id = :id';
    }

    /**
     *
     * @param array $descriptor
     * @return string
     */
    public function build(array $descriptor, $name =null)
    {
        //echo '<pre>'. print_r($descriptor, true).'</pre>';
        $type = strtoupper($descriptor['type']);
        $sql = '';
        switch ($type) {
            case 'SELECT':
                $sql = $this->select($descriptor);
                break;
            case 'COUNT':
                $sql = $this->count($descriptor, $name);
                break;
            case 'INSERT':
                $sql = $this->insert($descriptor);
                break;
            case 'UPDATE':
                $sql = $this->update($descriptor);
                break;
            case 'DELETE':
                $sql = $this->delete($descriptor);
                break;
            default:
                throw new UnexpectedValueException("$type sql instruction is not supported by ".__CLASS__);
        }

        return $sql;
    }
}
