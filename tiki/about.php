<?php

/*
  this page is used as doxygen output mainpage,
  content is pasted in from README.
*/

/**
\mainpage

TikiWiki

DOCUMENTATION

* It is highly recommended you refer to the online documentation:
* http://tikiwiki.org/InstallTiki for a setup guide
* http://tikiwiki.org/InstallTikiHelp for what to do in case of problems
* It might also be helpful to look into the official Manual. Last released
  documentation, in pdf format (350 pages) (outdated at the time of this writing):
  http://tikiwiki.org/tiki.pdf (version 1.6 but with many valuable help)

* The documentation for all versions is maintained on http://doc.tikiwiki.org,
  come and help if you can and wish.

* Notes about the releases are accessible from http://tikiwiki.org/ReleaseProcess19
* TikiWiki is also a live community on irc.freenode.net channel #tikiwiki


INSTALLATION

* There is a file INSTALL in this directory with notes on how to setup and
  configure Tiki. Again, see http://tikiwiki.org/InstallTiki for the latest install help.


COPYRIGHT

Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.All
Rights Reserved. See copyright.txt for details and a complete list of authors.
Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

... Have fun!
*/

// I call index.php because tiki may not be setup when people attempt to call this.
	header ("location: index.php");
	die;

?>