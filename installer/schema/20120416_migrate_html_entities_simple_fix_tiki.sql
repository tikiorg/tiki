#Start with pages
UPDATE tiki_pages SET data = REPLACE(data, '&quot;' , '"');
UPDATE tiki_pages SET data = REPLACE(data, '&gt;' , '>');
UPDATE tiki_pages SET data = REPLACE(data, '&lt;' , '<');
UPDATE tiki_pages SET data = REPLACE(data, '&amp;' , '&');

#Now with pages history
UPDATE tiki_history SET data = REPLACE(data, '&quot;' , '"');
UPDATE tiki_history SET data = REPLACE(data, '&gt;' , '>');
UPDATE tiki_history SET data = REPLACE(data, '&lt;' , '<');
UPDATE tiki_history SET data = REPLACE(data, '&amp;' , '&');