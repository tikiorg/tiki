Tikiwiki DevTools 
-----------------
$Id$

The content of this directory is intended to be used for 
Tikiwiki development. It's included in the package for use
of developers whose task is to release new versions of tikiwiki.

Main tools
==========

* tikiwiki.spec
  made by dheltzel
  script to build a rpm package from tarball release.

* lastcvs.sh
  made by mose
  script to build a tarbal from devel cvs using devel account

* tikirelease.sh
  made by mose
	script for lazy release manager. edit it before use !!

* ggg-trace.php
  made by George G. Geller
  For tracing and debugging php code -- output a text file.


Helper tools
============

* findstyledef.sh, findstyledef.pl
  made by ohertel
  Provide a report on CSS classes referenced in tpl and php files

* csscheck.sh, stripbraces.pl, stripcomments.pl
  made by mdavey
  Provide a report on CSS classes used in a stylesheet
  See http://tikiwiki.org/RecipeRestoreCss for details

* cvsup.sh
  made by mose
  Perform a cvs up and create files so that tiki can display date in footer.tpl


