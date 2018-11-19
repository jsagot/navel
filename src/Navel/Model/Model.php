<?php

namespace Navel\Model;

use Navel\Database\SQLBuilder;
use Navel\Utils\Inflect;

class Model
{
    private $context;
    private $name;
    private $type;
    private $tables = [];
    private $fields = [];
    private $types = [];
    private $queries = [];

    public function __call($name, array $arguments=null)
    {
        return call_user_func($this->$name, $arguments);
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    private function validateArgumentsCount($name, &$arguments)
    {
        // if function == find 0 arg
        if($name === 'find' && count($arguments) != 0) {
            return false;
        }
        if($name === 'find') {
            $this->context = 'select';
        }
        // if function start by findBy 1 arg
        if(substr($name, 0, 6) === "findBy" && count($arguments) != 1) {
            return false;
        }
        if(substr($name, 0, 6) === "findBy") {
            $this->context = 'select';
            $key = strtolower(str_replace('findBy', '', $name));
            $arguments[$key] = $arguments[0];
            unset($arguments[0]);
        }
        //findO
        if(substr($name, 0, (5)) === "findO" && count($arguments) != 0) {
            return false;
        }
        if(substr($name, 0, (5)) === "findO")
        {
            $this->context = 'select';
        }
        // if function = add 1 arg (Object)
        if($name === 'add' && count($arguments) != 1) {
            return false;
        }
        if($name === 'add') {
            $this->context = 'insert';
            // set object vars as arguments list
            $vars = $arguments[0]->getMembers();
            foreach ($vars as $key => $value) {
                if ($key !== 'id') {
                    $arguments[$key] = $value;
                }
            }
            unset($arguments[0]);
            //die('<pre>'.print_r($arguments, true).'</pre>');
        }
        // if function = save 1 arg (Object)
        if($name === 'save' && count($arguments) != 1) {
            return false;
        }
        if($name === 'save') {
            $this->context = 'update';
            // set object vars as arguments list
            $vars = $arguments[0]->getMembers();
            foreach ($vars as $key => $value) {
                $arguments[$key] = $value;
            }
            unset($arguments[0]);
        }
        // saveField
        if(substr($name, 0, 4) === 'save' && strlen($name) > 4) {
            $this->context = 'update';
            if(!empty($arguments)) {
                $key = strtolower(str_replace('save', '', $name));
                $arguments['id'] = $arguments[0];
                $arguments[$key] = $arguments[1];
                unset($arguments[1]);
                unset($arguments[0]);
            }
            //echo '<pre>'.print_r($arguments, true).'</pre>';
        }
        //count
        if(substr($name, 0, 5) === "count") {
            $this->context = 'count';
            if(!empty($arguments)) {
                $key = strtolower(str_replace('countForFindBy', '', $name));
                $arguments[$key] = $arguments[0];
                unset($arguments[0]);
            }
            //echo '<pre>'.print_r($arguments, true).'</pre>';
        }
        if($name === 'delete' && count($arguments) != 1) {
            return false;
        }
        if($name === 'delete' && !is_numeric($arguments[0])) {
            return false;
        }
        if($name === 'delete') {
            $this->context = 'delete';
            $arguments['id'] = $arguments[0];
            unset($arguments[0]);
        }

        return true;
    }

	/*
    private function bindParams(\PDOStatement &$statement, array $arguments)
    {
        foreach ($arguments as $key => $value) {
            $statement->bindParam(":".$key, $value);
        }
    } */

    private function executeFunction($name, $sql, $arguments, $type)
    {
        if(!$this->validateArgumentsCount($name, $arguments)) {
            throw new \InvalidArgumentException('Error: unexpected arguments count for function '.get_called_class().'->'.$name);
        }
        $db = \app\SchoolSupport::db(); // here is the breaking part
        $db->connect();
        try {
            $statement = $db->prepare($sql);
            $statement->execute($arguments);
            if($this->context == 'delete')
            {
                $results = $statement->rowCount();
            } elseif($this->context == 'count')
            {
                $results = $statement->fetchColumn();
            } elseif($this->context == 'select')
            {
                $results = $statement->fetchAll(\PDO::FETCH_CLASS, $type); // return array
                $count = count($results);
                if($count === 0) {
                    $results = false; // return false
                }
                if($count === 1) {
                    $results = $results[0]; // return object
                }
            } else { // insert update
                $results = $db->lastInsertId();
            }
        } catch (\PDOException $e) {
            //die($e->getMessage());
        }
        $this->context = null;
        unset($statement);
        $db->disconnect();
        unset($db);
        return $results;
    }

    private function generateFunctions()
    {
        $type = $this->type;
        foreach ($this->queries as $name => $sql)
        {
            $this->$name = function($args=null) use ($name, $sql, $type)
            {
                return $this->executeFunction($name, $sql, $args, $type);
            };
        }
    }

    private function generateQueries()
    {
        $sqlBuilder = new SQLBuilder();
        $queries = [];
        $descriptor = [];

        // find
        $descriptor['find'] = [
            'tables' => $this->tables,
            'type' => 'select',
            'fields' => $this->fields
        ];
        $queries['find'] = $sqlBuilder->build($descriptor['find']);
        // countForFind
        $descriptor['countForFind'] = [
            'tables' => $this->tables,
            'type' => 'count'
        ];
        $queries['countForFind'] = $sqlBuilder->build($descriptor['countForFind']);

        // findBy
        foreach ($this->fields as $field) {
            $name = 'findBy'.ucfirst($field);
            $descriptor[$name] = [
                'tables' => $this->tables,
                'type' => 'select',
                'fields' => $this->fields,
                'conditions' => [$field => "="]
            ];
            $queries[$name] = $sqlBuilder->build($descriptor[$name]);
            // countForFindBy
            $name = ucfirst($name);
            $descriptor["countFor$name"] = [
                'tables' => $this->tables,
                'type' => 'count',
                'conditions' => [$field => "="]
            ];
            $queries["countFor$name"] = $sqlBuilder->build($descriptor["countFor$name"]);
        }

        // findOrderedBy-FieldName
        foreach ($this->fields as $field) {
            $name = 'findOrderedBy'.ucfirst($field);
            $descriptor[$name] = [
                'tables' => $this->tables,
                'type' => 'select',
                'fields' => $this->fields,
                'order' => [$this->tables[0].".$field" => 'ASC'],
                'limit' => 10 // set this configurable
            ];
            $queries[$name] = $sqlBuilder->build($descriptor[$name]);
        }

        // add
        $fields = $this->fields;
        array_shift($fields);
        $descriptor['add'] = [
            'tables' => $this->tables,
            'type' => 'insert',
            'fields' => $fields,
            'binds' => $fields
        ];
        $queries['add'] = $sqlBuilder->build($descriptor['add']);

        // save
        $descriptor['save'] = [
            'tables' => $this->tables,
            'type' => 'update',
            'fields' => $fields
        ];
        $queries['save'] = $sqlBuilder->build($descriptor['save']);

        // saveFields expectes two parameters
        foreach ($fields as $field) {
            $name = 'save'.ucfirst($field);
            $descriptor[$name] = [
                'tables' => $this->tables,
                'type' => 'update',
                'fields' => [$field]
            ];
            $queries[$name] = $sqlBuilder->build($descriptor[$name]);
        }

        // delete !!!
        $descriptor['delete'] = [
            'tables' => $this->tables,
            'type' => 'delete'
        ];
        $queries['delete'] = $sqlBuilder->build($descriptor['delete']);

        $this->queries = $queries;
        $this->generateFunctions();
    }

    /**
     * @param string $class
     */
    public function __construct($class)
    {
        $entity = new $class();
        if(get_parent_class($entity) !== Entity::class) {
            throw new \Exception("$class must be a child instance of ".Entity::class);
        }
        $this->type = $class;
        $tmp = explode('\\', $class);
        $this->name = strtolower($tmp[count($tmp)-1]);
        $table = Inflect::pluralize($this->name);
        $this->tables = [$table];
        $fields = $entity->getMembers();
        foreach ($fields as $key => $value) {
            $this->fields[] = $key;
            $this->types[":$key"] = gettype($value);
        }
        $this->generateQueries();
    }
}
