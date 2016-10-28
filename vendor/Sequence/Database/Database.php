<?php


namespace Sequence\Database;


use PDO;
use PDOException;


/**
* @package      sequence
* @subpackage	database
* @author       Filip Szczechowiak <filip.szczechowiak@gmail.com>
*/
class Database
{
    const SELECT    = 'select';
    const INSERT    = 'insert';
    const UPDATE    = 'update';
    const DELETE    = 'delete';
    const DISTINCT  = 'distinct';
    const COLUMNS   = 'columns';
    const FROM      = 'from';
    const JOIN      = 'join';
    const WHERE     = 'where';
    const GROUP     = 'group';
    const ORDER     = 'order';
    const LIMIT     = 'limit';
    const OFFSET    = 'offset';
    
    const INNER_JOIN    = 'inner';
    const LEFT_JOIN     = 'left';
    const RIGHT_JOIN    = 'right';
    const FULL_JOIN     = 'full';
    const CROSS_JOIN    = 'cross';
    const NATURAL_JOIN  = 'natural';

    const SQL_WILDCARD  = '*';
    const SQL_SELECT    = 'SELECT';
    const SQL_INSERT    = 'INSERT INTO';
    const SQL_UPDATE    = 'UPDATE';
    const SQL_DELETE    = 'DELETE FROM';
    const SQL_FROM      = 'FROM';
    const SQL_JOIN      = 'JOIN';
    const SQL_WHERE     = 'WHERE';
    const SQL_GROUP_BY  = 'GROUP BY';
    const SQL_ORDER_BY  = 'ORDER BY';
    const SQL_AND       = 'AND';
    const SQL_SET       = 'SET';
    const SQL_AS        = 'AS';
    const SQL_OR        = 'OR';
    const SQL_ON        = 'ON';
    const SQL_ASC       = 'ASC';
    const SQL_DESC      = 'DESC';
    const SQL_LIMIT     = 'LIMIT';
    const SQL_OFFSET    = 'OFFSET';

    protected $options;
    protected $parts = array(
        'type'          => null,
        self::SELECT    => array(),
        self::INSERT    => array(),
        self::UPDATE    => array(),
        self::DELETE    => array(),
        self::FROM      => array(),
        self::JOIN      => array(),
        self::WHERE     => array(),
        self::GROUP     => array(),
        self::ORDER     => array(),
        self::LIMIT     => null,
        self::OFFSET    => null,
    );
    protected $query    = array();
    protected $bind     = array();
    protected $profiler = array();

    /**
     * @var \PDO
     */
    protected $connection = false;

    /**
     * @param array $options
     *  * username - użytkownik bazy danych
     *  * password - hasło użytkownika
     *  * driver_options - tablica asocjacyjna dodatkowych ustawień przekazywanych do sterownika połączenia, domyślnie:
     *    - PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
     *    - PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
     * @throws \InvalidArgumentException
     */
    public function __construct(array $options)
    {
        $this->options = array_replace_recursive(array(
            'username'          => null,
            'password'          => null,
            'driver_options'    => array(
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
            )
        ), $options);

        if(!isset($this->options['dsn']))
        {
            throw new \InvalidArgumentException('Brak argumentu "dsn" dla ustawień połączenia z bazą danych.');
        }
    }

