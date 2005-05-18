{*
{if $feature_help eq 'y'}
<div class="rbox" style="margin-top: 10px;">
<div class="rbox-title" style="background-color: #CCCCDD; font-weight : bold; display : inline; padding : 0 10px;" name="note">{tr}Note{/tr}</div>
<div class="rbox-data" style="padding: 2px 10px; background-color: #CCCCDD;" name="note">{tr}This feature does not currently have any web administration configuration{/tr}</div>
</div>
<br />
{/if}
*}
<div class="cbox">
<div class="cbox-title">
  {tr}{$crumbs[$crumb]->description}{/tr}
  {help crumb=$crumbs[$crumb]}
</div>
<div class="cbox-data">
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
<td colspan="2" class="button"><input type="submit" name="calprefs" value="{tr}Change settings{/tr}" /></td>
</tr>
</table>
</form>
</div>
</div>
