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
require_once("modules.php");
	// This file is the place to store all basic functions

	function mysql_prep( $value ) {
		$magic_quotes_active = get_magic_quotes_gpc();
		$new_enough_php = function_exists( "mysql_real_escape_string" ); // i.e. PHP >= v4.3.0
		if( $new_enough_php ) { // PHP v4.3.0 or higher
			// undo any magic quote effects so mysql_real_escape_string can do the work
			if( $magic_quotes_active ) { $value = stripslashes( $value ); }
			$value = mysql_real_escape_string( $value );
		} else { // before PHP v4.3.0
			// if magic quotes aren't already on then add slashes manually
			if( !$magic_quotes_active ) { $value = addslashes( $value ); }
			// if magic quotes are active, then the slashes already exist
		}
		return $value;
	}

	function redirect_to( $location = NULL ) {
		if ($location != NULL) {
			header("Location: {$location}");
			exit;
		}
	}

	function confirm_query($result_set) {
		if (!$result_set) {
			die("Database query failed: " . mysql_error());
		}
	}
	
	function get_all_subjects($public = true) {
		global $connection;
		$query = "SELECT * 
				FROM ".DB_PREFIX."subjects ";
		if ($public) {
			$query .= "WHERE visible = 1 ";
		}
		$query .= "ORDER BY position ASC";
		$subject_set = mysql_query($query, $connection);
		confirm_query($subject_set);
		return $subject_set;
	}
	
		function get_all_pages($public = true) {
		global $connection;
		$query = "SELECT * 
				FROM ".DB_PREFIX."pages ";
		if ($public) {
			$query .= "WHERE visible = 1 ";
		}
		$query .= "ORDER BY position ASC";
		$page_set = mysql_query($query, $connection);
		confirm_query($page_set);
		return $page_set;
	}
	
	function get_pages_for_subject($subject_id, $public = true) {
		global $connection;
		$query = "SELECT * 
				FROM ".DB_PREFIX."pages ";
		$query .= "WHERE subject_id = {$subject_id} ";
		if ($public) {
			$query .= "AND visible = 1 ";
		}
		$query .= "ORDER BY position ASC";
		$page_set = mysql_query($query, $connection);
		confirm_query($page_set);
		return $page_set;
	}
	
	function get_subpages_for_page($page_id, $public = true) {
		global $connection;
		$query = "SELECT * 
				FROM ".DB_PREFIX."subpages ";
		$query .= "WHERE page_id = {$page_id} ";
		if ($public) {
			$query .= "AND visible = 1 ";
		}
		$query .= "ORDER BY position ASC";
		$subpage_set = mysql_query($query, $connection);
		confirm_query($subpage_set);
		return $subpage_set;
	}
	
	
	function get_subject_by_id($subject_id) {
		global $connection;
		$query = "SELECT * ";
		$query .= "FROM ".DB_PREFIX."subjects ";
		$query .= "WHERE id=" . $subject_id ." ";
		$query .= "LIMIT 1";
		$result_set = mysql_query($query, $connection);
		confirm_query($result_set);
		// REMEMBER:
		// if no rows are returned, fetch_array will return false
		if ($subject = mysql_fetch_array($result_set)) {
			return $subject;
		} else {
			return NULL;
		}
	}

	function get_page_by_id($page_id) {
		global $connection;
		$query = "SELECT * ";
		$query .= "FROM ".DB_PREFIX."pages ";
		$query .= "WHERE id=" . $page_id ." ";
		$query .= "LIMIT 1";
		$result_set = mysql_query($query, $connection);
		confirm_query($result_set);
		// REMEMBER:
		// if no rows are returned, fetch_array will return false
		if ($page = mysql_fetch_array($result_set)) {
			return $page;
		} else {
			return NULL;
		}
	}
	
		function get_subpage_by_id($subpage_id) {
		global $connection;
		$query = "SELECT * ";
		$query .= "FROM ".DB_PREFIX."subpages ";
		$query .= "WHERE id=" . $subpage_id ." ";
		$query .= "LIMIT 1";
		$result_set = mysql_query($query, $connection);
		confirm_query($result_set);
		// REMEMBER:
		// if no rows are returned, fetch_array will return false
		if ($subpage = mysql_fetch_array($result_set)) {
			return $subpage;
		} else {
			return NULL;
		}
	}
	
	function get_default_page($subject_id) {
		// Get all visible pages
		$page_set = get_pages_for_subject($subject_id, true);
		if ($first_page = mysql_fetch_array($page_set)) {
			return $first_page;
		} else {
			return NULL;
		}
	}
	
	function find_selected_page() {
		global $sel_subject;
		global $sel_page;
		global $sel_subpage;
		if (isset($_GET['subj'])) {
			$sel_subject = get_subject_by_id($_GET['subj']);
			$sel_page = get_default_page($sel_subject['id']);
			$sel_subpage = NULL;
		} elseif (isset($_GET['page'])) {
			$sel_page = get_page_by_id($_GET['page']);
			$sel_subject = NULL;
			$sel_subpage = NULL;
		} elseif (isset($_GET['subpage'])) { 
			$sel_subpage = get_subpage_by_id($_GET['subpage']);
			$sel_subject = NULL ;
			$sel_page = NULL;
		
		} else {
			$sel_subject = NULL;
			$sel_page = NULL;
			$sel_subpage = NULL;
		}
	}

		function find_selected_page_public() {
		global $sel_subject;
		global $sel_page;
		global $sel_subpage;
		if (isset($_GET['subj'])) {
			$sel_subject = get_subject_by_id($_GET['subj']);
			$sel_page = get_default_page($sel_subject['id']);
			$sel_subpage = NULL;
		} elseif (isset($_GET['page'])) {
			$sel_page = get_page_by_id($_GET['page']);
			$sel_subject = get_parent_subject_id($sel_page['id']);
			$sel_subpage = NULL;
		} elseif (isset($_GET['subpage'])) {
			$sel_subpage = get_subpage_by_id($_GET['subpage']);
			//$sel_page = get_parent_page_id($sel_subpage['id']);
			//$sel_subject = get_parent_subject_id($sel_page['id']) ;
		} else {
			$sel_subject = NULL;
			$sel_page = NULL;
			$sel_subpage = NULL;
		}
	}

	function navigation($sel_subject, $sel_page, $sel_subpage, $public = false) {
		$output = "<ul class=\"subjects\">";
		$subject_set = get_all_subjects($public);
		while ($subject = mysql_fetch_array($subject_set)) {
			$output .= "<li";
			if ($subject["id"] == $sel_subject['id']) { $output .= " class=\"selected\""; }
			$output .= "><a href=\"edit_subject.php?subj=" . urlencode($subject["id"]) . 
				"\">{$subject["menu_name"]}</a></li>";
			$page_set = get_pages_for_subject($subject["id"], $public);
			$output .= "<ul class=\"pages\">";
			while ($page = mysql_fetch_array($page_set)) {
				$output .= "<li";
				if ($page["id"] == $sel_page['id']) { $output .= " class=\"selected\""; }
				$output .= "><a href=\"content.php?page=" . urlencode($page["id"]) .
					"\">{$page["menu_name"]}</a></li>";
					
					$subpage_set = get_subpages_for_page($page["id"], $public);
					if(!empty($subpage_set)) {
						$output .= "<ul class=\"subpages\">";
						while ($subpage = mysql_fetch_array($subpage_set)) {
							$output .= "<li";
								if ($subpage["id"] == $sel_subpage['id']) { $output .= " class=\"selected\""; }
							$output .= "><a href=\"content.php?subpage=" . urlencode($subpage["id"]) .
							"\">{$subpage["menu_name"]}</a></li>";
						}
						$output .= "</ul>";
					}
						
			}
			$output .= "</ul>";
	
		}
		$output .= "</ul>";
		return $output;
	}

	function public_navigation($sel_subject, $sel_page, $sel_subpage, $public = true) {
		$output = "<ul class=\"subjects\">";
		$subject_set = get_all_subjects($public);
		while ($subject = mysql_fetch_array($subject_set)) {
			$output .= "<li";
			if ($subject["id"] == $sel_subject['id']) { $output .= " class=\"selected\""; }
			$output .= "><a href=\"index.php?subj=" . urlencode($subject["id"]) . 
				"\">{$subject["menu_name"]}</a></li>";
			
			if (isset($_GET['subpage'])) {
			$subp_sel = $_GET['subpage'];
			$subp_parpg = get_parent_page_id($subp_sel);
			$par_subj = get_parent_subject_id($subp_parpg);
			} elseif (isset($_GET['page'])) {
				$par_subj = get_parent_subject_id($_GET['page']);
			}
			if ($subject['id'] == $sel_subject['id'] || $subject['id'] == $par_subj)  /*|| $page["id"] == $sel_page['id']) */  {	
				$page_set = get_pages_for_subject($subject["id"], $public);
				$output .= "<ul class=\"pages\">";
				while ($page = mysql_fetch_array($page_set)) {
					$output .= "<li";
					if ($page["id"] == $sel_page['id']) { $output .= " class=\"selected\""; }
					$output .= "><a href=\"index.php?page=" . urlencode($page["id"]) .
						"\">{$page["menu_name"]}</a></li>";
						
					//	$subset_set = get_subpages_for_page($sel_page["id"], $public = true);
					//	$subset = mysql_fetch_array($subset_set);
					//	if (empty($subset)) {
					// next($page);
					//	}
						
						
						if (!empty($_GET['page'])) {
							$getpage = $_GET['page'];
						} elseif (!empty($_GET['subpage'])) {
							$getpage = $subp_parpg; 
						} 
						if (!empty($getpage) && ($page['id'] == $sel_page['id']) || $subp_parpg == $page['id']) {
							$subpage_set = get_subpages_for_page($getpage, $public);
							if(!empty($subpage_set)) {
								$output .= "<ul class=\"subpages\">";
								while ($subpage = mysql_fetch_array($subpage_set)) {
									if (($subpage['page_id'] == $sel_page['id']) || ($subpage['id'] == $sel_subpage['id']) || $subp_parpg == $page['id']) {									
										$output .= "<li";
										if ($subpage["id"] == $sel_subpage['id']) { $output .= " class=\"selected\""; }
										$output .= "><a href=\"index.php?subpage=" . urlencode($subpage["id"]) .
										"\">{$subpage["menu_name"]}</a></li>";
									}
								}
							$output .= "</ul>";
							}
						}
				
				}
				$output .= "</ul>";
			}
		}
		$output .= "</ul>";
		return $output;
	}
	
		function get_parent_subject_id($page_id) {
		global $connection;
		$query = "SELECT subject_id FROM ".DB_PREFIX."pages ";
		$query .= "WHERE id=" . $page_id;
		$query .= " LIMIT 1";
		$parent_id_set = mysql_query($query, $connection);
		confirm_query($parent_id_set);
		if ($parent_id = mysql_fetch_array($parent_id_set)) {
			return $parent_id['subject_id'];
		} else {
			return NULL;
		}
		}
	
		function get_parent_page_id($subpage_id) {
		global $connection;
		$query = "SELECT page_id FROM ".DB_PREFIX."subpages ";
		$query .= "WHERE id=" . $subpage_id;
		$query .= " LIMIT 1";
		$parent_id_set = mysql_query($query, $connection);
		confirm_query($parent_id_set);
		if ($parent_id = mysql_fetch_array($parent_id_set)) {
			return $parent_id['page_id'];
		} else {
			return NULL;
		}
		}

		function get_torn_for_title() {
		global $connection;
		$query = "SELECT * FROM " . DB_PREFIX . "tornpages ";
		$query .= "WHERE istitle=1 ";
		$query .= "ORDER BY position ASC";
		$torn_set = mysql_query($query, $connection);
		confirm_query($torn_set);
		if (!empty($torn_set)) {
			return $torn_set;
		} else {
			return NULL;
		}
		}
		
		function display_title_torns() {
		$torn_set = get_torn_for_title();
		while ($torn_title = mysql_fetch_array($torn_set)) {
			echo "<h2>" . $torn_title['menu_name'] . "</h2>";
			echo "<div class=\"page-content\">".$torn_title['content']."</div>";
		}			
		}
		
		function get_all_torns() {
		global $connection;
		$query = "SELECT * FROM " . DB_PREFIX . "tornpages";
		$torn_set = mysql_query($query, $connection);
		confirm_query($torn_set);
		if (!empty($torn_set)) {
			return $torn_set;
		} else {
			return NULL;
		}
		}

		function display_all_torns() {
		$torn_set = get_all_torns();
		while ($torn = mysql_fetch_array($torn_set)) {
			echo "<ul>";
			echo "<li><a href=\"torns.php?torn=" . $torn['id'] . "\">" . $torn['menu_name'] . "</a>";
			$lang = $_COOKIE['psi-lang'];
			if ($lang == "us") {
				$l_incl_in_title = "Published on title page";
			} else  {
				$l_incl_in_title = "Публикувано на началната страница";	
			}
			if ($torn['istitle'] == 1) {
				echo "&nbsp;&nbsp;<em>" . $l_incl_in_title . "</em>";
			} //elseif ($torn['istitle'] = 0) {
			echo "</li>";
			echo "</ul>";
			//}
		}			
		}
		
		function get_torn_by_id($torn_id) {
		global $connection;
		$query = "SELECT * ";
		$query .= "FROM ".DB_PREFIX."tornpages ";
		$query .= "WHERE id=" . $torn_id ." ";
		$query .= "LIMIT 1";
		$result_set = mysql_query($query, $connection);
		confirm_query($result_set);
		// REMEMBER:
		// if no rows are returned, fetch_array will return false
		if ($subject = mysql_fetch_array($result_set)) {
			return $subject;
		} else {
			return NULL;
		}
		}
		
		function get_description_for_page($pageid) {
		global $connection;
		$query = "SELECT description ";
		if (!empty($_GET['page'])) {
			$query .= "FROM " .DB_PREFIX."pages ";
		} elseif (!empty($_GET['subpage'])) {
			$query .= "FROM " .DB_PREFIX."subpages ";
		}
		$query .= "WHERE id=" . $pageid . " ";
		$query .= "LIMIT 1";
		$result_set = mysql_query($query, $connection);
		confirm_query($result_set);
		// REMEMBER:
		// if no rows are returned, fetch_array will return false
		if ($decript = mysql_fetch_array($result_set)) {
			return $decript['description'];
		} else {
			return NULL;
		}
		}
		
		
		function get_keywords_for_page($pageid) {
		global $connection;
		$query = "SELECT keywords ";
		if (!empty($_GET['page'])) {
			$query .= "FROM " .DB_PREFIX."pages ";
		} elseif (!empty($_GET['subpage'])) {
			$query .= "FROM " .DB_PREFIX."subpages ";
		}
		$query .= "WHERE id=" . $pageid . " ";
		$query .= "LIMIT 1";
		$result_set = mysql_query($query, $connection);
		confirm_query($result_set);
		// REMEMBER:
		// if no rows are returned, fetch_array will return false
		if ($keywd = mysql_fetch_array($result_set)) {
			return $keywd;
		} else {
			return NULL;
		}
		}
		
		function get_all_admins() {
		global $connection;
		$query = "SELECT * ";
		$query .= "FROM " .DB_PREFIX."users ";
		$query .= "ORDER BY id ASC";
		$admin_set = mysql_query($query, $connection);
		confirm_query($admin_set);
		return $admin_set;
		}

		function get_admin_by_id($user_id) {
		global $connection;
		$query = "SELECT * ";
		$query .= "FROM ".DB_PREFIX."users ";
		$query .= "WHERE id=" . $user_id ." ";
		$query .= "LIMIT 1";
		$result_set = mysql_query($query, $connection);
		confirm_query($result_set);
		// REMEMBER:
		// if no rows are returned, fetch_array will return false
		if ($admin = mysql_fetch_array($result_set)) {
			return $admin;
		} else {
			return NULL;
		}
		}
		
		function load_module($module_name) {
			$file = "modules/mod_".$module_name.".php";
			require_once("$file");
		}
		
		function get_zones () {
		$zones = array (
						'header-left',
						'header-center',
						'header-right',
						'top-left',
						'top-center',
						'top-right',
						'left',
						'center',
						'right',
						'center-left',
						'center-right',
						'footer-left',
						'footer-center',
						'footer-right'
					);
		return $zones;
		}
		
		function get_scopes () {
		$scopes = array (
						'global',
						'title',
						'inner'
					);
		return $scopes;
		}
		
		function get_positions () {
		$positions = array (1, 2, 3, 4, 5, 6, 7, 8, 9);
		return $positions;
		}
		
		function get_separators () {
		$separators = array (
						'n' => '<br />',
						's' => '&nbsp;',
						'ss' => '&nbsp;&nbsp;',
						'sss' => '&nbsp;&nbsp;&nbsp;'
					);
		return $separators;
		}				
?>
