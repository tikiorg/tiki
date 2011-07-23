{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}You can see users rank by score in the module users_rank, for that go to{/tr} "<a class="rbox-link" href="tiki-admin_modules.php">{tr}Admin modules{/tr}</a>".{/remarksbox}

<form action="tiki-admin.php?page=score" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="scoreevents" value="{tr}Save{/tr}" />
	</div>
<fieldset class="admin">
<legend>{tr}Settings{/tr}</legend>
<table class="admin">
<tr>
  <td style="padding-left:5px"></td>
  <td></td>
  <td><b>{tr}Points{/tr}</b></td>
  <td><b>{tr}Expiration{/tr}</b></td>
</tr>

{section name=e loop=$events}
{if $events[e].category != $categ}
  {assign var=categ value=$events[e].category}
<tr>
  <td colspan="4"><b>{tr}{$events[e].category}{/tr}</b></td>
</tr>
{/if}

<tr>
  <td></td>
  <td>{tr}{$events[e].description}{/tr}</td>
  <td>
    <input type="text" size="3" name="events[{$events[e].event}][score]" value="{$events[e].score}" />
  </td>
  <td>
    <input type="text" size="4" name="events[{$events[e].event}][expiration]" value="{$events[e].expiration}" />
  </td>
</tr>
{/section}
</table>
</fieldset>
	<div class="heading input_submit_container" style="text-align: center">
		<input type="submit" name="scoreevents" value="{tr}Save{/tr}" />
	</div>
</form>
