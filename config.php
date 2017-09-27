<?php
/********************************************************************************

***********************************************************************************/

//Database connection Settings
define ('hostnameorservername','localhost'); //Your server name or host name goes in here
define ('serverusername','root'); //Your database username goes in here
define ('serverpassword',''); //Your database password goes in here
define ('databasenamed','comment_system'); //Your database name goes in here

global $connection;
$connection = @mysql_connect(hostnameorservername,serverusername,serverpassword) or die('Connection could not be made to the SQL Server. Please report this system error at <font color="blue">ynandan55@gmail.com</font>');
@mysql_select_db(databasenamed,$connection) or die('Connection could not be made to the database. Please report this system error at <font color="blue">ynandan55@gmail.com</font>');	
?>
