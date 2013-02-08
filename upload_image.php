<?php require_once("includes/session.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php confirm_logged_in(); ?>

<?php
require("includes/constants.php");

//  Create a database connection
$connection = mysql_connect(DB_SERVER,DB_USER,DB_PASS);
if (!$connection) {
	die("Database connection failed: " . mysql_error());
}
$db_select = mysql_select_db(DB_NAME,$connection);
if (!$db_select) {
	die("Database selection failed: " . mysql_error());
}



      if ($_POST['Submit']) {
        if ($_POST['MAX_FILE_SIZE'] >= $_FILES['file']['size']) {
          //print_r($_FILES);
          $type = $_POST['type'];
          $id = $_POST['id'];
          $photo = addslashes(fread(fopen($_FILES['file']['tmp_name'], "r"),
$_FILES['file']['size']));
           $query = sprintf("INSERT INTO ".DB_PREFIX."image(Image, FileType) VALUES
('%s', '%s')", $photo, $_FILES['file']['type']);
           if (mysql_query($query, $connection)) {
            $messages[] = "Your files is successfully store in database"; 
           } else {
            $messages[]= mysql_error();
           }
          } else {
           $messages[]="The file is bigger than the allowed size please resize";
          }
       
      $imgid = mysql_insert_id();
      $query = "UPDATE ".DB_PREFIX."image SET 
							type = '{$type}',
							id = {$id}
						WHERE ImageId = {$imgid}";
     if (mysql_query($query, $connection)) {
            $messages[] = "Your file relations are stored in database"; 
           } else {
            $messages[]= mysql_error();
           }
           
     mysql_close($connection);
     if ($type == "page") {
     	redirect_to("edit_page.php?page={$id}");
     } elseif ($type == "subpage") {
     	redirect_to("edit_page.php?subpage={$id}");
     }
        }
      ?>
