<?php
/***********---   PSI CMS    ---***********
**                                       **
**  Visit us at http://psi.tarakan.eu    **
**  This software is released under the  **
**           terms of GNU GPL            **
**        Developed & maintained by      **
**    Svilen Vassilev (psi@tarakan.eu)   **
**                                       **
******************************************/
?>
<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php
	include_once("includes/form_functions.php");
	
if (!empty($_GET['edit'])) {
if (isset($_POST['submit'])) {

$id = $_GET['edit'];
$name = $_POST['name'];

if ($name == "site_lang") {
	$value1 = $_POST['value1'];
	$query = "UPDATE ".DB_PREFIX."variables SET
					value1 = '{$value1}' 
			WHERE id = {$id};";
	if ($result=mysql_query($query, $connection)) {
		redirect_to("manage_settings.php?msg=editdone");
	} else {
		redirect_to("manage_settings.php?msg=editfail");
	}
} // end of site lang

if ($name == "site_name") {
	$value1 = $_POST['value1'];
	$value2 = $_POST['value2'];
	$query = "UPDATE ".DB_PREFIX."variables SET
					value1 = '{$value1}', 
					value2 = '{$value2}' 
			WHERE id = {$id};";
	if ($result=mysql_query($query, $connection)) {
		redirect_to("manage_settings.php?msg=editdone");
	} else {
		redirect_to("manage_settings.php?msg=editfail");
	}
} // end of site name

if ($name == "site_descr") {
	$value1 = $_POST['value1'];
	$query = "UPDATE ".DB_PREFIX."variables SET
					value1 = '{$value1}' 
				WHERE id = {$id};";
	if ($result=mysql_query($query, $connection)) {
		redirect_to("manage_settings.php?msg=editdone");
	} else {
		redirect_to("manage_settings.php?msg=editfail");
	}
} // end of site descr

if ($name == "site_keywords") {
	$value1 = $_POST['value1'];
	$query = "UPDATE ".DB_PREFIX."variables SET
					value1 = '{$value1}' 
				WHERE id = {$id};";
	if ($result=mysql_query($query, $connection)) {
		redirect_to("manage_settings.php?msg=editdone");
	} else {
		redirect_to("manage_settings.php?msg=editfail");
	}
} // end of site keywords

} // end of if - submit
} // end of if - edit
?>
<?php include("includes/header.php"); ?>
<table id="structure">
	<tr>
		<td id="navigation">
			<?php include("includes/lang_select.php");	?>
		
			<a href="staff.php"><?php echo $l_return_to_menu; ?></a><br />
			
		</td>
		<td id="page">
