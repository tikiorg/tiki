<div class="cbox">
<div class="cbox-title">{tr}Maps{/tr}</div>
<div class="cbox-data">
<div class="simplebox">
<form action="tiki-admin.php?page=maps" method="post">
<table class="admin">
<tr><td class="form">{tr}Full path to mapfiles{/tr}:</td><td><input type="text" name="map_path" value="{$map_path|escape}" size="50" /></td></tr>
<tr><td class="form">{tr}Default mapfile{/tr}:</td><td><input type="text" name="default_map" value="{$default_map}" /> </td></tr>
<tr><td class="form">{tr}Wiki Page for Help{/tr}:</td><td><input type="text" name="map_help" value="{$map_help}" /> </td></tr>
<tr><td class="form">{tr}Wiki Page for Comments{/tr}:</td><td><input type="text" name="map_comments" value="{$map_comments}" /> </td></tr>
<tr><td class="form">{tr}Full path to gdaltindex{/tr}:</td><td><input type="text" name="gdaltindex" value="{$gdaltindex}" size="50" /> </td></tr>
<tr><td colspan="2" class="button"><input type="submit" name="mapsset" value="{tr}Change Preferences{/tr}" /></td></tr> 
{if $map_error neq ''}
<tr><td align="center" colspan="2">{$map_error}</td></tr>
{/if}   
</table>
</form>
</div>
</div>
</div>