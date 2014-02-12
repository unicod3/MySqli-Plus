MySqli-Plus
===========
This class makes it easy to write mysql queries which are based on mysqli
To use this class, first include config.php and class.mysqliplus.php into your project page
```php
$myConfig = include "config.php";
require_once('class.mysqliplus.php');
```

Connection Settings
----
Set your database configuration in config.php :
```php 
return array(
        "sqlHost" => "localhost", // Set the IP or hostname of the database server you wish to connect to
        "sqlUser" => "dbUser", // set the database user name you wish to use to connect to the database server
        "sqlDatabase" => "dbName", // Set the name of the database you wish to connect to
        "sqlPassword" => "dbPass", // set the password for the username above
        "sqlPort" => 3306,
        "sqlPrefix" => ""
);
```

Then create a new instance,
```php
$mysqli  = new mySqliPlus($myConfig);
```
Example Table
-------
This will be my example table:
```php 
  $table = "users";
```
  id | first_name | last_name  
  --- | --- | ---
   1  | joe         | fox 
   2  | exm         | example 
   3  | admin       | admin 
    

Select Query
-------
`getRows` : you can fetch data with this method 
```php  
  $rows = $mysqli->getRows($table,'*',"where first_name ='joe'");
  print_r($rows); // contains array of returned rows
```

Row Count
-------
`rowCount` : you can query the number of rows with this method
```php  
  $rowCount = echo  $mysqli->rowCount($table,"where first_name ='joe'");  
  print_r($rowCount); // integer value
```
 
Insert Query
-------
`insert` : you can insert data with this method 
```php  
    $data_arr = array(
        'id' => '',
        'first_name' => "joe's data",
        'last_name' => "test",
        );
         
    $rowID = $mysqli->insert($table, $data_arr); 
    echo $rowID; // it returns inserted id  
```

Update Query
-------
`update` : you can update data with this method 
```php  
    $data_arr = array(
        'first_name' => "joe's Updated Data",
        'last_name' => "testUpdated",
        );
    $mysqli->update($table, $data_arr,'where id=3');
```

Delete Query
-------
`delete` : you can delete data with this method 
```php  
    $mysqli->delete($table,'id=2');
```
 
 