    /**
     * @return bool
     * @throws \RuntimeException
     */
    public function connect()
    {
        if(false !== $this->connection)
        {
            return true;
        }
        
        try
        {
            $this->connection = new PDO($this->options['dsn'], $this->options['username'], $this->options['password'], $this->options['driver_options']);
            
        } catch(PDOException $e)
        {
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
    * @return array
    */
    public function getLastQuery()
    {
        return end($this->profiler);
    }

    /**
    * @return int
    */
    public function getQueryCount()
    {
        return count($this->profiler);
    }

    /**
    * Zwraca tablicę wykonanych zapytań.
    * 
    * @return array
    */
    public function getProfiler()
    {
        return $this->profiler;
    }

    /**
    * Resetuje część lub całość zapytania.
    * 
    * @param    $part string - nazwa części
    * @return   Database
    */
    public function reset($part = null)
    {
        if(null === $part)
        {
            $this->query = $this->parts;
            $this->bind = array();
        } elseif(isset($this->parts[$part]))
        {
            unset($this->query[$part]);
            
            if($this->query['type'] == $part)
            {
                unset($this->query['type']);
            }
        }
        
        return $this;
    }

    /**
    * Budowanie zapytania typu select.
    * 
    * @param    array $fields - standardowy sql, jako string lub tablica. Domyślnie "*"
    * @return   Database
    */
    public function select($fields = null)
    {
        $this->reset();
        
        if(null === $fields)
        {
            $this->query[self::SELECT] = self::SQL_WILDCARD;
        } else
        {
            $this->query[self::SELECT] = $fields;
        }
        
        $this->query['type'] = self::SELECT;
        
        return $this;
    }

    /**
    * Budowanie zapytania typu insert.
    * 
    * @param    string $table - nazwa tabeli
    * @param    array $bind - tablica wartości, ["nazwa_kolumny" => "wartość"]
    * @return   Database
    */
    public function insert($table, array $bind)
    {
        $this->reset();
        
        $this->query[self::INSERT] = array(
            'table' => $table,
            'bind' => $bind
        );
        
        $this->query['type'] = self::INSERT;
        
        return $this;
    }

    /**
    * Budowanie zapytania typu update.
    * 
    * @param    string $table - nazwa tabeli
    * @param    array $bind - tablica wartości, ["nazwa_kolumny" => "wartość"]
    * @return   Database
    */
    public function update($table, array $bind)
    {
        $this->reset();
        
        $this->query[self::UPDATE] = array(
            'table' => $table,
            'bind' => $bind
        );
        
        $this->query['type'] = self::UPDATE;
        
        return $this;
    }

    /**
    * Budowanie zapytania typu delete.
    * 
    * @param    string $table - nazwa tabeli
    * @return   Database
    */
    public function delete($table)
    {
        $this->reset();
        
        $this->query[self::DELETE] = array('table' => $table);
        
        $this->query['type'] = self::DELETE;
        
        return $this;
    }

    /**
    * Dodaje FROM do zapytania
    * 
    * @param   mixed $tables - string 'table' lub array('alias' => 'tabela')
    * @return  Database
    */
    public function from($tables)
    {
        if(is_array($tables))
        {
            foreach($tables as $alias => $table)
            {
                $this->query[self::FROM][] = array(
                    'alias' => $alias,
                    'name' => $table
                );
            }
        } elseif(is_string($tables))
        {
            $this->query[self::FROM][] = array('name' => $tables);
        }
        
        return $this;
    }

    /**
    * Złączenie tabel, domyślnie INNER JOIN.
    * 
    * @param   string $table - nazwa tabeli
    * @param   string $on - warunek złączenia
    * @param   string $alias - alias tabeli
    * @param   string $type - typ złączenia, domyślnie INNER JOIN
    * @return  Database 
    */
    public function join($table, $on, $alias = null, $type = self::INNER_JOIN)
    {
        $this->query[self::JOIN][] = array(
            'table'  => $table,
            'on'     => $on,
            'alias'  => $alias,
            'type'   => $type,
        );
    	
    	return $this;
    }

    /**
    * Złączenie tabel INNER JOIN.
    * 
    * @param   string $table - nazwa tabeli
    * @param   string $on - warunek złączenia
    * @param   string $alias - alias tabeli
    * @return  Database 
    */
    public function innerJoin($table, $on, $alias = null)
    {
        return $this->join($table, $on, $alias, self::INNER_JOIN);
    }

    /**
    * Złączenie tabel LEFT JOIN.
    * 
    * @param   string $table - nazwa tabeli
    * @param   string $on - warunek złączenia
    * @param   string $alias - alias tabeli
    * @return  Database 
    */
    public function leftJoin($table, $on, $alias = null)
    {
        return $this->join($table, $on, $alias, self::LEFT_JOIN);
    }

    /**
    * Złączenie tabel RIGHT JOIN.
    * 
    * @param   string $table - nazwa tabeli
    * @param   string $on - warunek złączenia
    * @param   string $alias - alias tabeli
    * @return  Database 
    */
    public function rightJoin($table, $on, $alias = null)
    {
        return $this->join($table, $on, $alias, self::RIGHT_JOIN);
    }

    /**
    * Złączenie tabel FULL JOIN.
    * 
    * @param   string $table - nazwa tabeli
    * @param   string $on - warunek złączenia
    * @param   string $alias - alias tabeli
    * @return  Database 
    */
    public function fullJoin($table, $on, $alias = null)
    {
        return $this->join($table, $on, $alias, self::FULL_JOIN);
    }

    /**
    * Złączenie tabel CROSS JOIN.
    * 
    * @param   string $table - nazwa tabeli
    * @param   string $on - warunek złączenia
    * @param   string $alias - alias tabeli
    * @return  Database 
    */
    public function crossJoin($table, $on, $alias = null)
    {
        return $this->join($table, $on, $alias, self::CROSS_JOIN);
    }

    /**
    * Złączenie tabel NATURAL JOIN.
    * 
    * @param   string $table - nazwa tabeli
    * @param   string $on - warunek złączenia
    * @param   string $alias - alias tabeli
    * @return  Database 
    */
    public function naturalJoin($table, $on, $alias = null)
    {
        return $this->join($table, $on, $alias, self::NATURAL_JOIN);
    }

    /**
    * Warunek zapytania.
    * 
    * @param    mixed $condition - warunek, array ["kolumna" => "wartość"] lub string "kolumna = wartość"
    * @param    string $type - typ warunku, OR lub AND, domyślie AND.
    * @return   Database
    * @throws   \InvalidArgumentException - w przypadku nieobsługiwanego typu warunku.
    */
    public function where($condition, $type = self::SQL_AND)
    {
        if(is_array($condition))
        {
            foreach($condition as $column => $value)
            {
                $this->query[self::WHERE][] = array(
                    'column' => $column,
                    'type' => empty($this->query[self::WHERE]) ? null : $type,
                    'bind' => $this->addBind($column, $value)
                );
            }
        } elseif(is_string($condition))
        {
            $this->query[self::WHERE][] = array(
                'column' => $condition,
                'type' => empty($this->query[self::WHERE]) ? null : $type,
                'bind' => false
            );
            
        } else
        {
            throw new \InvalidArgumentException('Podany warunek, jest nieobsługiwanym typem. Obsługiwane typy to array i string.');
        }
        
        return $this;
    }

    /**
    * Warunek zapytania.
    * 
    * @param    mixed $condition - warunek, array ["kolumna" => "wartość"] lub string "kolumna = wartość'"
    * @return   Database
    */
    public function andWhere($condition)
    {
        return $this->where($condition, self::SQL_AND);
    }

    /**
    * Warunek zapytania.
    * 
    * @param    mixed $condition - warunek, array ["kolumna" => "wartość"] lub string "kolumna = wartość'"
    * @return   Database
    */
    public function orWhere($condition)
    {
        return $this->where($condition, self::SQL_OR);
    }

    /**
    * Grupowanie zapytania.
    * 
    * @param    mixed $group - ciąg 'kolumna', lub tablica kolumn array(kolumna, kolumna)
    * @return   Database
    */
    public function group($group)
    {
        foreach((array)$group as $column)
        {
            $this->query[self::GROUP][] = $column;
        }
        
        return $this;
    }

    /**
    * Sortowanie zapytania.
    * 
    * @param    mixed $column - ciąg kolumna, lub tablica kolumn. Przykłady:
    *  - order(array('kolumna', 'kolumna2', 'kolumna3')) - ORDER BY kolumna ASC, kolumna2 ASC, kolumna3 ASC
    *  - order(array('kolumna' => 'ASC', 'kolumna2' => 'DESC', 'kolumna3' => 'DESC')) - ORDER BY kolumna ASC, kolumna2 DESC, kolumna3 DESC
    *  - order('kolumna') - ORDER BY kolumna ASC
    *  - order('kolumna DESC') - ORDER BY kolumna DESC
    *           
    * @return   Database
    */
    public function order($column)
    {
        foreach((array)$column as $col => $sort)
        {
            if(is_string($col))
            {
                $this->query[self::ORDER][] = $col.' '.$sort;
            } else
            {
                $this->query[self::ORDER][] = $sort;
            }
        }
        
        return $this;
    }

    /**
    * Limiowanie zapytania.
    * 
    * @param    mixed $count - limit rekordów.
    * @return   Database
    */
    public function limit($count)
    {
        $this->query[self::LIMIT] = $count;
        
        return $this;
    }

    /**
    * Limiowanie zapytania.
    * 
    * @param    int $count - limit rekordów począwszy od podanego rekordu.
    * @return   Database
    */
    public function offset($count)
    {
        $this->query[self::OFFSET] = $count;
        
        return $this;
    }

    /**
     * Podpinanie zapytania.
     *
     * @return \PDOStatement
     * @throws \RuntimeException
     */
    public function get()
    {
        $sql = '';

        foreach($this->query as $part => $value)
        {
            if(!empty($value))
            {
                $method = '_render'.ucfirst($part);
                if(method_exists($this, $method))
                {
                    $sql .= $this->$method();
                }
            }
        }

        $this->profiler[] = array(
            'sql' => $sql,
            'bind' => $this->bind,
        );

        if(false === $this->connection)
        {
            $this->connect();
        }

        try
        {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($this->bind);

            if(in_array($this->query['type'], array(self::INSERT, self::UPDATE, self::DELETE)))
            {
                return $stmt->rowCount();
            }

            return $stmt;
        } catch(PDOException $e)
        {
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
     * Prepares a statement for execution and returns a statement object and
     * execute a prepared statement by passing an array of insert values.
     *
     * @param string $sql
     * @param array $input_parameters
     *
     * @return \PDOStatement
     *
     * @throws \RuntimeException
     */
    public function sql($sql, array $input_parameters)
    {
        if(false === $this->connection)
        {
            $this->connect();
        }

        $this->profiler[] = array(
            'sql' => $sql,
            'bind' => $input_parameters,
        );

        try
        {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($input_parameters);

            return $stmt;

        } catch(PDOException $e)
        {
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
     * Alias PDO::query()
     *
     * @see   PDO::query()
     * @throws   \RuntimeException
     */
    public function query($sql)
    {
        if(false === $this->connection)
        {
            $this->connect();
        }

        try
        {
            $this->profiler[] = $sql;

            return $this->connection->query($sql);
        } catch(PDOException $e)
        {
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
     * Alias PDO::exec()
     *
     * @see   PDO::exec()
     * @throws   \RuntimeException
     */
    public function exec($sql)
    {
        if(false === $this->connection)
        {
            $this->connect();
        }

        try
        {
            $this->profiler[] = array('sql' => $sql);

            return $this->connection->exec($sql);
        } catch(PDOException $e)
        {
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
     * Alias PDO::prepare()
     *
     * @see   PDO::prepare()
     * @throws   \RuntimeException
     */
    public function prepare($sql)
    {
        if(false === $this->connection)
        {
            $this->connect();
        }

        try
        {
            $this->profiler[] = $sql;

            return $this->connection->prepare($sql);
        } catch(PDOException $e)
        {
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
     * Alias PDO::quote()
     *
     * @see   PDO::quote()
     */
    public function quote($string, $parameter_type = PDO::PARAM_STR)
    {
        if(false === $this->connection)
        {
            $this->connect();
        }

        return $this->connection->quote($string, $parameter_type);
    }

    /**
     * Alias PDO::setAttribute()
     *
     * @see   PDO::setAttribute()
     */
    public function setAttribute($attribute, $value)
    {
        if(false === $this->connection)
        {
            $this->connect();
        }

        return $this->connection->setAttribute($attribute, $value);
    }

    /**
     * Alias PDO::rollBack()
     *
     * @see   PDO::rollBack()
     */
    public function rollBack()
    {
        if(false === $this->connection)
        {
            $this->connect();
        }

        return $this->connection->rollBack();
    }

    /**
     * Alias PDO::lastInsertId()
     *
     * @see   PDO::lastInsertId()
     */
    public function lastInsertId($name = NULL)
    {
        if(false === $this->connection)
        {
            $this->connect();
        }

        return $this->connection->lastInsertId($name);
    }

    /**
     * Alias PDO::inTransaction()
     *
     * @see   PDO::inTransaction()
     */
    public function inTransaction()
    {
        if(false === $this->connection)
        {
            $this->connect();
        }

        return $this->connection->inTransaction();
    }

    /**
     * Alias PDO::getAvailableDrivers()
     *
     * @see   PDO::getAvailableDrivers()
     */
    public function getAvailableDrivers()
    {
        if(false === $this->connection)
        {
            $this->connect();
        }

        return $this->connection->getAvailableDrivers();
    }

    /**
     * Alias PDO::getAttribute()
     *
     * @see   PDO::getAttribute()
     */
    public function getAttribute($attribute)
    {
        if(false === $this->connection)
        {
            $this->connect();
        }

        return $this->connection->getAttribute($attribute);
    }

    /**
     * Alias PDO::errorInfo()
     *
     * @see   PDO::errorInfo()
     */
    public function errorInfo()
    {
        if(false === $this->connection)
        {
            $this->connect();
        }

        return $this->connection->errorInfo();
    }

    /**
     * Alias PDO::errorCode()
     *
     * @see   PDO::errorCode()
     */
    public function errorCode()
    {
        if(false === $this->connection)
        {
            $this->connect();
        }

        return $this->connection->errorCode();
    }

    /**
     * Alias PDO::commit()
     *
     * @see   PDO::commit()
     */
    public function commit()
    {
        if(false === $this->connection)
        {
            $this->connect();
        }

        return $this->connection->commit();
    }

    /**
     * Alias PDO::beginTransaction()
     *
     * @see   PDO::beginTransaction()
     */
    public function beginTransaction()
    {
        if(false === $this->connection)
        {
            $this->connect();
        }

        return $this->connection->beginTransaction();
    }

    /**
    * Database::addBind();
    */
    protected function addBind($key, $value)
    {
        $key = trim(preg_replace(array('/>=|<=|!=|>|</', '/[A-Z\s]+/', '/[\s]+/'), array('', '', '_'), $key));
        
        if(isset($this->bind[$key]))
        {
            return $this->addBind($key.chr(mt_rand(97, 122)), $value);
        } else
        {
            $this->bind[$key] = $value;
            
            return $key;
        }
    }

    /**
    * Database::_renderSelect()
    * 
    * @return  string - sql
    */
    protected function _renderSelect()
    {
        if(!empty($this->query[self::SELECT]))
        {
            $sql = self::SQL_SELECT.' ';
            
            if(is_array($this->query[self::SELECT]))
            {
                $sql .= implode(', ', $this->query[self::SELECT]);
            } else
            {
                $sql .= $this->query[self::SELECT];
            }
            
            return $sql.' ';
        }
    }

    /**
    * Database::_renderFrom()
    * 
    * @return   string - sql
    */
    protected function _renderFrom()
    {
        if(!empty($this->query[self::FROM]))
        {
            $sql = self::SQL_FROM;
            $from = array();
            
            foreach($this->query[self::FROM] as $table)
            {
                $from[] = '`'.$table['name'].'`'.(isset($table['alias']) ? ' AS '.$table['alias'] : null);
            }
            
            return $sql.' '.implode(', ', $from).' ';
        }
    }

    /**
    * Database::_renderInsert()
    * 
    * @return   string - sql
    */
    protected function _renderInsert()
    {
        if(!empty($this->query[self::INSERT]))
        {
            $sql = self::SQL_INSERT.' `'.$this->query[self::INSERT]['table'].'`';
            
            $values = array();
            $columns = array();
            
            foreach($this->query[self::INSERT]['bind'] as $column => $value)
            {
                $columns[] = '`'.$column.'`';
                $values[] = ':'.$this->addBind($column, $value);
            }
            
            return $sql.' ('.implode(', ', $columns).') VALUES ('.implode(', ', $values).') ';
        }
    }

    /**
    * Database::_renderUpdate()
    * 
    * @return   string - sql
    */
    protected function _renderUpdate()
    {
        if(!empty($this->query[self::UPDATE]))
        {
            $sql = self::SQL_UPDATE.' `'.$this->query[self::UPDATE]['table'].'` '.self::SQL_SET;
            $part = array();
            
            foreach($this->query[self::UPDATE]['bind'] as $column => $value)
            {
                $part[] = '`'.$column.'` = :'.$this->addBind($column, $value);
            }
            
            return $sql.' '.implode(', ', $part).' ';
        }
    }

    /**
    * Database::_renderDelete()
    * 
    * @return   string - sql
    */
    protected function _renderDelete()
    {
        if(!empty($this->query[self::DELETE]))
        {
            return self::SQL_DELETE.' `'.$this->query[self::DELETE]['table'].'` ';
        }
    }

    /**
    * Database::_renderUpdate()
    * 
    * @return   string - sql
    */
    protected function _renderWhere()
    {
        if(!empty($this->query[self::WHERE]))
        {
            $sql = self::SQL_WHERE.' ';
            
            foreach($this->query[self::WHERE] as $condition)
            {                
                if(false === $condition['bind'])
                {
                    $sql .= $condition['type'].' ('.$condition['column'].') ';
                } else
                {
                    preg_match_all('#([\w\d]+)(?=(?:[\s]+)(>=|<=|!=|>|<|[A-Z\s]+))#i', $condition['column'], $matches);

                    if(isset($matches[2][0]))
                    {
                        $operator = $matches[2][0];
                    } else
                    {
                        $operator = '=';
                    }

                    $sql .= $condition['type'].' (`'.trim(empty($matches[0][0]) ? $condition['column'] : $matches[0][0]).'` '.$operator.' :'.$condition['bind'].') ';
                }
            }
            
            return $sql;
        }
    }

    /**
    * Database::_renderJoin()
    * 
    * @return   string - sql
    */
    protected function _renderJoin()
    {
        if(!empty($this->query[self::JOIN]))
        {
            $sql = '';
            
            foreach($this->query[self::JOIN] as $join)
            {
                $sql .= strtoupper($join['type']).' '.self::SQL_JOIN.' `'.$join['table'].'`'.(null === $join['alias'] ? null : ' '.self::SQL_AS.' '.$join['alias']).' '.self::SQL_ON.' '.$join['on'].' ';
            }
            
            return $sql;
        }
    }

    /**
    * Database::_renderGroup()
    * 
    * @return   string - sql
    */
    protected function _renderGroup()
    {
        if(!empty($this->query[self::GROUP]))
        {
            return self::SQL_GROUP_BY.' '.implode(', ', $this->query[self::GROUP]).' ';
        }
    }

    /**
    * Database::_renderOrder()
    * 
    * @return   string - sql
    */
    protected function _renderOrder()
    {
        if(!empty($this->query[self::ORDER]))
        {
            return self::SQL_ORDER_BY.' '.implode(', ', $this->query[self::ORDER]).' ';
        }
    }

    /**
    * Database::_renderOrder()
    * 
    * @return   string - sql
    */
    protected function _renderLimit()
    {
        if(isset($this->query[self::LIMIT]))
        {
            return self::SQL_LIMIT.' '.$this->query[self::LIMIT].' ';
        }
    }

    /**
    * Database::_renderOrder()
    * 
    * @return   string - sql
    */
    protected function _renderOffset()
    {
        if(isset($this->query[self::OFFSET]))
        {
            return self::SQL_OFFSET.' '.$this->query[self::OFFSET].' ';
        }
    }
}