<div class="cbox">
<div class="cbox-title">{tr}Maps{/tr}</div>
<div class="cbox-data">
<div class="simplebox">
<form action="tiki-admin.php?page=maps" method="post">
<table width="100%">
<tr><td class="form">{tr}full path to mapfiles{/tr}:</td><td><input type="text" name="map_path" value="{$map_path|escape}" /></td></tr>
<tr><td class="form">{tr}default mapfile{/tr}:</td><td><input type="text" name="default_map" value="{$default_map}" /> </td></tr>
<tr><td align="center" colspan="2"><input type="submit" name="mapsset" value="{tr}Set prefs{/tr}" /></td></tr>    
</table>
</form>
</div>
</div>
</div>