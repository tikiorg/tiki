-- Ensure that PermName is no longer than 36 characters, since the maximum allowed
-- by MySQL Full Text Search as Unified Search Index is 50, and trackers will internally prepend
-- "tracker_field_", which are another 14 characters (36+14=50). We could allow longer permanent names when other
-- search index engines are the ones being used, but this will probably only delay the problem until the admin
-- wants to change the search engine for some reason (some constrains in Lucene or Elastic Search,
-- as experience demonstrated in some production sites in real use cases over long periods of time).
-- And to increase chances to avoid conflict when long names only differ in the end of the long string,
-- where some meaningful info resides, we'll get the first 26 chars, 1 underscore and the last 9 chars.
--
--  Using same arbitrary convention as added in the code here: https://sourceforge.net/p/tikiwiki/code/59200
--
-- This upgrade script is not placed under the installer/schema folder by default since it might potentially
-- break some installations upon upgrade if they were using such long permanent names for real.
-- Therefore, we leave this upgrade script here under doc/devtools/ by default, so that each admin can
-- move it to installer/schema in their own install at their own risk.
UPDATE `tiki_tracker_fields`
SET permName = REPLACE(permName,
                       permName,
                       (SELECT CONCAT( LEFT(permName, 26), '_', RIGHT(permName, 9)))
                      )
WHERE char_length(permName) > 36


