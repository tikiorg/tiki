<form action="tiki-admin.php?page=maps" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="mapsset" value="{tr}Change preferences{/tr}" />
	</div>
<fieldset class="admin">
<legend>{tr}Settings{/tr}</legend>
<table class="admin">
<tr><td align="center" colspan="2"><font color="red">{$map_error}</font></td></tr>
<tr><td class="form">{tr}full path to mapfiles{/tr}:</td><td><input type="text" name="map_path" value="{$prefs.map_path|escape}" size="50" /></td></tr>
<tr><td class="form">{tr}default mapfile{/tr}:</td><td><input type="text" name="default_map" value="{$prefs.default_map}" /> </td></tr>
<tr><td class="form">{tr}Wiki Page for Help{/tr}:</td><td><input type="text" name="map_help" value="{$prefs.map_help}" /> </td></tr>
<tr><td class="form">{tr}Wiki Page for Comments{/tr}:</td><td><input type="text" name="map_comments" value="{$prefs.map_comments}" /> </td></tr>
<tr><td class="form">{tr}Full path to gdaltindex{/tr}:</td><td><input type="text" name="gdaltindex" value="{$prefs.gdaltindex}" size="50" /> </td></tr>
<tr><td class="form">{tr}Full path to ogr2ogr{/tr}:</td><td><input type="text" name="ogr2ogr" value="{$prefs.ogr2ogr}" size="50" /> </td></tr>
<tr><td class="form">{tr}Map Zone{/tr}:</td><td>
{html_radios name="mapzone" options=$checkboxes_mapzone selected=$prefs.mapzone separator="  "}
</td></tr>
{if $map_error neq ''}
{/if}   
</table>
		<div class="heading input_submit_container" style="text-align: center">
			<input type="submit" name="mapsset" value="{tr}Change preferences{/tr}" />
			<input type="submit" name="mapuser" value="{tr}Generate User Map{/tr}" />
		</div>
	</fieldset>
</form>
