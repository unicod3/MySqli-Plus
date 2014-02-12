<?php
/** 
 * @name mySqliPlus 
 * 
 * @category  Database Access
 * @author Sinan Ülker <www.sinanulker.com>
 * @copyright Copyright (c) 2013
 * @license GNU/GPL  
 */  
 
class mySqliPlus {
    /**
     * @var array
     */
    private $_config;

    private $result; // Query result

    private $querycount; //Total queries executed

    private $linkid;  

    private $tablePrefix = "";

    function __construct($config) {
        $this->_config = $config;
        $this->connect();
    }

    /**
     * method to get results
     *
     * @return string $this->result
     */
    public function getResult() {
        return $this->result;
    } 
     
    /** 
     * method to return the prefix for the sql tables 
     * 
     * @return string $this->tablePrefix
     */
    public function getTablePrefix() {
        return $this->tablePrefix;
    } 
     
    /** 
     * function to return a string from within another string 
     * found between $beginning and $ending 
     * 
     * @param string $source 
     * @param string $beginning 
     * @param string $ending 
     * @param string $init_pos
     *
     * @return string
     */
    function getMiddle($source, $beginning, $ending, $init_pos) {
        $beginning_pos = strpos ( $source, $beginning, $init_pos );  
        $middle_pos = $beginning_pos + strlen ( $beginning );  
        $ending_pos = strpos ( $source, $ending, $beginning_pos + 1 );  
        $middle = substr ( $source, $middle_pos, $ending_pos - $middle_pos );  
        return $middle;
    } 
     
    /** 
     * method to connect to the MySQL database server. 
     *
     **/
    function connect() {
        try {
            $this->linkid = mysqli_connect (
                 $this->_config['sqlHost']
                ,$this->_config['sqlUser']
                ,$this->_config['sqlPassword']
                ,$this->_config['sqlDatabase']
                ,$this->_config['sqlPort']
            );
            $this->tablePrefix = $this->_config['sqlPrefix'];
            if (!$this->linkid) {
                die ( 'Connect Error (' . mysqli_connect_errno () . ') ' . mysqli_connect_error () ); 
            } 
            mysqli_query($this->linkid, "SET NAMES 'utf8'");

            $this->selectDb();
        } catch ( Exception $e ) { 
            die ( $e->getMessage () ); 
        } 
    } 
     
    /** 
     * method to select the database to use 
     *
     */
    function selectDb() {
        try { 
            if (!mysqli_select_db ($this->linkid, $this->_config['sqlDatabase']  )) {
                throw new Exception ( "The Selected Database Can Not Be Found On the Database Server.". $this->_config['sqlDatabase'] ." (E2)" );
            } 
        } catch ( Exception $e ) { 
            die ( $e->getMessage());
        } 
    } 
     
    /** 
     * method to query sql database 
     * take mysql query string 
     * returns false if no results or NULL result is returned by query 
     * if query action is not expected to return results eg delete 
     * returns false on sucess else returns result set 
     * 
     * NOTE: If you requier the the actual result set call one of the fetch methods 
     * 
     * @param string $query 
     * @return boolean true or false
     */
    function query($query) { 
        // ensure clean results 
        unset ( $this->result ); 
        // make query 
        $this->result = mysqli_query ( $this->linkid, $query ); 
        if (! $this->result) { 
            echo "<br>Query failed: $query"; 
            return FALSE; 
        } else { 
            return true; 
        } 
    }

    /**
     * method to make data safe for database
     * @param string
     * @return string
     */
    function cleanData($value){
        if(empty($value))
            return false;
        return mysqli_real_escape_string($this->linkid, $value);
    }
     
    /** 
     * method to return the number of rows affected by the 
     * last query exicuted 
     * @return int 
     */
    function affectedRows() {  
        $count = mysqli_affected_rows ( $this->linkid ); 
        return $count;
    } 
     