<?php	if (!empty($_GET['edit'])) {
		
		$id = $_GET['edit'];
		$query = "SELECT * FROM ".DB_PREFIX."variables ";
		$query .= "WHERE id=".$id;
		
		$result = mysql_query($query, $connection);
		$variable = mysql_fetch_array($result);
		$name = $variable['name'];
		$value1 = $variable['value1'];
		$value2 = $variable['value2'];
		$value3 = $variable['value3'];
?>
		<?php
		if ($name == "site_lang") {
		?>
		<h2><?php echo $l_global_site_lang; ?></h2>
		<form action="manage_settings.php?edit=<?php echo $id; ?>" method="post">
		<select name="value1">
		<option value="bg"><?php echo $l_global_lang_bg; ?></option>
		<option value="us"><?php echo $l_global_lang_us; ?></option>
		</select>
		<br /><br />
		<input type="hidden" name="name" value="<?php echo $name; ?>">
		
		<input type="submit" name="submit" value="<?php echo $l_edit_mod; ?>" />
		&nbsp;&nbsp;
		<a href="manage_settings.php"><?php echo $l_cancel; ?></a>
		</form>

<?php     } // end of site lang
?>
					
		<?php
		if ($name == "site_name") {
		?>
		<h2><?php echo $$l_global_site_name; ?></h2>
		<form action="manage_settings.php?edit=<?php echo $id; ?>" method="post">
		<?php echo $l_sitename1_descr; ?>
		<p><input type="text" size="40" name="value1" value="<?php echo $value1; ?>" id="value1" /></p>
		<?php echo $l_sitename2_descr; ?>
		<p><input type="text" size="40" name="value2" value="<?php echo $value2; ?>" id="value2" /></p>
		<br />
		<input type="hidden" name="name" value="<?php echo $name; ?>">
		
		<input type="submit" name="submit" value="<?php echo $l_edit_mod; ?>" />
		&nbsp;&nbsp;
		<a href="manage_settings.php"><?php echo $l_cancel; ?></a>
		</form>

<?php     } // end of site name
?>		
		
		<?php
		if ($name == "site_descr") {
		?>
		<h2><?php echo $l_global_site_descr; ?></h2>
		<form action="manage_settings.php?edit=<?php echo $id; ?>" method="post">
		<p><textarea name="value1" rows="10" cols="60" class="mceNoEditor"><?php echo $value1; ?></textarea></p>
		<br />
		<input type="hidden" name="name" value="<?php echo $name; ?>">
		
		<input type="submit" name="submit" value="<?php echo $l_edit_mod; ?>" />
		&nbsp;&nbsp;
		<a href="manage_settings.php"><?php echo $l_cancel; ?></a>
		</form>

<?php     } // end of site descr
?>		

		<?php
		if ($name == "site_keywords") {
		?>
		<h2><?php echo $l_global_site_keywords; ?></h2>
		<form action="manage_settings.php?edit=<?php echo $id; ?>" method="post">
		<p><textarea name="value1" rows="10" cols="60" class="mceNoEditor"><?php echo $value1; ?></textarea></p>
		<br />
		<input type="hidden" name="name" value="<?php echo $name; ?>">
		
		<input type="submit" name="submit" value="<?php echo $l_edit_mod; ?>" />
		&nbsp;&nbsp;
		<a href="manage_settings.php"><?php echo $l_cancel; ?></a>
		</form>

<?php     } // end of site keywords
?>	

<?php	} // end of EDIT zone 

 else { ?>
		<h2><?php echo $l_gobal_settings; ?></h2>
		
		<?php
		$query = "SELECT * FROM ".DB_PREFIX."variables ";
		$query .= "WHERE can_edit=1";
		$result = mysql_query($query, $connection); ?>
		<table width="600px">
		
<?php	while ($variable = mysql_fetch_array($result)) {
			echo "<tr>";
			echo "<td width=\"150px\">";
			if ($variable['name']=="site_lang") {
				echo $l_global_site_lang;
			} elseif ($variable['name']=="site_name") {
				echo $l_global_site_name;
			} elseif ($variable['name']=="site_descr") {
				echo $l_global_site_descr;
			} elseif ($variable['name']=="site_keywords") {
				echo $l_global_site_keywords;
			}
			echo "</td>";
			
			echo "<td width=\"300px\">";
			if ($variable['value1']=="bg") {
				echo $l_global_lang_bg;
			} elseif ($variable['value1']=="us") {
				echo $l_global_lang_us;
			} elseif ($variable['name']=="site_name") {
				echo $variable['value1']." - ".$variable['value2'];
			} elseif ($variable['name']=="site_descr") {
				echo $variable['value1'];
			} elseif ($variable['name']=="site_keywords") {
				echo $variable['value1'];
			}
			echo "</td>";
			
			echo "<td>";
			echo "<a href=\"manage_settings.php?edit=".$variable['id']."\">".$l_edit_mod."</a>";
			echo "</td>";
			
			echo "</tr>";
		} // end of while loop
		
		?>
		</table>
<?php 	}	// end of non-EDIT else zone
?>		
		</td>
	</tr>
</table>
<?php include("includes/footer.php"); ?>

