<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php find_selected_page(); ?>
<?php include("includes/header.php"); ?>
<table id="structure">
	<tr>
		<td id="navigation">
			<?php include("includes/lang_select.php");	?>
			
			<?php echo navigation($sel_subject, $sel_page, $sel_subpage); ?>
			<br />
			<a href="new_subject.php">+ <? echo $l_add_subject; ?></a>
		</td>
		<td id="page">
		<?php if (!is_null($sel_subject)) { // subject selected ?>
			<h2><?php echo $sel_subject['menu_name']; ?></h2>
		<?php } elseif (!is_null($sel_page)) { // page selected ?>
			<h2><?php echo $sel_page['menu_name']; ?></h2>
			<div class="page-content">
				<?php echo $sel_page['content']; ?>
			</div>
			<br />
			<h3><?php echo $l_description . ": "?></h3>
			<div class="page-content">	<?php echo $sel_page['description']; ?> </div>
			<br />
			<h3><?php echo $l_keywords . ": "?></h3>
			<div class="page-content">	<?php echo $sel_page['keywords']; ?> </div>
			<div class="modified">
					<?php echo $l_last_modified . $sel_page['last_modified'] . $l_by . $sel_page['editor']; ?>
			</div>
			<?php
			echo "<h3>".$l_images_for_page."</h3>";
			$query = "SELECT * FROM ".DB_PREFIX."image ";
			$query .= "WHERE type='page' ";
			$query .= "AND id=".$sel_page['id'];
			$result_set = mysql_query($query, $connection);
			echo "<table><tr>";
			while ($image = mysql_fetch_array($result_set)) {
			echo "<td><a href=\"image.php?id=".$image['ImageId']."\" onclick=\"return openPopup(this.href);\">
			<img src=\"image.php?id=".$image['ImageId']."\" height=\"100px\"></a>
			<br />
			<a href=\"javascript:void(prompt('".$l_here_img_link."', 'image.php?id=".$image['ImageId']."'))\"
			 title=\"Image link\">".$l_link."</a>
			</td>";
			}
			echo	"</tr></table>";

			?>	
				<br />
			<a href="edit_page.php?page=<?php echo urlencode($sel_page['id']); ?>"><?php echo $l_edit_page; ?></a>
			
			<div style="margin-top: 2em; border-top: 1px solid #000000;">
			<h3><?php echo $l_subpages_in_page; ?></h3>
				<ul>
					<?php 
					$subpages_set = get_subpages_for_page($_GET['page']);
					while($subpage = mysql_fetch_array($subpages_set)) {
					echo "<li><a href=\"content.php?subpage={$subpage['id']}\">
					{$subpage['menu_name']}</a></li>";
					}
					?>
				</ul>
				<br />
				+ <a href="new_subpage.php?page=<?php echo $_GET['page']; ?>"><?php echo $l_add_subpage_to_page; ?></a>
			</div>
			
		<?php } elseif (isset($_GET['subpage'])) {
		
			echo "<h2>". $sel_subpage['menu_name']."</h2>";
			echo "<div class=\"page-content\">" .  $sel_subpage['content'] . "</div>"; ?>
			<br />
			<h3><?php echo $l_description . ": "?></h3>
			<div class="page-content">	<?php echo $sel_subpage['description']; ?> </div>
			<br />
			<h3><?php echo $l_keywords . ": "?></h3>
			<div class="page-content">	<?php echo $sel_subpage['keywords']; ?> </div>
			<div class="modified">
					<?php echo $l_last_modified . $sel_subpage['last_modified'] . $l_by . $sel_subpage['editor']; ?>
				</div>
			<?php
			echo "<h3>".$l_images_for_page."</h3>";
			$query = "SELECT * FROM ".DB_PREFIX."image ";
			$query .= "WHERE type='subpage' ";
			$query .= "AND id=".$sel_subpage['id'];
			$result_set = mysql_query($query, $connection);
			echo "<table><tr>";
			while ($image = mysql_fetch_array($result_set)) {
			echo "<td><a href=\"image.php?id=".$image['ImageId']."\" onclick=\"return openPopup(this.href);\">
			<img src=\"image.php?id=".$image['ImageId']."\" height=\"100px\"></a>
			<br />
			<a href=\"javascript:void(prompt('".$l_here_img_link."', 'image.php?id=".$image['ImageId']."'))\"
			 title=\"Image link\">".$l_link."</a>
			</td>";
			}
			echo	"</tr></table>";
			?>
			<?php echo "<br />";
			echo "<a href=\"edit_page.php?subpage=".urlencode($sel_subpage['id'])."\">".$l_edit_subpage."</a>"; ?>
			

			
		<?php			
		} else { // nothing selected ?>
			<h2><?php echo $l_select_to_edit; ?></h2>
			
			<br /><a href="staff.php"><?php echo $l_return_to_menu; ?></a>
		<?php } ?>
		</td>
	</tr>
</table>
<?php require("includes/footer.php"); ?>
