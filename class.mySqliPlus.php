<?php 

class config { 
     
    public static $dbServer = "127.0.0.1"; // Set the IP or hostname of the database server you wish to connect to 
    public static $dbName = "root"; // Set the name of the database you wish to connect to
    public static $dbUser = "dbUserName"; // set the database user name you wish to use to connect to the database server
    public static $dbPassword = "dbPassword"; // set the password for the username above
    public static $dbPort = 3306; 
    public static $dbPrefix = "";
} 

/** 
 * @name mySqliPlus 
 * 
 * @category  Database Access
 * @author Sinan Ulker <sinanulker386@gmail.com> 
 * @copyright Copyright (c) 2013
 * @license GNU/GPL  
 */  
 
class mySqliPlus { 
     
    // leave blank if used for multiple users and call setUser method 
    private $sqlUser = NULL; 
     
    // leave blank if used for multiple users and call setPassword method 
    private $sqlPassword = NULL; 
     
    // set this to the database name you wish to use. If this class is used to access a number of Databases 
    // leave blank and call the select method to select the desired database 
    private $sqlDatabase = NULL; 
     
    // set this to the database server address. If you are using this class to connect to differant server 
    // leave blank and call the setHost method 
    private $sqlHost = NULL; 

    // set the database port
    private $sqlPort = 3306;

    // Set this to the prefix of your tables if you set one while installing. 
    // default = "" 
    public $tablePrefix = NULL; 
    private $result; // Query result 
    private $querycount; // Total queries executed 
    private $linkid;  
     
    /////////////////////////////////////////END CONFIG OPTIONS///////////////////////////////////////////////////// 
     
    function __construct() {  
        $this->loadDefaults ();  
        $this->connect ();
        $this->select ( $this->sqlDatabase );  
    } 
     
    /* 
     * method to load the object with the defaut settings 
     */ 
     
    private function loadDefaults() { 
        $this->sqlUser         = config::$dbUser; 
        $this->sqlPassword     = config::$dbPassword; 
        $this->sqlHost         = config::$dbServer;
        $this->sqlDatabase     = config::$dbName;
        $this->sqlPort         = config::$dbPort;
        $this->tablePrefix     = config::$dbPrefix;
    } 
     
    public function getResult() {
        return $this->result;
    } 
     
    /** 
     * method to return the prefix for the sql tables 
     * 
     * @return = string $this->tablePrefix 
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
     * @param    string    $sqlHost 
     * @param    string    $sqlUser 
     * @param    string    $sqlPassword 
     **/ 
     
    function connect() {
        try { 
            $this->linkid = mysqli_connect ( $this->sqlHost, $this->sqlUser, $this->sqlPassword, $this->sqlDatabase, $this->sqlPort );
            if (! $this->linkid) { 
                die ( 'Connect Error (' . mysqli_connect_errno () . ') ' . mysqli_connect_error () ); 
            } 
            mysqli_query($this->linkid, "SET NAMES 'utf8'");  
        } catch ( Exception $e ) { 
            die ( $e->getMessage () ); 
        } 
    } 
     
    /** 
     * method to select the database to use 
     * @param string $sqlDatabase 
     */ 
     
    function select($sqlDatabase) { 
        try { 
            if (! @mysqli_select_db ($this->linkid, $sqlDatabase  )) { 
                throw new Exception ( "The Selected Database Can Not Be Found On the Database Server. $sqlDatabase (E2)" ); 
            } 
        } catch ( Exception $e ) { 
            die ( $e->getMessage () ); 
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
     * @return array 
     **/ 
     
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
     * method to clean data 
     * @return    string 
     **/ 
    function cleanData($data){ 
        if(empty($data))return false;  
        return    mysqli_real_escape_string($this->linkid, $data); 
    }  
    /* 
    * method to add something to a table 
    * @param string $tableName  
    * @param array $data_arr     = array('col'=>'val'); 
    * if it success it returns row id otherwise null  
    */      
    function insert($table , &$data_arr){  
           $sql  = "INSERT INTO ".$this->tablePrefix.$table;   
           $sql .= " (`".implode("`, `", array_keys($data_arr))."`)";  // implode keys of $array.  
           $sql .= " VALUES ('".implode("', '", $data_arr)."') ";  // implode values of $array. 
             
            $this->query($sql);
            return ($this->result == true) ?  mysqli_insert_id($this->linkid) : false; 
        } 
     
    /* 
    * method to update colons in a table  
    * @param string $tableName  
    * @param array $data_arr     = array('col'=>'val'); 
    * @param string $where_str  
    * if it success it returns true otherwise false 
    */      
    function update($table, &$data_arr , $where_str){ 
            $sql    = "UPDATE ".$this->tablePrefix.$table." SET "; 
             
            $last_item = end($data_arr); 
            $last_item = each($data_arr); 
            foreach($data_arr as $k => $v){ 
                $sql .= $k." = '".$v."'";     
                if(!($v == $last_item['value'] && $k == $last_item['key'])){ 
                    $sql .=", "; 
                } 
            }  
            $sql    .= " ".$where_str;
            $this->query($sql);
            return $this->result; 
        }  
     
    /* 
    * method to delete records 
    * @param string $tableName   
    * @param string $where_str  
    * if it success it returns true otherwise false 
    */      
     
    function delete($table, $where_str){ 
            if(empty($where_str))return false; 
            $sql        = "DELETE FROM ".$this->tablePrefix.$table." WHERE ".$where_str;      
            $this->query($sql);  
            return $this->result; 
        } 
         
         
         
    /* 
    * method to get row count 
    * @param string $tableName     
    * @param string $where_str  
    * if it success it returns true otherwise false 
    */          
    function rowCount($table,$where_str = ''){  
            $this->query("SELECT * FROM ".$this->tablePrefix.$table." ".$where_str);
            return $this->numRows(); 
        } 

    /* 
    * method to get rows from database 
    * @param string $tableName   
    * @param string/array $col_arr = array('col1','col2'); or *   
    * @param string $where_str  
    * if it success it returns true otherwise false 
    */     
    function getRows($table,$col_arr = '*', $where_str = ''){ 
            $cols     = !is_array($col_arr) ? $col_arr : implode(',',$col_arr);  
            $this->query("SELECT ".$cols." FROM ".$this->tablePrefix.$table." ".$where_str);
            return  ($this->numRows() > 0) ? $this->fetchArray() : false; 
        } 
         
} 
?> 
