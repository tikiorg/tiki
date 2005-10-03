TikiWIki - CMS/Groupware

README for /db directory
========================

This directory contains a multitude of database-releated files.


developer information
=====================

When adding, changing or removing SQL statements from Tiki, please update tiki.sql and the appropriate conversion file (e.g. tiki_1.9to1.10.sql for chhanges added between release 1.9 and 1.10 of TikiWiki.

Please remember that the database schema is frozen after first official release of each major version.

The database-specific versions of the sql files are generated automatically using the convertsqls.sh script found inside the converscripts subdirectory.


caveats
=======
An oversight lead to an inconsistent schema for TikiWIki 1.9.0 which was fixed in 1.9.1.  For all intents and purposes, 1.9.0 should be view historically as a release candidate and other 1.9.x releases declair their schema as version 1.9.1


security information
====================
tiki-secdb*.sql contain security checksums for the files that make up each major release


** End **
