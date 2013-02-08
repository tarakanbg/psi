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

// First - get the site title and short decription
// Are they stored in session?
if (isset($_SESSION['site_name'])) {
	$site_name = $_SESSION['site_name'];
	$site_short_descr = $_SESSION['site_short_descr'];
}
	else {

// The values are not in session - fetch them from DB
$query = "SELECT * FROM ".DB_PREFIX."variables ";
$query .= "WHERE id=2";
$result = mysql_query($query, $connection);
$site_details = mysql_fetch_array($result);
$site_name = $site_details['value1'];
$site_short_descr = $site_details['value2'];
}

// Let us store the values in session to avoid
// multiple queries
$_SESSION['site_name'] = $site_name;
$_SESSION['site_short_descr'] = $site_short_descr;

// Output of the title field begins
?>
<title><?php
	echo $site_name;
	if (!empty($_GET['subj'])) {
		echo " - " . $sel_subject['menu_name'] . " - " .$sel_page['menu_name'];
	} elseif (!empty($_GET['page'])) {
		$subj_title = get_subject_by_id($sel_subject);
		echo " - " . $subj_title['menu_name'] . " - " . $sel_page['menu_name'];
	} elseif (!empty($_GET['subpage'])) {
		$par_page_id = get_parent_page_id($_GET['subpage']);
		$par_page_title = get_page_by_id($par_page_id);
		$par_subj_id = get_parent_subject_id($par_page_id);
		$par_subj_title = get_subject_by_id($par_subj_id);
		echo " - " . $par_subj_title['menu_name'] . " - " . $par_page_title['menu_name'] 
			. " - " . $sel_subpage['menu_name'];
	} else {
		echo " - " . $site_short_descr;
	}
?></title>			
		<?php echo "<meta name=\"keywords\" content=\""; 
	if (!empty($_GET['page'])) {
		$kwrd = $sel_page['keywords']; 
	} elseif (!empty($_GET['subpage'])) {
		$kwrd = $sel_subpage['keywords'];
	} elseif (!empty($_GET['subj'])) {
		$kwrd = $sel_page['keywords'];
	} else {
	// Fetch the global keywords from DB
	$query = "SELECT * FROM ".DB_PREFIX."variables ";
	$query .= "WHERE id=4";
	$result = mysql_query($query, $connection);
	$keywords_set = mysql_fetch_array($result);
	$kwrd = $keywords_set['value1'];
	}
	echo $kwrd;
	echo "\">";
?>
	
		<?php echo "<meta name=\"description\" content=\""; 
	if (!empty($_GET['page'])) {
		$descr = $sel_page['description']; 
	} elseif (!empty($_GET['subpage'])) {
		$descr = $sel_subpage['description'];
	} elseif (!empty($_GET['subj'])) {
		$descr = $sel_page['description'];
	} else {
	// Fetch the global description from DB
	$query = "SELECT * FROM ".DB_PREFIX."variables ";
	$query .= "WHERE id=3";
	$result = mysql_query($query, $connection);
	$descr_set = mysql_fetch_array($result);
	$descr = $descr_set['value1'];
	}
	echo $descr;
	echo "\">";
?>

