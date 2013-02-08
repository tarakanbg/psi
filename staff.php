<?php require_once("includes/session.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php confirm_logged_in(); ?>
<?php include("includes/header.php"); ?>
<table id="structure">
	<tr>
		<td id="navigation">
			<?php include("includes/lang_select.php");	?>
		
			<a href="index.php"><?php echo $l_return_to_public; ?></a>
		</td>
		<td id="page">
			<h2><?php echo $l_staff_menu; ?></h2>
			<p><?php echo $l_staff_welcome ." <strong>" . $_SESSION['username'] . "</strong>"; ?>.</p>
			<ul>
				<li><a href="content.php"><?php echo $l_manage_website_content; ?></a></li>
				<li><a href="torns.php"><?php echo $l_manage_torn_pages; ?></a></li>
				<li><a href="manage_modules.php"><?php echo $l_manage_modules; ?></a></li>
				<li><a href="manage_settings.php"><?php echo $l_manage_settings; ?></a></li>
				<li><a href="manage_users.php"><?php echo $l_manage_admin_users; ?></a></li>
				<li><a href="logout.php"><?php echo $l_Logout; ?></a></li>
				
			</ul>
		</td>
	</tr>
</table>
<?php include("includes/footer.php"); ?>
