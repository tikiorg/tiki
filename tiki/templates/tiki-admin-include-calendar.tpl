<div class="cbox">
<div class="cbox-title">{tr}Calendar{/tr}</div>
<div class"cbox-data">
<div class="simplebox">
<form action="tiki-admin.php?page=calendar" method="post">
<table class="admin">
<tr class="form">
<td><label>{tr}Users see and add events in their timezone{/tr}</label></td>
<td><input type="checkbox" name="calendar_timezone" {if $calendar_timezone eq 'y'}checked="checked"{/if}/></td>
</tr>
<tr>
<td colspan="2" class="button"><input type="submit" name="calprefs" value="{tr}Change preferences{/tr}" /></td>
</tr>
</table>
</form>
</div>
</div>
</div>