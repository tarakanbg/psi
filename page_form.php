<?php require_once("includes/session.php"); ?>
<?php confirm_logged_in(); ?>
<?php // this page is included by edit_page.php ?>
<?php if (!isset($new_page)) {$new_page = false;} ?>

<p><?php echo $l_page_name; ?><input type="text" name="menu_name" value="<?php echo $sel_page['menu_name']; ?>" id="menu_name" /></p>

<p><?php echo $l_position; ?><select name="position">
	<?php
		if (!$new_page) {
			$page_set = get_pages_for_subject($sel_page['subject_id'], $public = false);
			$page_count = mysql_num_rows($page_set);
		} else {
			$page_set = get_pages_for_subject($sel_subject['id']);
			$page_count = mysql_num_rows($page_set) + 1;
		}
		for ($count=1; $count <= $page_count; $count++) {
			echo "<option value=\"{$count}\"";
			if ($sel_page['position'] == $count) { echo " selected"; }
			echo ">{$count}</option>";
		}
	?>
</select></p>
<p><?php echo $l_visible; ?>
	<input type="radio" name="visible" value="0"<?php 
	if ($sel_page['visible'] == 0) { echo " checked"; } 
	?> /> <?php echo $l_No; ?>
	&nbsp;
	<input type="radio" name="visible" value="1"<?php 
	if ($sel_page['visible'] == 1) { echo " checked"; } 
	?> /> <?php echo $l_Yes; ?>
</p>
<?php
echo $l_images_for_page."<br />";
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
 title=\"Image link\">".$l_link."</a> &nbsp;
<a href=\"delete_image.php?image=".$image['ImageId']."\"
 onclick=\"return confirm('".$l_sure_del_image."');\">".$l_delete."</а>
</td>";
}
echo	"</tr></table>";

?>
<p><?php echo $l_content; ?><br />
	<textarea name="content" rows="20" cols="100" class="mceEditor"><?php echo $sel_page['content']; ?></textarea>
</p>
<p><?php echo $l_description . ": "; ?><br />
	<textarea name="description" rows="5" cols="60" class="mceNoEditor"><?php echo $sel_page['description']; ?></textarea>
</p>
<p><?php echo $l_keywords . ": "; ?><br />
	<textarea name="keywords" rows="5" cols="60" class="mceNoEditor"><?php echo $sel_page['keywords']; ?></textarea>
</p>


