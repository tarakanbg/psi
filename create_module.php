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

if (isset($_POST['submit'])) {

	$name = mysql_prep($_POST['name']);
	$type = $_POST['type'];
	$location = $_POST['location'];
	$position = $_POST['position'];
	$scope = $_POST['scope'];
	$separator = htmlentities($_POST['separator']);
	$content = mysql_prep($_POST['content']);
	$can_delete = 1;
	$active = 1;
	
	$query = "INSERT INTO ".DB_PREFIX."modules (
				name, type, location, position, active, scope, content, can_delete
			) VALUES (
				'{$name}', '{$type}', '{$location}', {$position}, {$active}, '{$scope}', '{$content}', {$can_delete}
			)";
	if ($result = mysql_query($query, $connection)) {
		$id = mysql_insert_id ();
		
		$query = "UPDATE ".DB_PREFIX."modules SET ";
			if ($separator == "n") {
				$query .=	"	`separator` = '<br />'  ";
			}
			elseif ($separator == "s") {
				$query .=	"	`separator` = '&nbsp;'  ";
			}
			elseif ($separator == "ss") {
				$query .=	"	`separator` = '&nbsp;&nbsp;'  ";
			}
			elseif ($separator == "sss") {
				$query .=	"	`separator` = '&nbsp;&nbsp;&nbsp;'  ";
			}
			
			$query .= "	WHERE id = {$id};";
			
			if ($result2 = mysql_query($query, $connection)) {
				redirect_to("manage_modules.php?msg=adddone");
			} else {
				redirect_to("manage_modules.php?msg=addfail");
			}
		
	} else {
		redirect_to("staff.php");
	}
	
} // end of isset POST submit	
?>

<?php include("includes/header.php"); ?>
<table id="structure">
	<tr>
		<td id="navigation">
			<?php include("includes/lang_select.php");	?>
		
			<a href="staff.php"><?php echo $l_return_to_menu; ?></a><br />
			<br />
			<a href="help_modules.php"><?php echo $l_help_modules; ?></a><br />
		</td>
		<td id="page">
		<h2><?php echo $l_adding_html_module; ?></h2>
		
		<form action="create_module.php" method="post">
		
		<p><?php echo $l_mod_name.": "; ?><input type="text" name="name" value="" id="name" /></p>
			
		<p><?php echo $l_mod_location.": "; ?><select name="location">
			<?php
			$zones = get_zones();
			foreach ($zones as $zone) {
				echo "<option value=\"{$zone}\"";
				echo ">".$zone."</option>";
			}
			?>
			</select>&nbsp;&nbsp;
				
			<?php echo $l_mod_position.": "; ?><select name="position">
			<?php
			$positions = get_positions();
			foreach ($positions as $positionf) {
				echo "<option value=\"{$positionf}\"";
				echo ">".$positionf."</option>";
			}
			?>
			</select>&nbsp;&nbsp;&nbsp;
				
			<?php echo $l_mod_scope.": "; ?><select name="scope">
			<?php
			$scopes = get_scopes();
			foreach ($scopes as $scopef) {
				echo "<option value=\"{$scopef}\"";
				if ($scopef == "global") {
					$scopef_name = $l_mod_global_scope;
				} elseif ($scopef == "title") {
					$scopef_name = $l_mod_title_scope;
				} elseif ($scopef == "inner") {
					$scopef_name = $l_mod_inner_scope;
				}
				echo ">".$scopef_name."</option>";
			}
			?>
			</select></p>
				
			<p><?php echo $l_mod_separator.": "; ?><select name="separator">
			<?php
			$separators = get_separators();
			foreach ($separators as $sepname => $separatorf) {
				echo "<option value=\"{$sepname}\"";
				if ($sepname == "n") {
					$sep_name = $l_new_line;
				} elseif ($sepname == "s") {
					$sep_name = $l_1_space;
				} elseif ($sepname == "ss") {
					$sep_name = $l_2_space;
				} elseif ($sepname == "sss") {
					$sep_name = $l_3_space;
				}
				echo ">".$sep_name."</option>";
			}
			?>
			</select></p>
				
			<p><?php echo $l_content; ?><br />
			<textarea name="content" rows="10" cols="60" class="mceNoEditor"></textarea>
			</p>			
				
			<input type="hidden" name="type" value="html">
						
			<input type="submit" name="submit" value="<?php echo $l_create_this_mod; ?>" />
			&nbsp;&nbsp;
			<a href="manage_modules.php"><?php echo $l_cancel; ?></a>
			
			</form>
		</td>
	</tr>
</table>
<?php include("includes/footer.php"); ?>

