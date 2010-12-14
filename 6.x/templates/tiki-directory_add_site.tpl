{title url="tiki-directory_add_site.php?parent=$parent"}{tr}Add a new site{/tr}{/title}

{include file='tiki-directory_bar.tpl'}

{if $categs[0] eq ''}
	{icon _id=exclamation style="vertical-align:middle" alt="{tr}Error{/tr}"} {tr}You cannot add sites until Directory Categories are setup.{/tr} <br />
	{if $tiki_p_admin_directory_cats ne 'y'}
		{tr}Please contact the Site Administrator{/tr}{else}{tr}<a href="tiki-directory_admin_categories.php">Add a directory category now</a>.{/tr}
	{/if}
{else}
	{if $save eq 'y'}
		<h2>{tr}Site added{/tr}</h2>
			{icon _id=accept alt="{tr}OK{/tr}" style="vertical-align:middle" align="left"} {tr}The following site was added, but may require validation by the admin before appearing on the lists.{/tr}
		<table class="formcolor">
			<tr>
				<td>{tr}Name{/tr}:</td>
				<td>{$info.name}</td>
			</tr>
			<tr>
				<td>{tr}Description{/tr}:</td>
				<td>{$info.description}</td>
			</tr>
			<tr>
				<td>{tr}URL{/tr}:</td>
				<td>{$info.url}</td>
			</tr>
			{if $prefs.directory_country_flag eq 'y'}
				<tr>
					<td>{tr}Country{/tr}:</td>
					<td>{$info.country}</td>
				</tr>
			{/if}
		</table>
	{else}
		{if $msg}
			<div class="simplebox highlight">{icon _id=exclamation alt="{tr}Error{/tr}" style="vertical-align:middle"} {tr}{$msg}{/tr}</div>
		{/if}

		{* Display a form to add or edit a site *}
		<h2>{if $siteId}{tr}Edit a site{/tr}{else}{tr}Add a Site{/tr}{/if}</h2>
		<form action="tiki-directory_add_site.php" method="post">
			<input type="hidden" name="parent" value="{$parent|escape}" />
			<input type="hidden" name="siteId" value="{$siteId|escape}" />
			<table class="formcolor">
				<tr>
					<td><label for="name">{tr}Name{/tr}:</label></td>
					<td><input type="text" id="name" name="name" value="{$info.name|escape}" /></td>
				</tr>
				<tr>
					<td><label for="description">{tr}Description:{/tr}</label></td>
					<td><textarea rows="5" cols="60" id="description" name="description">{$info.description|escape}</textarea></td>
				</tr>
				<tr>
					<td><label for="url">{tr}URL:{/tr}</label></td>
					<td><input type="text" size="60" id="url" name="url" value="{if $info.url ne ""}{$info.url|escape}{else}http://{/if}" /></td>
				</tr>
				<tr>
					<td><label for="siteCats">{tr}Directory Categories:{/tr}</label></td>
					<td>
						<select id="siteCats" name="siteCats[]" multiple="multiple" size="4">
							{section name=ix loop=$categs}
								<option value="{$categs[ix].categId|escape}" {if $categs[ix].belongs eq 'y' or $categs[ix].categId eq $addtocat}selected="selected"{/if}>
									{$categs[ix].path|escape}
								</option>
							{/section}
						</select>
						{if $categs|@count ge '2'}
							{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Use Ctrl+Click to select multiple options{/tr}{/remarksbox}
						{/if}
					</td>
				</tr>
				{if $prefs.directory_country_flag eq 'y'}
					<tr>
						<td><label for="country">{tr}Country{/tr}:</label></td>
						<td>
							<select id="country" name="country">
      	    				{section name=ux loop=$countries}
									<option value="{$countries[ux]|escape}" {if $info.country eq $countries[ux]}selected="selected"{/if}>{tr}{$countries[ux]}{/tr}</option>
								{/section}
							</select>
						</td>
					</tr>
				{else}
					<input type="hidden" name="country" value="None">
				{/if}
				<input name="isValid" type="hidden" value="" />
      	   {if $prefs.feature_antibot eq 'y' && $user eq ''}
					{include file='antibot.tpl' td_style="formcolor"}
				{/if}
      	   <tr>
					<td>&nbsp;</td>
					<td><input type="submit" name="save" value="{tr}Save{/tr}" /></td>
				</tr>
			</table>
		</form>
	{/if}
{/if} 
