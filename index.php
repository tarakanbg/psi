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

// Uncomment the line below to debug the script
// error_reporting(E_ALL);

// Are we installed? If no - launch the installer
require_once("modules/mod_psi_installed.php");

// Make some crucial includes first
require_once("includes/functions.php");
require_once("includes/connection.php");

// Now let's find the language for the frontend
require_once("modules/mod_frontend_lang.php");

//Where were we? On which page or content item?
require_once("modules/mod_orientation.php");

// Now let's go back to html and draw some structure
?>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=8" />
		<meta name="generator" content="PSI CMS - http://psi.bgnetwork.net" />
		<?php // SEO features follow - title, decription and keywords
		require_once("modules/mod_head_seo.php");
		?>
		<link href="stylesheets/public.css" media="all" rel="stylesheet" type="text/css" />
	</head>
	<body>
		<div id="header">
			<table width="100%">
				<tr>
					<td id="header-left" align="left">
					<?php load_modules($location="header-left"); ?>
					</td>
					<td id="header-center" align="center">
					<?php load_modules($location="header-center"); ?>
					</td>
					<td id="header-right" align="right">
					<?php load_modules($location="header-right"); ?>
					</td>
				</tr>
			</table>			
		</div>
		<div id="main">
			<table id="structure">
				<tr>
					<td id="top-left" align="left">
					<?php load_modules($location="top-left"); ?>
					</td>
					<td id="top-center">
					<?php load_modules($location="top-center"); ?>
					</td>
					<td id="top-right" align="right">
					<?php load_modules($location="top-right"); ?>
					</td>
				</tr>
				<tr>
					<td id="navigation">
					<?php load_modules($location="left"); ?>
					</td>
					<td id="page">
						<table>
							<tr>
								<td id="center-left">
								<?php load_modules($location="center-left"); ?>
								</td>
								<td id="center-right">
								<?php load_modules($location="center-right"); ?>
								</td>
							</tr>
						</table>
					<?php load_modules($location="center"); ?>
					</td>
					<td id="right" align="right">
					<?php load_modules($location="right"); ?>
					</td>
				</tr>
			</table>
		</div>
		<div id="footer">
			<table width="100%" align="center" id="footer" margin-top="10px">
				<tr>
					<td id="footer-left" width="30%" align="left">
					<?php load_modules($location="footer-left"); ?>
					</td>
					<td id="footer-center" width="40%" align="center" text-align="center">
					<?php load_modules($location="footer-center"); ?>
					</td>
					<td id="footer-right" width="30%" align="right">
					<?php load_modules($location="footer-right"); ?>
					</td>
				</tr>
			</table>
		</div>
	</body>
</html>
<?php
// Close connection
mysql_close($connection);
?>

