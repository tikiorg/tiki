TikiWiki - CMS/Groupware

README for /db directory
========================

This directory contains a multitude of database-releated files.


developer information
=====================

When adding, changing or removing SQL statements from Tiki, please update tiki.sql and the appropriate conversion file (e.g. tiki_1.9to1.10.sql for changes added between release 1.9 and 1.10 of TikiWiki).

Please remember that the database schema is frozen after first official release of each major version.

The database-specific versions of the sql files are generated automatically using the convertsqls.sh script found inside the converscripts subdirectory.


caveats
=======
In theory, the database structure is not supposed to change in minor releases (ex.: 1.9.1 to 1.9.2). However, some bugs were found which made it necessary to modify the database structure. And once the database structure was no longer frozen, a few more settings for features were added.


security information
====================
tiki-secdb*.sql contain security checksums for the files that make up each major release


** End **
