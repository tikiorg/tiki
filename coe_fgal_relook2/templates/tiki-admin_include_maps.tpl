<form action="tiki-admin.php?page=maps" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="mapsset" value="{tr}Change preferences{/tr}" />
	</div>
	<fieldset class="admin">
		<legend>{tr}Settings{/tr}</legend>
		<div class="adminoptionbox">
			{if $map_error neq ''}
				{remarksbox type=warning title="{tr}Warning{/tr}"}{$map_error}{/remarksbox}
			{/if}
			{preference name=map_path}
			{preference name=default_map}
			{preference name=map_help}
			{preference name=map_comments}
			{preference name=gdaltindex}
			{preference name=ogr2ogr}
			{preference name=mapzone}
		</div>

		<div class="heading input_submit_container" style="text-align: center">
			<input type="submit" name="mapsset" value="{tr}Change preferences{/tr}" />
			<input type="submit" name="mapuser" value="{tr}Generate User Map{/tr}" />
		</div>
	</fieldset>
</form>
