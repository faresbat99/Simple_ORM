<?php
require 'MysqlAdapter.php';
require 'Database_config.php';

class User extends MysqlAdapter
{
    //set table name 
    private $_table = 'users';
    public function __construct()
    {
        //add from Database_config.php
        global $config;

        //call the parent constructor 
        parent::__construct($config);
    }
    /** 
     * list All users
     * @return array Returns every user row as array of assoc array
     */
    public function getUsers()
    {
        $this->select($this->_table);
        return $this->fetchAll();
    }
    /**
     * show one user
     * @param int $user_id
     * @return array Returns a user row as assoc array 
     */
    public function getUser($user_id)
    {
        $this->select($this->_table, 'id=' . $user_id);
        return $this->fetch();
    }
    /**
     * Add New User
     *
     * @param array $user_data Assoc array containing column and value 
     * @return int Returns the id of the user inserted
     */
    public function addUser($user_data)
    {
        return $this->insert($this->_table, $user_data);
    }
    /**
     * Update existing user
     *
     * @param array $user_data Assoc array containing column and value 
     * @param int $user_id 
     * @return int Number of affected row
     */
    public function updateUser($user_data, $user_id)
    {
        return $this->update($this->_table, $user_data, 'id= ' . $user_id);
    }
    /**
     * Delete user
     *
     * @param int $user_id
     * @return int Number of Affected rows
     */
    public function deleteUser($user_id)
    {
        return $this->delete($this->_table, 'id= ' . $user_id);
    }

    public function searchUsers($keyword)
    {
        $this->select($this->_table,"name LIKE '%$keyword%' OR email LIKE '%$keyword%'");
        return $this->fetchAll();
    }
/**
 * login users
 * 
 * @param string $email
 * @param string $password
 * @return array has one row 
 */
    public function LoginUsers($email,$password)
    {
        $this->select($this->_table,"email = '$email' and password = '$password'");  // don't forget '' between variables 😒
        return $this->fetch();
    }
}
