<form action="tiki-admin.php?page=userfiles" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="userfilesprefs" value="{tr}Change preferences{/tr}" />
	</div>
	<fieldset class="admin">
		<legend>{tr}Settings{/tr}</legend>
		<table class="admin">
			<tr>
				<td>{tr}Quota (Mb){/tr}</td>
				<td>
					<input type="text" name="userfiles_quota" value="{$prefs.userfiles_quota|escape}" size="5" />
				</td>
			</tr>
			<tr>
				<td>{tr}Use database to store userfiles{/tr}:</td>
				<td>
					<input type="radio" name="uf_use_db" value="y" {if $prefs.uf_use_db eq 'y'}checked="checked"{/if}/>
				</td>
			</tr>
			<tr>
				<td>{tr}Use a directory to store userfiles{/tr}:</td>
				<td>
					<input type="radio" name="uf_use_db" value="n" {if $prefs.uf_use_db eq 'n'}checked="checked"{/if}/> {tr}Path{/tr}:
					<br />
					<input type="text" name="uf_use_dir" value="{$prefs.uf_use_dir|escape}" size="50" />
				</td>
			</tr>
		</table>
	</fieldset>
	<div class="heading input_submit_container" style="text-align: center">
 		<input type="submit" name="userfilesprefs" value="{tr}Change preferences{/tr}" />
	</div>
</form>
