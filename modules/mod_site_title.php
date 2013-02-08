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
// This module constructs the site title on top
// of the title page
$site_name = $_SESSION['site_name'];
?>
<h1><?php echo "<a href=\"index.php\" class=\"site-title\">" . $site_name . "</a>"; ?></h1>
