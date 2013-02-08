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

// Show the content of the page, depending on
// what is selected with GET - a subject, a page,
// a subpage or nothing (title page)

global $sel_subject;
global $sel_page;
global $sel_subpage;
global $l_last_modified;
global $l_by;
?>
<?php if ($sel_page) { ?>
	<h2><?php echo $sel_page['menu_name']; ?></h2>
	<div class="page-content">
	<?php 
	// echo strip_tags(nl2br($sel_page['content']), "<b><br><p><a>"); 
	echo $sel_page['content'];
	?>
	</div>
	
	<?php
	$query = "SELECT * FROM ".DB_PREFIX."modules ";
	$query .= "WHERE name='last_modified' ";
	$query .= "LIMIT 1";
	$result = mysql_query($query, $connection);
	$lm = mysql_fetch_array($result);
	$lm_active = $lm['active'];
	$lm_author = $lm['content'];
	
	if ($lm_active == 1) {
	?>	
	<div class="modified">
	<?php echo $l_last_modified . $sel_page['last_modified'];
		if ($lm_author == "include_author") {
			echo $l_by . $sel_page['editor']; 
		} ?>
	</div>
	<?php
	}
	?>
<?php } elseif (isset($_GET['subpage'])) {
	echo "<h2>".$sel_subpage['menu_name']."</h2>";
	echo "<div class=\"page-content\">".$sel_subpage['content']."</div>";
?>
	<?php
	$query = "SELECT * FROM ".DB_PREFIX."modules ";
	$query .= "WHERE name='last_modified' ";
	$query .= "LIMIT 1";
	$result = mysql_query($query, $connection);
	$lm = mysql_fetch_array($result);
	$lm_active = $lm['active'];
	$lm_author = $lm['content'];
	
	if ($lm_active == 1) {
	?>	
	<div class="modified">
	<?php echo $l_last_modified . $sel_subpage['last_modified'];
		if ($lm_author == "include_author") {
			echo $l_by . $sel_subpage['editor']; 
		} ?>
	</div>

<?php }
 } else { 
	// Get torn pages marked for inclusion on the title page
	display_title_torns ();
} ?>