    /** 
     * method to return the number of rows in the result set 
     * returned by the last query 
     */
    function numRows() {  
        $count = @mysqli_num_rows ( $this->result );  
        return $count;  
    } 
     
    /** 
     * method to return the result row as an object 
     * @return    object 
     */
    function fetchObject() {
        $rw = array();
        while($row = mysqli_fetch_object ( $this->result ))
                $rw[] = $row;
        return $rw;
    }  
     
    /** 
     * method to return the result row as an indexed array 
     * @return array 
     */
    function fetchRows() {
        $rw = array();
        while($row = @mysqli_fetch_row ( $this->result )) 
            $rw[] = $row;
        return $rw;  
    } 
     
    /**
     * method to return the result row as an associative array.
     *
     * @var int
     * @return array
     */
    function fetchArray($assoc = MYSQL_ASSOC) {
        $rw = array();
        while($row = @mysqli_fetch_array ( $this->result,$assoc)) 
        $rw[] = $row;  
        return $rw;  
    } 
     
    /** 
     * method to return total number queries executed during 
     * the lifetime of this object. 
     * 
     * @return int
     */
    function numQueries() {  
        return $this->querycount;  
    }

    /**
     * set the results
     * @param $resultSet
     */
    function setResult($resultSet) {  
        $this->result = $resultSet;  
    } 
     
    /** 
     * method to return the number of fields in a result set 
     * @return int 
     **/
    function numberFields() {  
        return @mysqli_num_fields ( $this->result ); 
    }

    /**
    * method to add something to a table 
    * @param string
    * @param array
    *
    * @return boolean
    */      
    function insert($table , &$data_arr){
           $safe_data = array_map(array($this, 'cleanData'), $data_arr);
           $sql  = "INSERT INTO ".$this->tablePrefix.$table;   
           $sql .= " (`".implode("`, `", array_keys($data_arr))."`)";  // implode keys of $array.  
           $sql .= " VALUES ('".implode("', '", $safe_data)."') ";  // implode values of $array.
             
            $this->query($sql);
            return ($this->result == true) ?  mysqli_insert_id($this->linkid) : false; 
        } 
     
    /**
    * method to update colons in a table  
    * @param string
    * @param array
    * @param string
    *
    * @return boolean
    */      
    function update($table, &$data_arr , $where_str){ 
            $sql    = "UPDATE ".$this->tablePrefix.$table." SET "; 
             
            $last_item = end($data_arr); 
            $last_item = each($data_arr); 
            foreach($data_arr as $k => $v){ 
                $sql .= $k." = '".$this->cleanData($v)."'";
                if(!($v == $last_item['value'] && $k == $last_item['key'])){ 
                    $sql .=", "; 
                } 
            }  
            $sql    .= " ".$where_str;
            $this->query($sql);
            return $this->result; 
        }  
     
    /**
    * method to delete records 
    * @param string
    * @param string
    *
    * @return boolean
    */
    function delete($table, $where_str){ 
            if(empty($where_str))return false; 
            $sql        = "DELETE FROM ".$this->tablePrefix.$table." WHERE ".$where_str;      
            $this->query($sql);  
            return $this->result; 
        }
         
    /**
    * method to get row count 
    * @param string
    * @param string
    * @return int
    */          
    function rowCount($table,$where_str = ''){  
            $this->query("SELECT * FROM ".$this->tablePrefix.$table." ".$where_str);
            return $this->numRows(); 
        } 

    /**
    * method to get rows from database 
    * @param string
    * @param string/array
    * @param string
    * @return int/boolean
    */     
    function getRows($table,$col_arr = '*', $where_str = ''){ 
            $cols     = !is_array($col_arr) ? $col_arr : implode(',',$col_arr);  
            $this->query("SELECT ".$cols." FROM ".$this->tablePrefix.$table." ".$where_str);
            return  ($this->numRows() > 0) ? $this->fetchArray() : false; 
        }
}