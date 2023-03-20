<?php
class MysqlAdapter
{
    protected $_config = array(); // that pass from Database_config
    protected $_link; // will store the {connection}
    protected $_result; // the result from the select statement

    /**
     * Constrictor
     * the start will be the connection that receive array have 4 element   
     */
    public function __construct($config)
    {
        // exception
        if (count($config) !== 4) {
            throw new InvalidArgumentException('invalid number of connection parameters');
        }
        // connection is true 
        $this->_config = $config;
    }
    /**
     * connection to MySQL
     */
    public function connect()
    {
        // use single tone design pattern >> at least one instance of connection is there 
        //connect only once 
        if ($this->_link === null) {
            // Assign variables by list that came from $_config 
            list($host, $user, $password, $database) = $this->_config;
            //if there is error 
            if (!$this->_link = @mysqli_connect($host, $user, $password, $database)) {
                throw new RuntimeException("error connecting to the server" . mysqli_connect_error());
            }
            unset($host, $user, $password, $database);
        }
        // if there is connection >> the connection will be open , مفيش داعي افتح اتصال تاني 
        return $this->_link;
    }
    /**
     * Execute the specified query
     */
    public function query($query)
    {
        if (!is_string($query) && empty($query)) {
            throw new InvalidArgumentException('the specified query is not valid', 1);
        }
        //lazy connection to MySQL => connect when i use the database only
        $this->connect();
        if (!$this->_result = mysqli_query($this->_link, $query)) {
            throw new RuntimeException('error executing the specified query' . mysqli_error($this->_link));
        }
        return $this->_result;
    }
    /**
     * perform a select statement
     * notice it doesn't return the result set that's why we need to do fetch and fetchAll function⏬
     * it used in search statement
     *
     * @param string $table
     * @param string $where
     * @param string $fields
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return int Returns every user row as array of assoc array
     */
    public function select($table, $where = '', $fields = '*', $order = '', $limit = null, $offset = null)
    {
        $query = 'SELECT ' . $fields . ' FROM ' . $table
            . (($where) ? ' WHERE ' . $where : '')
            . (($limit) ? ' LIMIT ' . $limit : '')
            . (($offset && $limit) ? ' OFFSET ' . $offset : '')
            . (($order) ? ' ORDER BY ' . $order : '');
        $this->query($query);
        return $this->countRows();
    }
    /**
     * Escaping the specified value
     * bec of sql injection 
     */
    public function quoteValue($value)
    {
        $this->connect();
        if ($value === null) {
            $value = 'NULL';
        } elseif (!is_numeric($value)) {
            $value = "'" . mysqli_real_escape_string($this->_link, $value) . "'";
        }
        return $value;
    }

    /**
     * perform a INSERT statement
     *
     * @param  $table
     * @param array $data
     * type hinting that the data must be array
     * keys => cols name
     * value => the data
     * 
     */
    public function insert($table, array $data)
    {
        $fields = implode(',', array_keys($data));
        $values = implode(',', array_map(array($this, 'quoteValue'), array_values($data)));
        $query = 'INSERT INTO ' . $table . ' (' . $fields . ')' . 'VALUES (' . $values . ')';
        $this->query($query); //execute
        return $this->getInsertId();
    }
    /**
     * perform a UPDATE statement
     *
     * @param $table
     * @param array $data
     * @param string $where
     * @return int Number of affected row
     */
    public function update($table, array $data, $where = '')
    {
        $set = [];
        foreach ($data as $field => $value) {
            $set[] = $field . '=' . $this->quoteValue($value);
        }
        $set = implode(',', $set);
        $query = 'UPDATE ' . $table . ' SET ' . $set
            . (($where) ? ' WHERE ' . $where : '');
        $this->query($query);
        return $this->getAffectedRows();
    }
    /**
     * perform a DELETE statement
     *
     * @param $table
     * @param string $where
     * @return int Number of Affected rows
     */
    public function delete($table, $where = '')
    {
        $query = 'DELETE FROM ' . $table
            . (($where) ? ' WHERE ' . $where : '');
        $this->query($query);
        return $this->getAffectedRows();
    }

    /**
     * fetch all row from the current result set (as an associative array)
     */
    public function fetch()
    {
        if ($this->_result !== null) {
            if (($row = mysqli_fetch_array($this->_result, MYSQLI_ASSOC)) === false) {
                $this->freeResult();
            }
            return $row;
        }
        return false;
    }
    /**
     * fetch single row from the current result set (as an associative array)
     */
    public function fetchAll()
    {
        if ($this->_result !== null) {
            if (($all = mysqli_fetch_all($this->_result, MYSQLI_ASSOC)) === false) {
                $this->freeResult();
            }
            return $all;
        }
        return false;
    }
    /**
     * Get the insertion ID
     */
    public function getInsertId()
    {
        return $this->_link !== null ? mysqli_insert_id($this->_link) : null;
    }
    /**
     * Get the number of rows returned by the current result set
     */
    public function countRows()
    {
        return $this->_result !== null ? mysqli_num_rows($this->_result) : 0;
    }
    /**
     * Get the number of affected rows
     */
    public function getAffectedRows()
    {
        return $this->_link !== null ? mysqli_affected_rows($this->_link) : 0;
    }
    /**
     * free up the current result set
     */
    public function freeResult()
    {
        if ($this->_result === null) {
            return  false;
        }
        mysqli_free_result($this->_result);
        return true;
    }
    /**
     * close explicitly the DB Connection
     *
     * @return bool
     */
    public function disconnect()
    {
        if ($this->_link === null) {
            return false;
        }
        mysqli_close($this->_link);
        return true;
    }
    /**
     * close automatically the database connection when the instance of the class is destroyed 
     */
    public function __destruct()
    {
        $this->disconnect();
    }
}
