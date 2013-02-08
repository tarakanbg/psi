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

if ($_GET['active'] == "yes") {
	$query = "UPDATE ".DB_PREFIX."modules SET ";
	$query .="active = 1 ";
	$query .="WHERE id =".$_GET['mod'];
	if ($result = mysql_query($query, $connection)) {
		$mod_mes1 = $l_mod_activated;
	}
} elseif ($_GET['active'] == "no") {
	$query = "UPDATE ".DB_PREFIX."modules SET ";
	$query .="active = 0 ";
	$query .="WHERE id =".$_GET['mod'];
	if ($result = mysql_query($query, $connection)) {
		$mod_mes1 =  $l_mod_deactivated;
	}
}

if (!empty($_GET['edit'])) {

	if (isset($_POST['submit'])) {
		$id = $_GET['edit'];
		$name = mysql_prep($_POST['name']);
		$type = $_POST['type'];
		$location = $_POST['location'];
		$position = $_POST['position'];
		$scope = $_POST['scope'];
		$separator = htmlentities($_POST['separator']);
		$content = mysql_prep($_POST['content']);
		
		if ($type == "file") {
			$query = "UPDATE ".DB_PREFIX."modules SET 
						location = '{$location}',
						position = {$position}, 
						scope = '{$scope}', ";
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
			
			
			if ($result = mysql_query($query, $connection)) {
				redirect_to("manage_modules.php?msg=editdone");
			} else {
				redirect_to("manage_modules.php?msg=editfail");
			}
		} // end of if type = file
		elseif ($type == "html") {
			
			$query = "UPDATE ".DB_PREFIX."modules SET 
						location = '{$location}',
						position = {$position},
						name = '{$name}',
						content = '{$content}', 
						scope = '{$scope}', ";
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
			
			
			if ($result = mysql_query($query, $connection)) {
				redirect_to("manage_modules.php?msg=editdone");
			} else {
				redirect_to("manage_modules.php?msg=editfail");
			}
		
		
		} // end of if type = html
		
		elseif ($name == "last_modified") {
			$author = $_POST['author'];
			if ($author == "on") {
				$content = "include_author";
			} elseif ($author == "off") {
				$content = "non_include_author";
			}
			
			$query = "UPDATE ".DB_PREFIX."modules SET 
						content = '{$content}',
						scope = '{$scope}' ";
			$query .= "	WHERE id = {$id};";
			
			
			if ($result = mysql_query($query, $connection)) {
				redirect_to("manage_modules.php?msg=editdone");
			} else {
				redirect_to("manage_modules.php?msg=editfail");
			}
					
		} // end of last_modified
			
	} // end of if isset POST submit
		
	$id = $_GET['edit'];
	$query = "SELECT * FROM ".DB_PREFIX."modules ";
	$query .= "WHERE id="."'$id'";
	$query .= "LIMIT 1";
	$result = mysql_query($query, $connection);
	$module_set = mysql_fetch_array($result);
	
	$id = $module_set['id'];
	$name = $module_set['name'];
	$type = $module_set['type'];
	$location = $module_set['location'];
	$position = $module_set['position'];
	$scope = $module_set['scope'];
	$content = $module_set['content'];
	$separator = $module_set['separator'];

}


