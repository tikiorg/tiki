<h1><a href="tiki-calendar_import.php" class="pagetitle">{tr}Calendar import{/tr}</a></h1>
{popup_init src="lib/overlib.js"}

{if $updated}
{tr}Calendar has been updated{/tr}
{/if}
<form method="post" action="tiki-calendar_import.php" enctype="multipart/form-data">
<table class="normal">
<tr><td class="formcolor">{tr}Calendar{/tr}</td><td class="formcolor">
<select name="calendarId">
{foreach item=lc from=$calendars}
<option value="{$lc.calendarId}">{$lc.name|escape}</option>
{/foreach}
</select>
</td></tr>
<tr><td class="formcolor">{tr}CSV File{/tr}<a {popup text='name|subject,description,(start date,start time)|start,(end date,end time)|end,status,lang,categoryId,locationId,priority,url,categoryId'}>{icon _id='help'}</a>
</td><td class="formcolor">
<input type="file" name="fileCSV" size="50" />
<input type="submit" name="import" value="{tr}import{/tr}" />
</td></tr></table>
</form>

