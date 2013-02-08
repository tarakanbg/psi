<?php session_start(); ?>
<?php
ob_start();
 //Are we installed?
require("../includes/constants.php");
$psi_installed = PSI_INSTALLED;
$psistep = $_GET['step'];
if ($psistep >= "5") {
	$psi_installed	= "no";
}
if ($psi_installed == "yes") {
$location = "../index.php";
header("Location: {$location}");
}
ob_end_flush();
?>
<?php
// Get the step number
$step = $_GET['step'];

// Get the language from the session or from GET array
// If there's no language in the session - store the selcted there
if (isset($_GET['lang'])) {
	$lang = $_GET['lang'];
} elseif (isset($_SESSION['lang'])) {
	$lang = $_SESSION['lang'];
}
$_SESSION['lang'] = $lang;

	
// Step 1 preparations
if ($step==1) {
	// Check if the constants file is writeable
	$constants_file = "../includes/constants.php";
	if (is_writable($constants_file)) {
    		$con_message = "<font color=\"green\">The config file constants.php is writeable</font>";
    			if ($lang=="bg") {
    				$con_message = "<font color=\"green\">Конфигурационният файл constants.php е достъпен за запис</font>";
    			}
	} else {
		$con_message = "<font color=\"red\">The config file constants.php is NOT writeable</font><br />
							<em>Please, locate the file in your \"includes\" folder and make it writeable (chmod 777)</em>";
			if ($lang=="bg") {
				$con_message = "<font color=\"red\">Конфигурационният файл constants.php НЕ е достъпен за запис</font><br />
									<em>Моля, намерете този файл в папка \"includes\" и разрешете да бъде презаписан (chmod 777)</em>";
			}
	}
	
	// Check output buffering
	$obtest = ob_get_status();
		if (!empty($obtest)) {
			$ob_message = "<font color=\"green\">Output buffering is enabled on your PHP server</font>";
			if ($lang=="bg") {
    				$ob_message = "<font color=\"green\">Функцията \"output buffering\" е разрешена на вашия сървър</font>";
    			}
		} else {
			$ob_message = "<font color=\"red\">Output buffering is NOT enabled on your PHP server</font><br />
							<em>Please, locate the setting \"output_buffering\" in your php.ini file and <br />
							change it to \"output_buffering = 4096\"</em>";
			if ($lang=="bg") {
				$ob_message = "<font color=\"red\">Тази функция не е позволена във вашия PHP сървър</font><br />
							<em>Моля, намерете опцията \"output_buffering\" във вашия php.ini файл и <br />
							я променете на \"output_buffering = 4096\"</em>";
			}
		}
	
} // end of step 1

// Step 2 preparations
elseif ($step == 2) {
	require_once("../includes/functions.php");
	
	// If the form has been submitted - pull the values from it
	$mysql_server = $_POST['mysql_server']; 
	$mysql_root = $_POST['mysql_rootp'];
	$mysql_db = $_POST['mysql_db'];
	$mysql_user = $_POST['mysql_user'];
	$mysql_password = $_POST['mysql_pass'];
	$site_title = $_POST['site_title'];
	$username = $_POST['username'];
	$userpass = $_POST['userpass'];
	$mysql_prefix = $_POST['mysql_prefix'];
	
	// Write the global values to session
	$_SESSION['mysql_server'] = $mysql_server;
	$_SESSION['mysql_root'] = $mysql_root;
	$_SESSION['mysql_db'] = $mysql_db;
	$_SESSION['mysql_user'] = $mysql_user;
	$_SESSION['mysql_password'] = $mysql_password;
	$_SESSION['site_title'] = $site_title;
	$_SESSION['username'] = $username;
	$_SESSION['userpass'] = $userpass;
	$_SESSION['mysql_prefix'] = $mysql_prefix;	
	$_SESSION['isroot'] = $_GET['root'];
	
} // end of step 2

