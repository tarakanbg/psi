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

// We don't know yet what the server supports - 
// so lets start output buffering manually
ob_start();

// We need this to get the install flag
include_once("includes/constants.php");

// Ler's get some functions going
include_once("includes/functions.php");

//Are we installed?
if (PSI_INSTALLED == "no") {
	// A connection was opened before? Or wasn't it? - If yes, let's close it
	if (isset($connection)) {
		mysql_close($connection);
	}
	// We're not installed - launch the installer
	redirect_to("install/index.php");
}
// Flush the buffer and output the result
ob_flush()
?>
