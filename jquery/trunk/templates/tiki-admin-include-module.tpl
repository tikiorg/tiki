{remarksbox type="tip" title="{tr}Tip{/tr}"}
{tr}Right &amp; left boxes{/tr}
<hr />
<a class="rbox-link" href="tiki-admin_modules.php">{tr}Administer modules{/tr}</a>
{/remarksbox}

<div class="cbox">
  <div class="cbox-title">
    {tr}{$crumbs[$crumb]->description}{/tr}
    {help crumb=$crumbs[$crumb]}
  </div>


      <form action="tiki-admin.php?page=module" method="post">
        <table class="admin">
	<tr>
		<td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Module+Control" target="tikihelp" class="tikihelp" title="{tr}Show Module Controls{/tr}">{/if} {tr}Show Module Controls{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
		<td><input type="checkbox" name="feature_modulecontrols" {if $prefs.feature_modulecontrols eq 'y'}checked="checked"{/if}/></td>
	</tr>
	<tr>
		<td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Users+Configure+Modules" target="tikihelp" class="tikihelp" title="{tr}Users can Configure Modules{/tr}">{/if} {tr}Users can Configure Modules{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
		<td><input type="checkbox" name="user_assigned_modules" {if $prefs.user_assigned_modules eq 'y'}checked="checked"{/if}/></td>
	</tr>
	<tr>

		<td class="form"> {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Users+Shade+Modules" target="tikihelp" class="tikihelp" title="{tr}Users can Shade Modules{/tr}">{/if} {tr}Users can Shade Modules{/tr} {if $prefs.feature_help eq 'y'}</a>{/if}</td>
		<td><select name="user_flip_modules">
		<option value="y" {if $prefs.user_flip_modules eq 'y'}selected="selected"{/if}>{tr}always{/tr}</option>
		<option value="module" {if $prefs.user_flip_modules eq 'module'}selected="selected"{/if}>{tr}module decides{/tr}</option>
		<option value="n" {if $prefs.user_flip_modules eq 'n'}selected="selected"{/if}>{tr}never{/tr}</option>
		</select></td>
	</tr>
	<tr>
		<td class="form" > <label for="general-modules">{tr}Display modules to all groups always{/tr}:</label></td>
	        <td ><input type="checkbox" name="modallgroups" id="general-modules" {if $prefs.modallgroups eq 'y'}checked="checked"{/if} {popup text="Hint: If you lose your login module, use tiki-login_scr.php to be able to login!" textcolor=red}/>
	        </td>
	</tr>
	<tr>
		<td class="form"><label for="general-anon_modules">{tr}Hide anonymous-only modules from registered users{/tr}:</label></td>
	        <td><input type="checkbox" name="modseparateanon" id="general-anon_modules" {if $prefs.modseparateanon eq 'y'}checked="checked"{/if}/>
	        </td>

	</tr>
	<tr>
		
          <td colspan="2" class="button"><input type="submit" name="modulesetup" value="{tr}Save{/tr}" /></td>
		  
        </tr>
        </table>
      </form>

</div>