if ($_GET['mods'] == "active") {
	$query = "SELECT * FROM ".DB_PREFIX."modules ";
	$query .= "WHERE active=1 ";
	$query .= "ORDER BY type";
	$module_set = mysql_query($query, $connection);
} else {
	$query = "SELECT * FROM ".DB_PREFIX."modules ";
	$query .= "ORDER BY type";
	$module_set = mysql_query($query, $connection);
}



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
<?php	if (!empty($_GET['edit'])) { ?>
			<h2><?php
			if ($name == "public_navigation_vert"){
				$modname = $l_public_navigation_vert;
			} elseif ($name == "site_title"){
				$modname = $l_mod_site_title;
			} elseif ($name == "show_content"){
				$modname = $l_mod_show_content;
			} elseif ($name == "classic_footer"){
				$modname = $l_mod_classic_footer;
			} elseif ($name == "last_modified"){
				$modname = $l_mod_last_modified;
			} else {
				$modname = $name;
			}
			echo $l_editing_module.": ".$modname; ?></h2>
			
			<form action="manage_modules.php?edit=<?php echo $id; ?>" method="post">
			<?php if ($name == "last_modified") { ?>
			
				<?php echo $l_mod_scope.": "; ?><select name="scope">
				<?php
				$scopes = get_scopes();
				foreach ($scopes as $scopef) {
				echo "<option value=\"{$scopef}\"";
				if ($scopef == $scope) {
					echo " selected";
				}
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
				
				<input type="radio" name="author" value="on"
				<?php if ($content=="include_author") { echo "checked"; } ?>
				> <?php echo $l_show_author; ?>
				&nbsp;
				<input type="radio" name="author" value="off"
				<?php if ($content!="include_author") { echo "checked"; } ?>
				> <?php echo $l_hide_author; ?>
				
				<input type="hidden" name="name" value="<?php echo $name; ?>">
				
				<br /><br />		
			 
			
	<?php	} // end of mod last_modified
			 else {
			
			?>
			
			<?php if ($type == "html") { ?>
				<p><?php echo $l_mod_name.": "; ?><input type="text" name="name" value="<?php echo $name; ?>" id="name" /></p>
			<?php } ?>
			
				<p><?php echo $l_mod_location.": "; ?><select name="location">
				<?php
				$zones = get_zones();
				foreach ($zones as $zone) {
				echo "<option value=\"{$zone}\"";
				if ($zone == $location) {
					echo " selected";
				}
				echo ">".$zone."</option>";
				}
				?>
				</select>&nbsp;&nbsp;
				
				<?php echo $l_mod_position.": "; ?><select name="position">
				<?php
				$positions = get_positions();
				foreach ($positions as $positionf) {
				echo "<option value=\"{$positionf}\"";
				if ($positionf == $position) {
					echo " selected";
				}
				echo ">".$positionf."</option>";
				}
				?>
				</select>&nbsp;&nbsp;&nbsp;
				
				<?php echo $l_mod_scope.": "; ?><select name="scope">
				<?php
				$scopes = get_scopes();
				foreach ($scopes as $scopef) {
				echo "<option value=\"{$scopef}\"";
				if ($scopef == $scope) {
					echo " selected";
				}
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
				if ($separatorf == $separator) {
					echo " selected";
				}
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
				
				<?php if ($type == "html") { ?>
				<p><?php echo $l_content; ?><br />
				<textarea name="content" rows="10" cols="60" class="mceNoEditor"><?php echo $content; ?></textarea>
				</p>			
				<?php } ?>
				
				<?php if ($type != "html") { ?>
				<input type="hidden" name="name" value="<?php echo $name; ?>">
				<?php } ?>
				
				<?php } // end of else - non specific module
				?>
				<input type="hidden" name="type" value="<?php echo $type; ?>">
				
				<input type="submit" name="submit" value="<?php echo $l_update_mod; ?>" />
				&nbsp;&nbsp;
				<a href="manage_modules.php"><?php echo $l_cancel; ?></a>
			
			</form>
			
<?php		} else {
?>
		<?php if (isset($mod_mes1)) { echo "<h3>".$mod_mes1."</h3>"; }?>
		<?php if ($_GET['msg'] == "editdone") { echo "<h3>".$l_editdone."</h3>"; } ?>
		<?php if ($_GET['msg'] == "editfail") { echo "<h3>".$l_editfail."</h3>"; } ?>
		<?php if ($_GET['msg'] == "adddone") { echo "<h3>".$l_adddone."</h3>"; } ?>
		<?php if ($_GET['msg'] == "addfail") { echo "<h3>".$l_addfail."</h3>"; } ?>
		<?php if ($_GET['msg'] == "deldone") { echo "<h3>".$l_deldone."</h3>"; } ?>
		<?php if ($_GET['msg'] == "delfail") { echo "<h3>".$l_delfail."</h3>"; } ?>
		<h2><?php echo $l_installed_modules; ?></h2>
		<a href="manage_modules.php"><?php echo $l_all; ?></a>&nbsp;&nbsp;
		<a href="manage_modules.php?mods=active"><?php echo $l_active; ?></a><br /><br />
		<table width="750px" border="2"><tr bgcolor="#8D0D19">
			<td><font color="#EEE4B9"><?php echo $l_mod_name; ?></font></td>
			<td><font color="#EEE4B9"><?php echo $l_mod_type; ?></font></td>
			<td><font color="#EEE4B9"><?php echo $l_mod_location; ?></font></td>
			<td><font color="#EEE4B9"><?php echo $l_mod_position; ?></font></td>
			<td><font color="#EEE4B9"><?php echo $l_mod_scope; ?></font></td>
			<td><font color="#EEE4B9"><?php echo $l_mod_separator; ?></font></td>
			<td colspan="3" align="center"><font color="#EEE4B9"><?php echo $l_mod_action; ?></font></td>
			</tr>
		<?php
		while ($module = mysql_fetch_array($module_set)) {
			if ($module['name'] == "public_navigation_vert"){
				$modname = $l_public_navigation_vert;
			} elseif ($module['name'] == "site_title"){
				$modname = $l_mod_site_title;
			} elseif ($module['name'] == "show_content"){
				$modname = $l_mod_show_content;
			} elseif ($module['name'] == "classic_footer"){
				$modname = $l_mod_classic_footer;
			} elseif ($module['name'] == "last_modified"){
				$modname = $l_mod_last_modified;
			} else {
				$modname = $module['name'];
			}
			
			echo "<tr><td>";
			echo $modname;
			echo "</td><td>";
			echo $module['type'];
			echo "</td><td>";
			echo $module['location'];
			echo "</td><td>";
			echo $module['position'];
			echo "</td><td>";
			if ($module['scope'] == "global") {
				$modscope = $l_mod_global_scope;
			} elseif ($module['scope'] == "inner") {
				$modscope = $l_mod_inner_scope;
			} elseif ($module['scope'] == "title") {
				$modscope = $l_mod_title_scope;
			} else {
				$modscope = $module['scope'];
			}
			echo $modscope;
			echo "</td><td>";
			if ($module['separator'] == "&nbsp;") {
				echo $l_1_space;
			} elseif ($module['separator'] == "<br />") {
				echo $l_new_line;
			} elseif ($module['separator'] == "&nbsp;&nbsp;") {
				echo $l_2_space;
			} elseif ($module['separator'] == "&nbsp;&nbsp;&nbsp;") {
				echo $l_3_space;
			}
			echo "</td><td>";
			if ($module['active'] == 0) {
			echo "<a href=\"manage_modules.php?active=yes&mod=".$module['id']."\">".$l_activate_mod."</a>";
			} elseif ($module['active'] == 1) {
			echo "<a href=\"manage_modules.php?active=no&mod=".$module['id']."\">".$l_deactivate_mod."</a>";
			}
			echo "</td><td>";
			echo "<a href=\"manage_modules.php?edit=".$module['id']."\">".$l_edit_mod."</a>";
			echo "</td><td>";
			if ($module['can_delete'] == "1") {
			echo "<a href=\"delete_module.php?id=".$module['id']."\" onclick=\"return confirm('" . $l_confirm_del_module . "');\">".$l_del_mod."</a>";
			}
			echo "</td></tr>";
			
		}
		
		
		?>
		</table>
		<br />
		<strong>
		+ <a href="create_module.php"><?php echo $l_create_module; ?></a>
		</strong>
		
<?php     }  ?>
		</td>
	</tr>
</table>
<?php include("includes/footer.php"); ?>

