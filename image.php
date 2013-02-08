<?php
require("includes/constants.php");
$connection = mysql_connect(DB_SERVER,DB_USER,DB_PASS);
$db_select = mysql_select_db(DB_NAME,$connection);
$result = mysql_query(sprintf("SELECT * from ".DB_PREFIX."image WHERE ImageId = %d", $_GET['id']));
$row = mysql_fetch_array($result);
mysql_close($connection);
header(sprintf("Content-type: %s", $row['FileType']));
print $row['Image'];
?>
