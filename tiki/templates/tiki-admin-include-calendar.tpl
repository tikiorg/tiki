<div class="cbox">
<div class="cbox-title">{tr}Calendar{/tr}</div>
<div class"cbox-data">
<div class="simplebox">
<form action="tiki-admin.php?page=calendar" method="post">
<table class="admin">
<tr class="form">
<td><label>{tr}Group calendar sticky popup{/tr}</label></td>
<td><input type="checkbox" name="calendar_sticky_popup" {if $calendar_sticky_popup eq 'y'}checked="checked"{/if}/></td>
</tr>
<tr class="form">
<td><label>{tr}Group calendar item view tab{/tr}</label></td>
<td><input type="checkbox" name="calendar_view_tab" {if $calendar_view_tab eq 'y'}checked="checked"{/if}/></td>
</tr>
<tr>
<td colspan="2" class="button"><input type="submit" name="calprefs" value="{tr}Change preferences{/tr}" /></td>
</tr>
</table>
</form>
</div>
</div>
</div>