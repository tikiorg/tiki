{title}{tr}Calendar import{/tr}{/title}

<div class="navbar">
{button _text="{tr}View Calendars{/tr}" href="tiki-calendar.php"}
{button _text="{tr}Admin Calendars{/tr}" href="tiki-admin_calendars.php"}
</div>

{if $updated}
  {tr}Calendar has been updated{/tr}
{/if}

<form method="post" action="tiki-calendar_import.php" enctype="multipart/form-data">
  <table class="normal">
    <tr>
      <td class="formcolor">{tr}Calendar{/tr}</td>
      <td class="formcolor">
        <select name="calendarId">
          {foreach item=lc from=$calendars}
            <option value="{$lc.calendarId}">{$lc.name|escape}</option>
          {/foreach}
        </select>
      </td>
    </tr>
    <tr>
      <td class="formcolor">
        {tr}CSV File{/tr}
        <a {popup text='name|subject,description,start date,start time,end date,end time,status,lang,categoryId,locationId,priority,url,categoryId'}>{icon _id='help'}</a>
      </td>
      <td class="formcolor">
        <input type="file" name="fileCSV" size="50" />
        <input type="submit" name="import" value="{tr}import{/tr}" />
      </td>
    </tr>
  </table>
</form>

