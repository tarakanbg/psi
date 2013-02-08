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

// It's a function call to load the vertical
// public navigation module for the structured
// content
global $sel_subject;
global $sel_page;
global $sel_subpage;
echo public_navigation($sel_subject, $sel_page, $sel_subpage);
?>
