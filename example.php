<?php   
/**  
* @table : users   
*  | id | first_name | last_name | 
*  ------------------------------- 
*    1  | joe         | fox 
*    2  | exm         | example 
*    3  | admin       | admin 
**/
$myConfig = include $config;

require_once("class.mySqliPlus.php");  
    $mysqli  = new mySqliPlus($myConfig);
  
          
  
     $table = 'users'; 
     
    //row count
    echo  $mysqli->rowCount($table,"where first_name ='joe'");  
     
     
    //get rows
    echo '<pre>'; 
    print_r($mysqli->getRows($table,'*',"where first_name ='joe'"));  
    echo '</pre>';

    //insert data
    $data_arr = array(
        'id' => '',
        'first_name' => "joe's data",
        'last_name' => "test",
        );

    $rowID = $mysqli->insert($table, $data_arr);
    echo $rowID; // it returns inserted id
/*

    //update data
    $data_arr = array(
        'first_name' => "joe's Updated Data",
        'last_name' => "testUpdated",
        );
    $mysqli->update($table, $data_arr,'where id=1');


    //delete data
    echo $mysqli->delete($table,'id=2');

*/