// Step 3
elseif ($step == 3) {
	// Pull down the preferences stored in session
	$mysql_server = $_SESSION['mysql_server'];
	$mysql_root = $_SESSION['mysql_root'];
	$mysql_db = $_SESSION['mysql_db'];
	$mysql_user = $_SESSION['mysql_user'];
	$mysql_password = $_SESSION['mysql_password'];
	$username = $_SESSION['username'];
	$userpass = $_SESSION['userpass'];
	$isroot = $_SESSION['isroot'];
	$mysql_prefix = $_SESSION['mysql_prefix'];
	
	if ($isroot == "yes") {
		
		// Connect to the MySQL server
		$connection = mysql_connect($mysql_server,root,$mysql_root);
		if (!$connection) {
		die("Database connection failed: Go back to <a href=\"index.php?step=2\">Step 2</a> and correct your preferences!" . mysql_error());}
		
		// Select utf8 encoding
		$charset = utf8;
		mysql_set_charset($charset,$connection);
		
		// Create the DB
		$query = "CREATE DATABASE ";
		$query .= $mysql_db;
		$query .= " DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
		$result = mysql_query($query, $connection);
		
		// Create the user
		$query2 = "CREATE USER '";
		$query2 .= $mysql_user;
		$query2 .= "'@'";
		$query2 .= $mysql_server;
		$query2 .= "' IDENTIFIED BY '";
		$query2 .= $mysql_password;
		$query2 .= "';";
		$result2 = mysql_query($query2, $connection);	
		
		// Grant privileges on DB to user
		$query3 = "GRANT ALL PRIVILEGES ON ";
		$query3 .= $mysql_db;
		$query3 .= " . * ";
		$query3 .= "TO '";
		$query3 .= $mysql_user;
		$query3 .= "'@'";
		$query3 .= $mysql_server;
		$query3 .= "';";
		$result3 = mysql_query($query3, $connection);	
		
		// Close the root MySQL connection
		mysql_close($connection);
	}
	// Reconnect to MySQL with user account
	$connection = mysql_connect($mysql_server,$mysql_user,$mysql_password);
	$db_select = mysql_select_db($mysql_db,$connection);
	
	// Select utf8 encoding
	$charset = utf8;
	mysql_set_charset($charset,$connection);
	
	// Create tables and structure
	
	$query10 = "DROP TABLE IF EXISTS `".$mysql_prefix."pages`;";
  	$query11 = "CREATE TABLE `".$mysql_prefix."pages` (
  	 `id` int(11) NOT NULL auto_increment,
  `subject_id` int(11) NOT NULL,
  `menu_name` varchar(50) NOT NULL,
  `position` int(3) NOT NULL,
  `visible` tinyint(1) NOT NULL,
  `author` varchar(50) NOT NULL,
  `content` longtext NOT NULL,
  `description` text NOT NULL,
  `keywords` text NOT NULL,
  `last_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `editor` varchar(50) NOT NULL,
 	 PRIMARY KEY  (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	$result = mysql_query($query10, $connection);
	$result = mysql_query($query11, $connection);
	
	$query12 = "DROP TABLE IF EXISTS `".$mysql_prefix."subjects`;";
	$query13 = "CREATE TABLE `".$mysql_prefix."subjects` (
	  `id` int(11) NOT NULL auto_increment,
  	`menu_name` varchar(50) NOT NULL,
  	`position` int(3) NOT NULL,
  	`visible` tinyint(1) NOT NULL,
  	PRIMARY KEY  (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	$result = mysql_query($query12, $connection);
	$result = mysql_query($query13, $connection);
	
	
	$query14 = "DROP TABLE IF EXISTS `".$mysql_prefix."users`;";
	$query15 = "CREATE TABLE `".$mysql_prefix."users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(50) NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `hashed_password` varchar(40) NOT NULL,
  PRIMARY KEY  (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	$result = mysql_query($query14, $connection);
	$result = mysql_query($query15, $connection);

	
	$query16 = "CREATE TABLE `".$mysql_prefix."subpages` (
	 `id` int(11) NOT NULL auto_increment,
  `page_id` int(11) NOT NULL,
  `menu_name` varchar(50) NOT NULL,
  `visible` tinyint(4) NOT NULL,
  `position` int(3) NOT NULL,
  `author` varchar(50) NOT NULL,
  `content` longtext NOT NULL,
  `description` text NOT NULL,
  `keywords` text NOT NULL,
  `last_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `editor` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
	) ENGINE = MYISAM ;";
	$result = mysql_query($query16, $connection);
	
	
	$query17 = "CREATE TABLE `".$mysql_prefix."tornpages` (
  `id` int(11) NOT NULL auto_increment,
  `menu_name` varchar(40) NOT NULL,
  `visible` tinyint(4) NOT NULL,
  `position` int(11) NOT NULL,
  `content` text NOT NULL,
  `istitle` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`)
	) ENGINE = MYISAM ;";
	$result = mysql_query($query17, $connection);
	
	$query18 = "CREATE TABLE IF NOT EXISTS `".$mysql_prefix."image` (
  `ImageId` int(10) NOT NULL auto_increment,
  `Image` longblob,
  `FileType` varchar(32) default NULL,
  `type` varchar(10) NOT NULL,
  `id` int(6) NOT NULL,
  PRIMARY KEY  (`ImageId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
	$result = mysql_query($query18, $connection);
	
	$query19 = "CREATE TABLE IF NOT EXISTS `".$mysql_prefix."modules` (
  `id` int(10) NOT NULL auto_increment,
  `name` varchar(250) NOT NULL,
  `type` varchar(100) NOT NULL,
  `location` varchar(250) NOT NULL,
  `position` int(10) NOT NULL,
  `active` tinyint(4) NOT NULL,
  `scope` varchar(250) NOT NULL,
  `address` varchar(250) NOT NULL,
  `content` text NOT NULL,
  `separator` varchar(200) NOT NULL,
  `can_delete` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
		$result = mysql_query($query19, $connection);
		
		
	$query20 =	"CREATE TABLE IF NOT EXISTS `".$mysql_prefix."variables` (
  `id` int(10) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `value1` text NOT NULL,
  `value2` text NOT NULL,
  `value3` text NOT NULL,
  `can_edit` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
		$result = mysql_query($query20, $connection);
		
		
	$query21 = 	"INSERT INTO `".$mysql_prefix."modules` (`id`, `name`, `type`, `location`, `position`, `active`, `scope`, `address`, `content`, `separator`, `can_delete`) VALUES
(1, 'public_navigation_vert', 'file', 'left', 2, 1, 'global', 'modules/mod_public_navigation_vert.php', '', '<br />', 0),
(2, 'site_title', 'file', 'header-left', 1, 1, 'global', 'modules/mod_site_title.php', '', '&nbsp;', 0),
(3, 'show_content', 'file', 'center', 1, 1, 'global', 'modules/mod_show_content.php', '', '<br />', 0),
(4, 'classic_footer', 'file', 'footer-center', 1, 1, 'global', 'modules/mod_classic_footer.php', '', '<br />', 0),
(5, 'last_modified', 'inline', '', 0, 1, 'global', '', 'include_author', '', 0);";
	$result = mysql_query($query21, $connection);
	
	
	$query22 = 	"INSERT INTO `".$mysql_prefix."variables` (`id`, `name`, `value1`, `value2`, `value3`, `can_edit`) VALUES
(1, 'site_lang', 'us', '', '', 1),
(2, 'site_name', 'Edit me', 'Edit me', '', 1),
(3, 'site_descr', 'PSI CMS - a personal, simple, intuitive content management system', '', '', 1),
(4, 'site_keywords', 'psi-cms, content, management, system, personal, simple, intuitive', '', '', 1),
(5, 'psi_version', '0.4', '', '', 0);";
		$result = mysql_query($query22, $connection);
		
	
	
	// Create the admin user
	$hashed_password = sha1($userpass);
	$query101 = "INSERT INTO ".$mysql_prefix."users (
							username, hashed_password
						) VALUES (
							'{$username}', '{$hashed_password}'
						);";
	$result = mysql_query($query101, $connection);

} // end of step 3

// Step 4
elseif ($step == 4) {
	// Pull down the preferences stored in session
	$mysql_server = $_SESSION['mysql_server'];
	$mysql_root = $_SESSION['mysql_root'];
	$mysql_db = $_SESSION['mysql_db'];
	$mysql_user = $_SESSION['mysql_user'];
	$mysql_password = $_SESSION['mysql_password'];
	$username = $_SESSION['username'];
	$userpass = $_SESSION['userpass'];
	$site_title = $_SESSION['site_title'];
	$isroot = $_SESSION['isroot'];
	$mysql_prefix = $_SESSION['mysql_prefix'];

	$filename = '../includes/constants.php';
	$content = "<?php
define(\"DB_SERVER\", \"$mysql_server\");
define(\"DB_USER\", \"$mysql_user\");
define(\"DB_PASS\", \"$mysql_password\");
define(\"DB_NAME\", \"$mysql_db\");
define(\"SITE_NAME\", \"$site_title\");
define(\"DB_PREFIX\", \"$mysql_prefix\");
define(\"PSI_INSTALLED\", \"yes\");
define(\"PSI_VERSION\", \"0.4\");
?>";
	
       // open file in write mode
	    if (!$handle = fopen($filename, 'w')) {
	         $message4 = "Cannot open file ($filename)";
	         exit;
	    }
	
	    // Write $somecontent to our opened file.
	    if (fwrite($handle, $content) === FALSE) {
	        $message4 = "Cannot write to file ($filename)";
	        exit;
	    }
	
	    $message4 = "Success, wrote ($somecontent) to file ($filename)";


// Reconnect to MySQL with user account
	$connection = mysql_connect($mysql_server,$mysql_user,$mysql_password);
	$db_select = mysql_select_db($mysql_db,$connection);
	
	// Select utf8 encoding
	$charset = utf8;
	mysql_set_charset($charset,$connection);

$querytit = 	"UPDATE `".$mysql_prefix."variables` SET value1 = '$site_title' WHERE id = '2' LIMIT 1;";
$result = mysql_query($querytit, $connection);

	
} // end of step 4

elseif ($step == 5) {

	

} // end of step 5

// Step 6
elseif ($step == 6) {
	require_once("../includes/functions.php");
		// Four steps to closing a session
		// (i.e. logging out)

		// 1. Find the session
		session_start();
		
		// 2. Unset all the session variables
		$_SESSION = array();
		
		// 3. Destroy the session cookie
		if(isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time()-42000, '/');
		}
		
		// 4. Destroy the session
		session_destroy();
		
	//	
	redirect_to("../login.php");
} //end of step 6
?>

<?php
// Localization follows with US as a default choice
if ($lang == "bg") {
	$main_title = "PSI CMS Инсталатор";
	$page_title = "Добре дошли в PSI CMS";
	$step0_cont = "<strong>PSI CMS</strong> е персонална, опростена и интуитивна система за управление на съдържание. <br />
		Тя ви позволява да задвижите прост динамичен уеб сайт и да го поддържате самостоятелно без специални познания по уеб програмиране. <br />
		<br />
		Този инсталатор ще ви преведе през инсталационния процес в няколко лесни стъпки. <br />
		<div style=\"margin-top: 2em; border-top: 1px solid #000000;\"><br />
		=> Не забравяйте да <strong>изтриете или преименувате</strong> папката \"install\" на това приложение след като завърши инсталацията. <br />
		 В противен случай рискувате да загубите цялото си съдържание, ако този инсталатор бъде повторно стартиран впоследствие.
		 </div>
		 <div style=\"margin-top: 2em; border-top: 1px solid #000000;\"><br />
		Прочетете Лицензионното споразумение, и ако го приемате, продължете към следващата стъпка:
		<br /><br />";

		$step0_cont2 = "<br><a href=\"index.php?step=1\">Продължете към Стъпка 1</a>
		</div>";
	 $step0_menu = "Начало";
	 $step1_menu = "Стъпка 1";
	 $lang_select = "<strong>Изберете език: </strong>";
	 $step1_cont = "Проверка дали конфигурационният файл constants.php е достъпен за запис... <br />
	 	<strong>=> Резултат:</strong> " . $con_message . "<br />
	 	<br />Проверка дали вашият сървър поддържа функцията \"output buffering\"... <br />
	 	<strong>=> Резултат:</strong> " . $ob_message . "<br />
	 	<div style=\"margin-top: 2em; border-top: 1px solid #000000;\"><br />
		<a href=\"index.php?step=1\">Повторете теста</a>&nbsp;&nbsp;<a href=\"index.php?step=2\">Продължете към Стъпка 2</a>
		</div>	";	
	 $step1_title = "Проверка на системните изисквания";
	 $step2_menu = "Стъпка 2";
	 $step2_pre1 =  "Имате ли root достъп до MySQL сървъра (знаете ли паролата на акаунта \"root\" на MySQL)?
							<br /><br/><em>Ако отговорите с \"Не\"
							ще трябва да посочите за тази инсталация вече съществуваща база данни и да въведете името и паролата
							на вече съществуващ MySQL потребител с права за използване на посочената база.</em><br /><br />
							<em>Ако отговорите с \"Да\", ще трябва да въведете root паролата и създаването на нова база данни и нов
							потребител, асоцииран с тази база, ще бъдат извършени автоматично от инсталатора.</em><br /><br />
							<a href=\"index.php?step=2&root=yes\"><strong>Да</strong></a>&nbsp;&nbsp;&nbsp;
							<a href=\"index.php?step=2&root=no\"><strong>Не</strong></a>";
	$step2_nonroot = 
	 $step2_title = "Определяне на глобалните настройки";
	 $step2_cont_form = "<p>Попълнете формата. <strong>Всички полета са задължителни.</stong></p> 
	 			<form action=\"index.php?step=2&root=yes\" method=\"post\">
	 			<table>
	 			<tr>
	 				<td> Адресът на вашия MySQL сървър:</td> 
					<td><input type=\"text\" name=\"mysql_server\" value=\"" . $_POST['mysql_server'] . "\" id=\"mysql_server\" /></td>
					<td><em>Обикновено \"localhost\" </em></td>
				</tr><tr>
					<td>Вашата MySQL \"root\" парола:</td>
					<td><input type=\"text\" name=\"mysql_rootp\" value=\"" . $_POST['mysql_rootp'] . "\" id=\"mysql_rootp\" /></td>
					<td><em>Ще бъде използвана <strong>единствено</strong> за създаване на база данни и потребител за PSI CMS във вашия MySQL сървър. Няма да бъде съхранявана или използвана след края на инсталацията. Оставете полето празно, ако не използвате парола за root.</em></td>
				</tr><tr>
					<td>База данни за PSI CMS</td>
					<td><input type=\"text\" name=\"mysql_db\" value=\"" . $_POST['mysql_db'] . "\" id=\"mysql_db\" /></td>
					<td><em>Ще бъде създадена от този инсталатор</em></td>
				</tr><tr>
					<td>Префикс на таблиците в БД</td>
					<td><input type=\"text\" name=\"mysql_prefix\" value=\"" . $_POST['mysql_prefix'] . "\" id=\"mysql_prefix\" /></td>
					<td><em>Стойността по подразбиране е \"psi_\"</em></td>
				</tr><tr><tr>
					<td>MySQL потребител за PSI CMS</td>
					<td><input type=\"text\" name=\"mysql_user\" value=\"" . $_POST['mysql_user'] . "\" id=\"mysql_user\" /></td>
					<td><em>Ще бъде създаден от този инсталатор</em></td>
				</tr><tr>
					<td>MySQL парола за PSI CMS</td>
					<td><input type=\"text\" name=\"mysql_pass\" value=\"" . $_POST['mysql_pass'] . "\" id=\"mysql_pass\" /></td>
					<td><em>Ще бъде създадена от този инсталатор</em></td>
				</tr><tr>
					<td>Името на вашия сайт</td>
					<td><input type=\"text\" name=\"site_title\" value=\"" . $_POST['site_title'] . "\" id=\"site_title\" /></td>
					<td><em>Може да бъде променено по-късно</em></td>
				</tr><tr>
					<td>Вашето потребителско име</td>
					<td><input type=\"text\" name=\"username\" value=\"" . $_POST['username'] . "\" id=\"username\" /></td>
					<td><em>Ще бъде използвано за логване в служебната зона на сайта</em></td>
				</tr><tr>
					<td>Вашата потребителска парола</td>
					<td><input type=\"text\" name=\"userpass\" value=\"" . $_POST['userpass'] . "\" id=\"userpass\" /></td>
					<td><em>Ще бъде използвана за логване в служебната зона на сайта</em></td>
				</tr>				
				</table><br />
				<input type=\"submit\" name=\"submit2\" value=\"Submit preferences\" />
				</form>
				";
		$step2_cont_form_noroot = "<p>Попълнете формата. <strong>Всички полета са задължителни.</stong></p> 
	 			<form action=\"index.php?step=2&root=no\" method=\"post\">
	 			<table>
	 			<tr>
	 				<td> Адресът на вашия MySQL сървър:</td> 
					<td><input type=\"text\" name=\"mysql_server\" value=\"" . $_POST['mysql_server'] . "\" id=\"mysql_server\" /></td>
					<td><em>Обикновено \"localhost\" </em></td>
				</tr><tr>
					<td>База данни за PSI CMS</td>
					<td><input type=\"text\" name=\"mysql_db\" value=\"" . $_POST['mysql_db'] . "\" id=\"mysql_db\" /></td>
					<td><em>Трябва да съществува на сървъра</em></td>
				</tr><tr>
					<td>Префикс на таблиците в БД</td>
					<td><input type=\"text\" name=\"mysql_prefix\" value=\"" . $_POST['mysql_prefix'] . "\" id=\"mysql_prefix\" /></td>
					<td><em>Стойността по подразбиране е \"psi_\"</em></td>
				</tr><tr><tr>
					<td>MySQL потребител за PSI CMS</td>
					<td><input type=\"text\" name=\"mysql_user\" value=\"" . $_POST['mysql_user'] . "\" id=\"mysql_user\" /></td>
					<td><em>Трябва да съществува на сървъра</em></td>
				</tr><tr>
					<td>MySQL парола за PSI CMS</td>
					<td><input type=\"text\" name=\"mysql_pass\" value=\"" . $_POST['mysql_pass'] . "\" id=\"mysql_pass\" /></td>
					<td><em>Паролата за посочения по-горе потребител</em></td>
				</tr><tr>
					<td>Името на вашия сайт</td>
					<td><input type=\"text\" name=\"site_title\" value=\"" . $_POST['site_title'] . "\" id=\"site_title\" /></td>
					<td><em>Може да бъде променено по-късно</em></td>
				</tr><tr>
					<td>Вашето потребителско име</td>
					<td><input type=\"text\" name=\"username\" value=\"" . $_POST['username'] . "\" id=\"username\" /></td>
					<td><em>Ще бъде използвано за логване в служебната зона на сайта</em></td>
				</tr><tr>
					<td>Вашата потребителска парола</td>
					<td><input type=\"text\" name=\"userpass\" value=\"" . $_POST['userpass'] . "\" id=\"userpass\" /></td>
					<td><em>Ще бъде използвана за логване в служебната зона на сайта</em></td>
				</tr>				
				</table><br />
				<input type=\"submit\" name=\"submit2\" value=\"Submit preferences\" />
				</form>
				";
		$step2_res_form = "<em>Това са настройките, въведени от вас. Проверете ги за грешки и ако има неточности - коригирайте ги. <br />
						Ако сте доволни от резултата - натиснете \"Продължете към стъпка 3\" по-долу</em><br><br>
		
						Вашия MySQL сървър: <strong>" . $mysql_server . "</strong><br />
						Вашата MySQL root парола: <strong>" . $mysql_root . "</strong><br />
						Вашата MySQL база данни: <strong>" . $mysql_db . "</strong><br />
						Вашия MySQL префикс: <strong>" . $mysql_prefix . "</strong><br />
						Вашия MySQL потребител: <strong>" . $mysql_user . "</strong><br />
						Вашата MySQL парола: <strong>" . $mysql_password . "</strong><br />
						Името на вашия PSI CMS сайт: <strong>" . $site_title . "</strong><br />
						Вашето PSI CMS потребителско име: <strong>" . $username . "</strong><br />
						Вашата PSI CMS парола: <strong>" . $userpass . "</strong><br />					
		<br />		";
		$step2_res_form_noroot = "<em>Това са настройките, въведени от вас. Проверете ги за грешки и ако има неточности - коригирайте ги. <br />
						Ако сте доволни от резултата - натиснете \"Продължете към стъпка 3\" по-долу</em><br><br>
		
						Вашия MySQL сървър: <strong>" . $mysql_server . "</strong><br />
						Вашата MySQL база данни: <strong>" . $mysql_db . "</strong><br />
						Вашия MySQL префикс: <strong>" . $mysql_prefix . "</strong><br />
						Вашия MySQL потребител: <strong>" . $mysql_user . "</strong><br />
						Вашата MySQL парола: <strong>" . $mysql_password . "</strong><br />
						Името на вашия PSI CMS сайт: <strong>" . $site_title . "</strong><br />
						Вашето PSI CMS потребителско име: <strong>" . $username . "</strong><br />
						Вашата PSI CMS парола: <strong>" . $userpass . "</strong><br />					
		<br />		";
		$step2_continue = "<a href=\"index.php?step=3\">Продължете към Стъпка 3</a>";
		$step3_menu = "Стъпка 3";
		$step3_title = "Създаване на работна среда";
		$step3_cont = "Успешно свързване с MySQL сървъра " . $mysql_server . " ...<br />
					Успешно създаване на новата MySQL база данни: " . $mysql_db . "...<br />
					Успешно създаване на новия MySQL потребител " . $mysql_user . " с парола " . $mysql_password . " ...<br />
					Успешно делегиране на всички права за база данни  " . $mysql_db . " на потребител " . $mysql_user . " ... <br />
					Успешно свързване към MySQL сървъра с името и паролата на потребител " . $mysql_user ." ...<br />
					Успешно създаване на таблиците и структурата на базата данни ...<br />
					Успешно създаване на админ потребител за PSI CMS: " . $username . " с парола: " . $userpass . " ...<br />
					<br /><strong>Всички операции в тази стъпка са изпълнени!</strong><br />
					 <div style=\"margin-top: 2em; border-top: 1px solid #000000;\"><br />
					<a href=\"index.php?step=4\">Продължете към Стъпка 4</a>
					</div>";
		$step3_cont_alt = "<strong>Всички операции в тази стъпка са изпълнени!</strong><br />
					 <div style=\"margin-top: 2em; border-top: 1px solid #000000;\"><br />
					<a href=\"index.php?step=4\">Продължете към Стъпка 4</a>
					</div>";
		$step4_menu = "Стъпка 4";
		$step4_title = "Подготовка и запис на конфигурационния файл";
		$step4_cont = "Файлът е подготвен и записан в папка \"includes\".<br />
					<div style=\"margin-top: 2em; border-top: 1px solid #000000;\"><br />
					<a href=\"index.php?step=5\">Продължете към Стъпка 5</a>
					</div>";
		$step5_menu = "Стъпка 5";
		$step5_title = "Завършване на инсталацията";
		$step5_cont = "<strong>Поздравления!</strong> <br /><br />
				Вие инсталирахте успешно PSI CMS. <br />
				Сега може да влезете в администраторския панел, използвайки своето потебителско име и парола <br />
				и да започнете да редактирате и въвеждате съдържанието на своя сайт.<br />
				<br />Не забравяйте да <strong>изтриете</strong> папката \"install\" на PSI CMS!<br/>
				<div style=\"margin-top: 2em; border-top: 1px solid #000000;\"><br />
				<a href=\"index.php?step=6\">Към админ панела</a>
				</div>";
	 

} else {
	$main_title = "PSI CMS Installer";
	$page_title = "Welcome to PSI CMS";
	$step0_cont = "<strong>PSI CMS</strong> stands for Personal Simple Intuitive content management system <br />
		It allows you to power up a simple dynamic website and to maintain it yourself without special knowledge in web development. <br />
		<br />
		This installer will guide you through the installation procedure in a few easy steps. <br />
		
		<div style=\"margin-top: 2em; border-top: 1px solid #000000;\"><br />
		=> Make sure to <strong>delete or rename</strong> the \"install\" folder of this application after the installation finishes <br />
	 	or you risk losing all your content if the installer is at some point accidentally run again
	 	</div>
		
	 	<div style=\"margin-top: 2em; border-top: 1px solid #000000;\"><br />
		 Review the Licence Agreement and continue to the next step, if you accept it:
		<br /><br />";

	$step0_cont2 ="<br><a href=\"index.php?step=1\">Continue to Step 1</a>
	 	</div>";
	 $step0_menu = "Welcome";
	 $step1_menu = "Step 1";
	 $lang_select = "<strong>Choose your language: </strong>";
	 $step1_cont = "Check if the configuration file constants.php is writeable... <br />
	 	<strong>=> Result:</strong> " . $con_message . "<br />
	 	<br />Check if your server supports output buffering... <br />
	 	<strong>=> Result:</strong> " . $ob_message . "<br />
	 	<div style=\"margin-top: 2em; border-top: 1px solid #000000;\"><br />
		<a href=\"index.php?step=1\">Test again</a>&nbsp;&nbsp;<a href=\"index.php?step=2\">Continue to Step 2</a>
		</div>	";
	 $step1_title = "System requirements test";
	 $step2_menu = "Step 2";	
	$step2_pre1 =  "Do you have root access to the MySQL server (do you know the password of the \"root\" account in MySQL)?
							<br /><br/><em>If you answer \"No\" to this question
							you'll have to provide an existing database name for this installation, and to enter the username and password 
							of an already existing MySQL user with rights to use this database.</em><br /><br />
							<em>If you answer \"Yes\", you'll have to provide the root password, and the creation of a new database
							and user will be handled automatically by this installer.</em><br /><br />
							<a href=\"index.php?step=2&root=yes\"><strong>Yes</strong></a>&nbsp;&nbsp;&nbsp;
							<a href=\"index.php?step=2&root=no\"><strong>No</strong></a>"; 
	 $step2_title = "Setting the global preferences";
	 $step2_cont_form = "<p>Fill in the form. <strong>All fields are required.</stong></p> 
	 			<form action=\"index.php?step=2&root=yes\" method=\"post\">
	 			<table>
	 			<tr>
	 				<td> Your MySQL server:</td> 
					<td><input type=\"text\" name=\"mysql_server\" value=\"" . $_POST['mysql_server'] . "\" id=\"mysql_server\" /></td>
					<td><em>Usually \"localhost\" </em></td>
				</tr><tr>
					<td>Your MySQL \"root\" password:</td>
					<td><input type=\"text\" name=\"mysql_rootp\" value=\"" . $_POST['mysql_rootp'] . "\" id=\"mysql_rootp\" /></td>
					<td><em>Will be used <strong>only</strong> for creating the database and the PSI CMS user for MySQL. Will not be stored or used after the installation completes. Leave blank if you use no password for root.</em></td>
				</tr><tr>
					<td>Database name for PSI CMS</td>
					<td><input type=\"text\" name=\"mysql_db\" value=\"" . $_POST['mysql_db'] . "\" id=\"mysql_db\" /></td>
					<td><em>Will be created by this installer</em></td>
				</tr><tr>
					<td>Database table prefix for PSI CMS</td>
					<td><input type=\"text\" name=\"mysql_prefix\" value=\"" . $_POST['mysql_prefix'] . "\" id=\"mysql_prefix\" /></td>
					<td><em>The default is \"psi_\"</em></td>
				</tr><tr>
					<td>MySQL user for PSI CMS</td>
					<td><input type=\"text\" name=\"mysql_user\" value=\"" . $_POST['mysql_user'] . "\" id=\"mysql_user\" /></td>
					<td><em>Will be created by this installer</em></td>
				</tr><tr>
					<td>MySQL pasword for PSI CMS</td>
					<td><input type=\"text\" name=\"mysql_pass\" value=\"" . $_POST['mysql_pass'] . "\" id=\"mysql_pass\" /></td>
					<td><em>Will be created by this installer</em></td>
				</tr><tr>
					<td>The name of your site</td>
					<td><input type=\"text\" name=\"site_title\" value=\"" . $_POST['site_title'] . "\" id=\"site_title\" /></td>
					<td><em>Can be changed later</em></td>
				</tr><tr>
					<td>Your username</td>
					<td><input type=\"text\" name=\"username\" value=\"" . $_POST['username'] . "\" id=\"username\" /></td>
					<td><em>Will be used for logging in the staff area</em></td>
				</tr><tr>
					<td>Your user password</td>
					<td><input type=\"text\" name=\"userpass\" value=\"" . $_POST['userpass'] . "\" id=\"userpass\" /></td>
					<td><em>Will be used for logging in the staff area</em></td>
				</tr>				
				</table><br />
				<input type=\"submit\" name=\"submit2\" value=\"Submit preferences\" />
				</form>
				";
		$step2_cont_form_noroot = "<p>Fill in the form. <strong>All fields are required.</stong></p> 
	 			<form action=\"index.php?step=2&root=no\" method=\"post\">
	 			<table>
	 			<tr>
	 				<td> Your MySQL server:</td> 
					<td><input type=\"text\" name=\"mysql_server\" value=\"" . $_POST['mysql_server'] . "\" id=\"mysql_server\" /></td>
					<td><em>Usually \"localhost\" </em></td>
				</tr><tr>
					<td>Database name for PSI CMS</td>
					<td><input type=\"text\" name=\"mysql_db\" value=\"" . $_POST['mysql_db'] . "\" id=\"mysql_db\" /></td>
					<td><em>Must already exist</em></td>
				</tr><tr>
					<td>Database table prefix for PSI CMS</td>
					<td><input type=\"text\" name=\"mysql_prefix\" value=\"" . $_POST['mysql_prefix'] . "\" id=\"mysql_prefix\" /></td>
					<td><em>The default is \"psi_\"</em></td>
				</tr><tr>
					<td>MySQL user for PSI CMS</td>
					<td><input type=\"text\" name=\"mysql_user\" value=\"" . $_POST['mysql_user'] . "\" id=\"mysql_user\" /></td>
					<td><em>Must already exist</em></td>
				</tr><tr>
					<td>MySQL pasword for PSI CMS</td>
					<td><input type=\"text\" name=\"mysql_pass\" value=\"" . $_POST['mysql_pass'] . "\" id=\"mysql_pass\" /></td>
					<td><em>For the existing user filled in above</em></td>
				</tr><tr>
					<td>The name of your site</td>
					<td><input type=\"text\" name=\"site_title\" value=\"" . $_POST['site_title'] . "\" id=\"site_title\" /></td>
					<td><em>Can be changed later</em></td>
				</tr><tr>
					<td>Your username</td>
					<td><input type=\"text\" name=\"username\" value=\"" . $_POST['username'] . "\" id=\"username\" /></td>
					<td><em>Will be used for logging in the staff area</em></td>
				</tr><tr>
					<td>Your user password</td>
					<td><input type=\"text\" name=\"userpass\" value=\"" . $_POST['userpass'] . "\" id=\"userpass\" /></td>
					<td><em>Will be used for logging in the staff area</em></td>
				</tr>				
				</table><br />
				<input type=\"submit\" name=\"submit2\" value=\"Submit preferences\" />
				</form>
				";
		$step2_res_form_noroot = "<em>These are the preferences you entered. Double check them, correct if any are wrong, <br />
						and when you're satisfied with the result - click on \"Continue to Step 3\" below</em><br><br>
		
						Your MySQL server: <strong>" . $mysql_server . "</strong><br />
						Your MySQL database: <strong>" . $mysql_db . "</strong><br />
						Your MySQL table prefix: <strong>" . $mysql_prefix . "</strong><br />
						Your MySQL user: <strong>" . $mysql_user . "</strong><br />
						Your MySQL user password: <strong>" . $mysql_password . "</strong><br />
						Your PSI CMS site name: <strong>" . $site_title . "</strong><br />
						Your PSI CMS username: <strong>" . $username . "</strong><br />
						Your PSI CMS password: <strong>" . $userpass . "</strong><br />					
		<br />		";
		$step2_res_form = "<em>These are the preferences you entered. Double check them, correct if any are wrong, <br />
						and when you're satisfied with the result - click on \"Continue to Step 3\" below</em><br><br>
		
						Your MySQL server: <strong>" . $mysql_server . "</strong><br />
						Your MySQL root password: <strong>" . $mysql_root . "</strong><br />
						Your MySQL database: <strong>" . $mysql_db . "</strong><br />
						Your MySQL table prefix: <strong>" . $mysql_prefix . "</strong><br />
						Your MySQL user: <strong>" . $mysql_user . "</strong><br />
						Your MySQL user password: <strong>" . $mysql_password . "</strong><br />
						Your PSI CMS site name: <strong>" . $site_title . "</strong><br />
						Your PSI CMS username: <strong>" . $username . "</strong><br />
						Your PSI CMS password: <strong>" . $userpass . "</strong><br />					
		<br />		";
		$step2_continue = "<a href=\"index.php?step=3\">Continue to Step 3</a>";
		$step3_menu = "Step 3";
		$step3_title = "Creating the working environment";
		$step3_cont = "Successfully connected to the MySQL server " . $mysql_server . " ...<br />
					Successfully created the new MySQL database: " . $mysql_db . "...<br />
					Successfully created the new MySQL user " . $mysql_user . " with password " . $mysql_password . " ...<br />
					Successfully granted all priviliges on database " . $mysql_db . " to user " . $mysql_user . " ... <br />
					Successfully reconnected to the MySQL server with the credentials of user " . $mysql_user ." ...<br />
					Successfully created the tables and database structure ...<br />
					Successfully created admin user for PSI CMS: " . $username . " with password: " . $userpass . " ...<br />
					
					<br /><strong>All operations in this step completed!</strong><br />
					 <div style=\"margin-top: 2em; border-top: 1px solid #000000;\"><br />
					<a href=\"index.php?step=4\">Continue to Step 4</a>
					</div>";
		$step3_cont_alt = "<strong>All operations in this step completed!</strong><br />
					 <div style=\"margin-top: 2em; border-top: 1px solid #000000;\"><br />
					<a href=\"index.php?step=4\">Continue to Step 4</a>
					</div>";
		$step4_menu = "Step 4";
		$step4_title = "Constructing and saving the configuration file";
		$step4_cont = "The file is saved in the  \"includes\" folder of PSI CMS.<br />
					<div style=\"margin-top: 2em; border-top: 1px solid #000000;\"><br />
					<a href=\"index.php?step=5\">Continue to Step 5</a>
					</div>";
		$step5_menu = "Step 5";
		$step5_title = "Finish the installation";
		$step5_cont = "<strong>Congratulations!</strong> <br /><br />
						You have successfully installed PSI CMS. <br />
						You can now go to the Staff Area, log in with your username and password, and start managing your site.<br />
						<br />Don't forget to <strong>delete</strong> the \"install\" folder of PSI CMS!<br/>
						<div style=\"margin-top: 2em; border-top: 1px solid #000000;\"><br />
						<a href=\"index.php?step=6\">Go to Staff Area</a>
						</div>";
}

 ?>
 
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf8" />
		<title><?php echo $main_title; ?></title>
		<link href="../stylesheets/public.css" media="all" rel="stylesheet" type="text/css" />
	</head>
	<body>
		<div id="header">
			<h1><?php echo $main_title; ?></h1>
		</div>
		<div id="main">
		<table id="structure">
		<tr>
		<td id="navigation">
			<ul class="subjects">
				<li <?php if (!isset ($step)) {
					echo "class=\"selected\"" ;
				} ?>
				><?php echo $step0_menu; ?></li>
				<li <?php if ($step == 1) {
					echo "class=\"selected\"" ;
				} ?>
				><?php echo $step1_menu; ?></li>
				<li <?php if ($step == 2) {
					echo "class=\"selected\"" ;
				} ?>
				><?php echo $step2_menu; ?></li>
				<li <?php if ($step == 3) {
					echo "class=\"selected\"" ;
				} ?>
				><?php echo $step3_menu; ?></li>
				<li <?php if ($step == 4) {
					echo "class=\"selected\"" ;
				} ?>
				><?php echo $step4_menu; ?></li>
				<li <?php if ($step == 5) {
					echo "class=\"selected\"" ;
				} ?>
				><?php echo $step5_menu; ?></li>
			</ul>
		</td>

		<td id="page">
		<?php
		if (!isset($step)) {
		 	echo "<div align=\"right\"><BR />" . $lang_select . "<a href=\"index.php?lang=bg\"><IMG src=\"../images/bg.jpg\"></a>&nbsp;&nbsp;<a href=\"index.php?lang=us\"><IMG src=\"../images/usa.jpg\"></a>&nbsp;&nbsp;</div>";
		}
		?>	
			<h2>
				<?php
					// Display page title for each step
					if (!isset($step)) {
						echo $page_title;
					} elseif ($step == 1) {
						echo $step1_title;
					} elseif ($step == 2) {
						echo $step2_title;
					} elseif ($step == 3) {
						echo $step3_title;
					} elseif ($step == 4) {
						echo $step4_title;
					} elseif ($step == 5) {
						echo $step5_title;
					}
					
				?>
			</h2>
				<div class="page-content">
					<?php
					// Display page content
					if (!isset($step)) {
					$licence = file_get_contents('../LICENCE');
					//$ttt = print_r($licence);
					echo $step0_cont . "<textarea rows=\"14\" cols=\"100\">". $licence . "</textarea><br />" . $step0_cont2;
					} elseif ($step == 1) {
						echo $step1_cont;
					} elseif ($step == 2) {
						if ($_GET['root'] == "yes") {
							if($_POST['submit2'] != "Submit preferences" ) {
								echo $step2_cont_form;
							} else {
								echo $step2_res_form . $step2_cont_form . "&nbsp;&nbsp;" .$step2_continue;
							}
						} elseif ($_GET['root'] == "no") {
							if($_POST['submit2'] != "Submit preferences" ) {
								echo $step2_cont_form_noroot;
							} else {
								echo $step2_res_form_noroot. $step2_cont_form_noroot . "&nbsp;&nbsp;" .$step2_continue;
							}						
						} else {
							echo $step2_pre1;
						}
					
					} elseif ($step == 3) {
						if ($isroot == "yes") {
							echo $step3_cont;
						} else {
							echo $step3_cont_alt;
						}
					} elseif ($step == 4) {
						echo $step4_cont;
					} elseif ($step == 5) {
						echo $step5_cont;
					}
					?>
				</div>
			
		</td>
	</tr>
</table>
		</div>
		<div id="footer">Copyright 2007-2009, <a href="http://psi.bgnetwork.net"><font color="#EEE4B9">PSI CMS</font></a>, v. 0.4</div>
	</body>
</html>
<?php
//  Close DB connection
	if (isset($connection)) {
		mysql_close($connection);
	}
?>
