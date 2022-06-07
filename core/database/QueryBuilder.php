<?php

namespace core\database;

use core\router\App;
use PDO;
use PDOException;
use PDOStatement;
use Exception;

class QueryBuilder
{
    /**
     * This is the PDO instance.
     * @var PDO
     */
    protected $pdo;

    /**
     * This is the class name a Model will be bound to.
     * @var
     */
    protected $class_name;

    /**
     * This is the current SQL query.
     * @var
     */
    protected $sql;

    /**
     * This method is the constructor for the QueryBuilder class and simply initializes a new PDO object.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * This method returns the PDO instance.
     * @return PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * This method returns the last set SQL query.
     *
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * This method sets the class name to bind the Model to.
     * @param mixed $class_name
     * @return QueryBuilder
     */
    public function setClassName($class_name)
    {
        $this->class_name = $class_name;
        return $this;
    }

    /**
     * This method selects all of the rows from a table in a database.
     * @param string $table
     * @param string $limit
     * @param string $offset
     * @return array|false
     * @throws Exception
     */
    public function selectAll($table, $limit = "", $offset = "")
    {
        return $this->select($table, "*", $limit, $offset);
    }


    public function query($sql)
    {

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_CLASS, $this->class_name ?: "stdClass");
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return false;
    }




    public function iWhere($column, $table, $where = "")
    {
        $this->sql = "SELECT {$column} FROM {$table} WHERE {$where}";
        try {
            $statement = $this->pdo->prepare($this->sql);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_CLASS, $this->class_name ?: "stdClass");
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return false;
    }




    /**
     * This method selects rows from a table in a database where one or more conditions are matched.
     * @param string $table
     * @param $where
     * @param string $limit
     * @param string $offset
     * @return array|false
     * @throws Exception
     */
    public function selectAllWhere($table, $where, $limit = "", $offset = "")
    {
        return $this->selectWhere($table, "*", $where, $limit, $offset);
    }

    /**
     * This method returns the number of rows in a table.
     * @param string $table
     * @return  int|bool
     * @throws Exception
     */
    public function count($table)
    {
        $this->sql = "SELECT COUNT(*) FROM {$table}";
        try {
            $statement = $this->pdo->prepare($this->sql);
            $statement->execute();
            return $statement->fetchColumn();
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return false;
    }

    /**
     * This method returns the number of rows in a table where one or more conditions are matched.
     * @param string $table
     * @param $where
     * @param string $columns
     * @return int|bool
     * @throws Exception
     */
    public function countWhere($table, $where)
    {
        $where = $this->prepareWhere($where);
        $mapped_wheres = $this->prepareMappedWheres($where);
        $where = array_column($where, 3);
        $this->sql = "SELECT COUNT(*) FROM {$table} WHERE {$mapped_wheres}";
        try {
            $statement = $this->pdo->prepare($this->sql);
            $statement->execute($where);
            return $statement->fetchColumn();
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return false;
    }

    /**
     * This method selects rows from a table in a database.
     * @param string $table
     * @param string $columns
     * @param string $limit
     * @param string $offset
     * @return array|false
     * @throws Exception
     */
    public function select($table, $columns, $limit = "", $offset = "")
    {
        $limit = $this->prepareLimit($limit);
        $offset = $this->prepareOffset($offset);
        $this->sql = "SELECT {$columns} FROM {$table} {$limit} {$offset}";
        try {
            $statement = $this->pdo->prepare($this->sql);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_CLASS, $this->class_name ?: "stdClass");
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return false;
    }

    /**
     * This method selects rows from a table in a database where one or more conditions are matched.
     * @param string $table
     * @param string $columns
     * @param $where
     * @param string $limit
     * @param string $offset
     * @return array|false
     * @throws Exception
     */
    public function selectWhere($table, $columns, $where, $limit = "", $offset = "")
    {
        $limit = $this->prepareLimit($limit);
        $offset = $this->prepareOffset($offset);
        $where = $this->prepareWhere($where);
        $mapped_wheres = $this->prepareMappedWheres($where);
        $where = array_column($where, 3);
        $this->sql = "SELECT {$columns} FROM {$table} WHERE {$mapped_wheres} {$limit} {$offset}";
        try {
            $statement = $this->pdo->prepare($this->sql);
            $statement->execute($where);
            return $statement->fetchAll(PDO::FETCH_CLASS, $this->class_name ?: "stdClass");
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return false;
    }

    /**
     * This method deletes rows from a table in a database.
     * @param string $table
     * @param string $limit
     * @return int
     * @throws Exception
     */
    public function delete($table, $limit = "")
    {
        $limit = $this->prepareLimit($limit);
        $this->sql = "DELETE FROM {$table} {$limit}";
        try {
            $statement = $this->pdo->prepare($this->sql);
            $statement->execute();
            return $statement->rowCount();
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return 0;
    }


    /**
     * This method deletes rows from a table in a database where one or more conditions are matched.
     * @param string $table
     * @param $where
     * @param $limit
     * @return int
     * @throws Exception
     */
    public function deleteWhere($table, $where, $limit = "")
    {
        $limit = $this->prepareLimit($limit);
        $where = $this->prepareWhere($where);
        $mapped_wheres = $this->prepareMappedWheres($where);
        $where = array_column($where, 3);
        $this->sql = "DELETE FROM {$table} WHERE {$mapped_wheres} {$limit}";
        try {
            $statement = $this->pdo->prepare($this->sql);
            $statement->execute($where);
            return $statement->rowCount();
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return 0;
    }

    /**
     * This method inserts data into a table in a database.
     * @param string $table
     * @param $parameters
     * @return string
     * @throws Exception
     */
    public function insert($table, $parameters)
    {
        $names = $this->prepareCommaSeparatedColumnNames($parameters);
        $values = $this->prepareCommaSeparatedColumnValues($parameters);
        $this->sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $table,
            $names,
            $values
        );
        try {
            $statement = $this->pdo->prepare($this->sql);
            $statement->execute($parameters);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return "";
    }

    /**
     * This method updates data in a table in a database.
     * @param string $table
     * @param $parameters
     * @param string $limit
     * @return int
     * @throws Exception
     */
    public function update($table, $parameters, $limit = "")
    {
        $limit = $this->prepareLimit($limit);
        $set = $this->prepareNamed($parameters);
        $this->sql = sprintf(
            'UPDATE %s SET %s %s',
            $table,
            $set,
            $limit
        );
        try {
            $statement = $this->pdo->prepare($this->sql);
            $statement->execute($parameters);
            return $statement->rowCount();
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return 0;
    }

    /**
     * This method updates data in a table in a database where one or more conditions are matched.
     * @param string $table
     * @param $parameters
     * @param $where
     * @param string $limit
     * @return int
     * @throws Exception
     */
    public function updateWhere($table, $parameters, $where, $limit = "")
    {
        $limit = $this->prepareLimit($limit);
        $set = $this->prepareUnnamed($parameters);
        $parameters = $this->prepareParameters($parameters);
        $where = $this->prepareWhere($where);
        $mapped_wheres = $this->prepareMappedWheres($where);
        $where = array_column($where, 3);
        $this->sql = sprintf(
            'UPDATE %s SET %s WHERE %s %s',
            $table,
            $set,
            $mapped_wheres,
            $limit
        );
        try {
            $statement = $this->pdo->prepare($this->sql);
            $statement->execute(array_merge($parameters, $where));
            return $statement->rowCount();
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return 0;
    }

    /**
     * This method selects all of the rows from a table in a database.
     * @param string $table
     * @return array|int
     * @throws Exception
     */
    public function describe($table)
    {
        $this->sql = "DESCRIBE {$table}";
        try {
            $statement = $this->pdo->prepare($this->sql);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_CLASS, $this->class_name ?: "stdClass");
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return 0;
    }

    /**
     * This method executes raw SQL against a database.
     * @param string $sql
     * @param array $parameters
     * @return array|int
     * @throws Exception
     */
    public function raw($sql, array $parameters = [])
    {
        try {
            $this->sql = $sql;
            $statement = $this->pdo->prepare($sql);
            $statement->execute($parameters);
            $output = $statement->rowCount();
            if (stripos($sql, "SELECT") === 0) {
                $output = $statement->fetchAll(PDO::FETCH_CLASS, $this->class_name ?: "stdClass");
            }
            return $output;
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return 0;
    }

    /**
     * This method prepares the where clause array for the query builder.
     * @param $where
     * @return mixed
     */
    private function prepareWhere($where)
    {
        $array = $where;
        foreach ($where as $key => $value) {
            if (count($value) < 4) {
                array_unshift($array[$key], 0);
            }
        }
        return $array;
    }

    /**
     * This method prepares the limit statement for the query builder.
     * @param $limit
     * @return string
     */
    private function prepareLimit($limit)
    {
        return (!empty($limit) ? " LIMIT " . $limit : "");
    }

    /**
     * This method prepares the offset statement for the query builder.
     * @param $offset
     * @return string
     */
    private function prepareOffset($offset)
    {
        return (!empty($offset) ? " OFFSET " . $offset : "");
    }

    /**
     * This method prepares the comma separated names for the query builder.
     * @param $parameters
     * @return string
     */
    private function prepareCommaSeparatedColumnNames($parameters)
    {
        return implode(', ', array_keys($parameters));
    }

    /**
     * This method prepares the comma separated values for the query builder.
     * @param $parameters
     * @return string
     */
    private function prepareCommaSeparatedColumnValues($parameters)
    {
        return ':' . implode(', :', array_keys($parameters));
    }

    /**
     * This method prepares the mapped wheres.
     * @param $where
     * @return string
     */
    private function prepareMappedWheres($where)
    {
        $mapped_wheres = '';
        foreach ($where as $clause) {
            $modifier = $mapped_wheres === '' ? '' : $clause[0];
            $mapped_wheres .= " {$modifier} {$clause[1]} {$clause[2]} ?";
        }
        return $mapped_wheres;
    }

    /**
     * This method prepares the unnamed columns.
     * @param $parameters
     * @return string
     */
    private function prepareUnnamed($parameters)
    {
        return implode(', ', array_map(
            static function ($property) {
                return "{$property} = ?";
            },
            array_keys($parameters)
        ));
    }

    /**
     * This method prepares the named columns.
     * @param $parameters
     * @return string
     */
    private function prepareNamed($parameters)
    {
        return implode(', ', array_map(
            static function ($property) {
                return "{$property} = :{$property}";
            },
            array_keys($parameters)
        ));
    }

    /**
     * This method prepares the parameters with numeric keys.
     * @param $parameters
     * @param int $counter
     * @return mixed
     */
    private function prepareParameters($parameters, $counter = 1)
    {
        foreach ($array = $parameters as $key => $value) {
            unset($parameters[$key]);
            $parameters[$counter] = $value;
            $counter++;
        }
        return $parameters;
    }

    /**
     * This method binds values from an array to the PDOStatement.
     * @param PDOStatement $PDOStatement
     * @param $array
     * @param int $counter
     */
    private function prepareBindings(PDOStatement $PDOStatement, $array, $counter = 1)
    {
        foreach ($array as $key => $value) {
            $PDOStatement->bindParam($counter, $value);
            $counter++;
        }
    }

    /**
     * This method handles PDO exceptions.
     * @param PDOException $e
     * @return mixed
     * @throws Exception
     */
    private function handlePDOException(PDOException $e)
    {
        App::logError('There was a PDO Exception. Details: ' . $e);
        if (App::get('config')['options']['debug']) {
            header('HTTP/1.0 500 PDO Exception QB');
            return iView('error/500', ['error' => $e->getMessage()]);
        }
        header('HTTP/1.0 500 PDO Exception QB');
        return iView('error/500');
    }
}
