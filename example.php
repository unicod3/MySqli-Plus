<?php   
/**  
* @table : users   
*  | id | first_name | last_name | 
*  ------------------------------- 
*    1  | joe         | fox 
*    2  | exm          | example 
**/ 
require_once("class.mySqliPlus.php");  
    $mysqli  = new mySqliPlus();  
  
          
  
     $table = 'users'; 
     
    ////row count  
    echo  $mysqli->rowCount($table,"where first_name ='joe'");  
     
     
    ////get rows    
    echo '<pre>'; 
    print_r($mysqli->getRows($table,'*',"where first_name ='joe'"));  
    echo '</pre>'; 
     
    ////insert data 
    $data_arr = array(  
        'id' => '', 
        'first_name' => 'joe', 
        'last_name' => 'test',  
        );  
         
    $row_id = $mysqli->insert($table, $data_arr); 
    echo $row_id; // it returns insert id  
     
     
    ////update data 
    $data_arr = array(  
        'id' => '', 
        'first_name' => 'joe', 
        'last_name' => 'test',  
        );  
    $mysqli->update($table, $data_arr,'where id=3'); 
     
     
     
    //delete data 
    echo $mysqli->delete($table,'id=2'); 
     
?>
