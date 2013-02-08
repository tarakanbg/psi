<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php
	include_once("includes/form_functions.php");
	


?>

<?php include("includes/header.php"); ?>
<table id="structure">
	<tr>
		<td id="navigation">
			<?php include("includes/lang_select.php");	?>
		
			<a href="staff.php"><?php echo $l_return_to_menu; ?></a><br />
			<br />
			<a href="manage_modules.php"><?php echo $l_manage_modules; ?></a><br />
		</td>
		<td id="page">
		
		
		</td>
	</tr>
</table>
<?php include("includes/footer.php"); ?>

