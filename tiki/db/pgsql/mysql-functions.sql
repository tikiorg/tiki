--
-- Some functions to transition Mysql SQL to PostgreSQL
-- 
-- Created: 2000-11-01
-- Last updated: 2003-06-12
--
-- Zachary Beane <xach@xach.com>
--
-- This file is in the public domain.
--


drop function ifnull (text, text);
create function ifnull (text, text) returns text AS '
select coalesce($1, $2) as result
' language 'sql';


drop function ifnull (int4, int4);
create function ifnull (int4, int4) returns int4 as '
select coalesce($1, $2) as result
' language 'sql';


--
-- from_unixtime
--
-- Takes a seconds-since-the-epoch integer and returns a timestamp
--

drop function from_unixtime(integer);
create function from_unixtime(integer) returns timestamp as '
       select abstime($1) as result
' language 'sql';


--
-- unix_timestamp
--
-- Takes a timestamp and returns the seconds-since-the-epoch for it
--

drop function unix_timestamp(timestamp);
create function unix_timestamp(timestamp) returns integer as '
       select date_part(''epoch'', $1)::int4 as result
' language 'sql';


--
-- to_days
--
-- Convert a timestamp to an integer representing a day count
--

drop function to_days(timestamp);
create function to_days(timestamp) returns integer as '
       select date_part(''day'', $1 - ''0000-01-01'')::int4 as result
' language 'sql';


--
-- from_days
--
-- Convert a day count returned from from_days to a timestamp
--

drop function from_days(integer);
create function from_days(integer) returns timestamp as '
       select ''0000-01-02''::timestamp + ($1 || '' days'')::interval as result
' language 'SQL';


--
-- convert_date_format
--
-- Convert mysql's date_format string to a postgresql to_char compatible string
--
-- BE WARNED! If your date format string contains valid to_char
-- substitutions, you will get unexpected results. For example:
-- 
-- test=# select date_format(now(), '%Y-%m-%d is today.');
--         date_format         
-- ----------------------------
--  2001-04-10 is totuesday  .
-- (1 row)
--
-- Since the string "day" is substituted by to_char, it puts today's
-- day name into the output string.
--

drop function convert_date_format(text);
create function convert_date_format(text) 
returns text 
as '
   set old_format $1
   array set substitutions {%% %
		      %M Month
		      %W Day
		      %D FMDDth
		      %Y YYYY
		      %y YY
		      %X ""
		      %x ""
		      %a Dy
		      %d DD
		      %e FMDD
		      %m MM
		      %c FMmm
		      %b Mon
		      %j DDD
		      %H HH24
		      %k FMHH24
		      %h HH12
		      %I HH12
		      %l FMHH12
		      %i MI
		      %r {HH12:MI:SS AM}
		      %T HH24:MI:SS
		      %S SS
		      %s SS
		      %p AM}


   # Iterate through characters of $old_format and replace any % escapes.

   set string_size [string length $old_format]
   set i 0
   set new_format ""

   while { $i < $string_size } {
         set fchar [string index $old_format $i]
	 if { $fchar == "%" } {
            set code [string range $old_format $i [expr $i + 1]]
            if [info exists substitutions($code)] {
               append new_format $substitutions($code)
               incr i
            } else {
               append new_format $fchar
            }
         } else {
           append new_format $fchar
         }
         
         incr i
   }
   return $new_format
' language 'pltcl';		      


--
-- date_format
--
-- Produce pretty output for a timestamp
--

drop function date_format(timestamp, text);
create function date_format(timestamp, text)
returns text
as '
   select to_char($1, convert_date_format($2))
' language 'sql';


