<div class="cbox">
	<div class="cbox-title">{tr}User files{/tr}</div>
		<div class="cbox-data">
			<div class="simplebox">
				<form action="tiki-admin.php?page=userfiles" method="post">
				<table width="100%">
				<tr><td class="form">{tr}Quota (Mb){/tr}</td><td>
				<input type="text" name="userfiles_quota" value="{$userfiles_quota|escape}" />
				</td></tr>
  				<tr><td class="form">{tr}Use database to store userfiles{/tr}:</td><td><input type="radio" name="uf_use_db" value="y" {if $uf_use_db eq 'y'}checked="checked"{/if}/></td></tr>
    			<tr><td class="form">{tr}Use a directory to store userfiles{/tr}:</td><td><input type="radio" name="uf_use_db" value="n" {if $uf_use_db eq 'n'}checked="checked"{/if}/> {tr}Path{/tr}:<input type="text" name="uf_use_dir" value="{$uf_use_dir|escape}" /> </td></tr>
    			<tr><td align="center" colspan="2"><input type="submit" name="userfilesprefs" value="{tr}Change preferences{/tr}" /></td></tr>				</table>
				</form>
			</div>
		</div>
</div>
