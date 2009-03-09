-------------------------
JQuery/Tiki readme
-------------------------
jonnybradley March 2009
- - - - - - - - - - - - -

This directory /lib/jquery/ contains the JQuery library and selected plugins for TikiWiki 3.0

The suggested layout is as follows:

	JQuery itself on the root
	JS files taken from the release zip (currently jquery-1.3.2-release.zip) /dist/ directory
		jquery.js (readable version for debugging)
		jquery.min.js	(minified version for production)

Plugins and other additions are added in their default named directories (with version numbers removed where applicable).
These are added generally complete and without modification - such as jquery.ui/ and jquery-autocomplete/
(note: removed plugin "demo" and "test" dirs now, saves another 10MB and several hundred more files)

Duplicate files, such as other copies of jquery.js etc should be removed to avoid conflicts. Demos could also be removed if large.

The dir /lib/jquery_tiki/ is used for custom files for connect Tiki to JQuery such as tiki-jquery.js.

JQuery runs at the moment in "compatibility mode" to monimise conflicts with MooTools - so you have to use $jq or jQuery to access the object.
