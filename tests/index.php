<?php
// $Id$

// This is a part of TikiWiki CMS/Groupware -- LGPL licensed Free / Open Source Software
// Copyright © 2002-2008 Luis Argerich, Garland Foster, Eduardo Polidor and many others
// See the README file for details...

/// just redirect to the site's root to prevent directory browsing

header ("location: ../tiki-index.php");
die;
?>