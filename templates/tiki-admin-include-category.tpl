{remarksbox type="tip" title="{tr}Tip{/tr}"}
{tr}Use categories to regroup various Tiki objects{/tr}.
<hr />
{tr}Link to{/tr} <a class="rbox-link" href="tiki-browse_categories.php">{tr}Browse categories{/tr}</a>
<hr />
{tr}Link to{/tr} <a class="rbox-link" href="tiki-admin_categories.php">{tr}Administer categories{/tr}</a>
{/remarksbox}

<div class="cbox">
  <div class="cbox-title">
    {tr}{$crumbs[$crumb]->description}{/tr}
    {help crumb=$crumbs[$crumb]}
  </div>


      <form action="tiki-admin.php?page=category" method="post">
        <table class="admin">
	<tr>
		<td class="form"> {tr}Show Category Path{/tr} </td>
		<td><input type="checkbox" name="feature_categorypath" {if $prefs.feature_categorypath eq 'y'}checked="checked"{/if}/></td>
	</tr>
	<tr>
		<td class="form"> {tr}Exclude These Category IDs from Path (comma delimited){/tr} </td>
		<td><input type="text" name="categorypath_excluded" value="{$prefs.categorypath_excluded}" size="15" /></td>
	</tr>
	<tr>
		<td class="form"> {tr}Show Category Objects{/tr} </td>
		<td><input type="checkbox" name="feature_categoryobjects" {if $prefs.feature_categoryobjects eq 'y'}checked="checked"{/if}/></td>
	</tr>
	<tr>

	<td class="form">
	{if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}WYSIWYCA+Search" target="tikihelp" class="tikihelp" title="{tr}Search may show forbidden results. Much better performance though.{/tr}">{/if}
		{tr}Ignore category viewing restrictions{/tr} ({tr}Search{/tr})
		{if $prefs.feature_help eq 'y'}</a>{/if}</td>
          <td><input type="checkbox" name="feature_search_show_forbidden_cat"
                {if $prefs.feature_search_show_forbidden_cat eq 'y'}checked="checked"{/if}/></td>
	</tr>
	<tr>
		<td class="form">{tr}Permission to all (not just any) of an object's categories is required for access{/tr}</td>
		<td><input type="checkbox" name="feature_category_reinforce" {if $prefs.feature_category_reinforce eq 'y'}checked="checked"{/if}/></td>
	</tr>
	<tr>
		<td class="form">{tr}Categories browse uses PhpLayers{/tr} <i>({tr}The feature must be activated{/tr})</i></td>
		<td><input type="checkbox" name="feature_category_use_phplayers" {if $prefs.feature_category_use_phplayers eq 'y'}checked="checked"{/if}/></td>
	</tr>
	<tr>
          <td colspan="2" class="button"><input type="submit" name="categorysetup" value="{tr}Save{/tr}" /></td>
		  
        </tr>
        </table>
      </form>

</div>

