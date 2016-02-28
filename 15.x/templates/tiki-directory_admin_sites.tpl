{* $Id$ *}

{title help="Directory" url="tiki-directory_admin_sites.php?parent=$parent"}{tr}Admin sites{/tr}{/title}

{include file='tiki-directory_admin_bar.tpl'}
<h2>{tr}Parent directory category:{/tr}</h2>
<form name="path" method="post" action="tiki-directory_admin_categories.php" class="form-horizontal">
	<br>
	<div class="form-group">
		<label class="col-sm-3 control-label">
			{tr}Parent directory category{/tr}
		</label>
		<div class="col-sm-7">
			<select name="parent" onchange="javascript:path.submit();" class="form-control">
				<option value="0">{tr}Top{/tr}</option>
				{section name=ix loop=$categs}
					<option value="{$categs[ix].categId|escape}" {if $parent eq $categs[ix].categId}selected="selected"{/if}>{$categs[ix].path|escape}</option>
				{/section}
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label"></label>
		<div class="col-sm-7">
			<input type="submit" class="btn btn-default btn-sm" name="go" value="{tr}Go{/tr}">
		</div>
	</div>
</form>

{* Dislay a form to add or edit a site *}
<h2>{if $siteId}{tr}Edit a site{/tr}{else}{tr}Add a site{/tr}{/if}</h2>
<form action="tiki-directory_admin_sites.php" method="post" class="form-horizontal">
	<input type="hidden" name="parent" value="{$parent|escape}">
	<input type="hidden" name="siteId" value="{$siteId|escape}">

	<div class="form-group">
		<label class="col-sm-3 control-label">{tr}Name{/tr}</label>
		<div class="col-sm-7">
	      	<input type="text" name="name" value="{$info.name|escape}" class="form-control">
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}Description{/tr}</label>
		<div class="col-sm-7">
	      	<textarea rows="5" cols="60" name="description" class="form-control">{$info.description|escape}</textarea>
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}URL{/tr}</label>
		<div class="col-sm-7">
	      	<input type="text" size="60" name="url" value="{if $info.url ne ""}{$info.url|escape}{else}http://{/if}" class="form-control">
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}Directory Categories{/tr}</label>
		<div class="col-sm-7">
	      	<select name="siteCats[]" multiple="multiple" size="4" class="form-control">
				{section name=ix loop=$categs}
					<option value="{$categs[ix].categId|escape}" {if $categs[ix].belongs eq 'y' or $categs[ix].categId eq $addtocat}selected="selected"{/if}>
						{$categs[ix].path|escape}
					</option>
				{/section}
			</select>
			<br>
			{if $categs|@count ge '2'}
				{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Use Ctrl+Click to select multiple options{/tr}{/remarksbox}
			{/if}
	    </div>
    </div>
    {if $prefs.directory_country_flag eq 'y'}
	    <div class="form-group">
			<label class="col-sm-3 control-label">{tr}Country{/tr}</label>
			<div class="col-sm-7">
		      	<select id="country" name="country" class="form-control">
					{section name=ux loop=$countries}
						<option value="{$countries[ux]|escape}" {if $info.country eq $countries[ux]}selected="selected"{/if}>{tr}{$countries[ux]}{/tr}</option>
					{/section}
				</select>
		    </div>
	    </div>
	{/if}
    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}Is valid{/tr}	</label>
		<div class="col-sm-7">
	      	<input name="isValid" type="checkbox" {if $info.isValid eq 'y'}checked="checked"{/if}>
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label"></label>
		<div class="col-sm-7">
	      	<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
	    </div>
    </div>
</form>

<h2>{tr}Sites{/tr}</h2>
{* Display the list of categories (items) using pagination *}
{* Links to edit, remove, browse the categories *}
<form action="tiki-directory_admin_sites.php" method="post">
	{* Use css menus as fallback for item dropdown action menu if javascript is not being used *}
	{if $prefs.javascript_enabled !== 'y'}
		{$js = 'n'}
		{$libeg = '<li>'}
		{$liend = '</li>'}
	{else}
		{$js = 'y'}
		{$libeg = ''}
		{$liend = ''}
	{/if}
	<div class="{if $js === 'y'}table-responsive{/if}"> {* table-responsive class cuts off css drop-down menus *}
		<table class="table table-striped table-hover">
			<tr>
				<th> </th>
				<th> <a href="tiki-directory_admin_sites.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a> </th>
				<th> <a href="tiki-directory_admin_sites.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'url_desc'}url_asc{else}url_desc{/if}">{tr}URL{/tr}</a> </th>
				{if $prefs.directory_country_flag eq 'y'}
					<th> <a href="tiki-directory_admin_sites.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'country_desc'}country_asc{else}country_desc{/if}">{tr}Country{/tr}</a> </th>
				{/if}
				<th class="text-center"> <a href="tiki-directory_admin_sites.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Hits{/tr}</a> </th>
				<th class="text-center"> <a href="tiki-directory_admin_sites.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'isValid_desc'}isValid_asc{else}isValid_desc{/if}">{tr}Valid{/tr}</a> </th>
				<th></th>
			</tr>

			{section name=user loop=$items}
			<tr class="{cycle advance=false}">
				<td class="checkbox-cell"><input type="checkbox" name="remove[]" value="{$items[user].siteId}"></td>
				<td class="text">{$items[user].name|escape}</td>
				<td class="text"><a href="{$items[user].url}" target="_new">{$items[user].url}</a></td>
				{if $prefs.directory_country_flag eq 'y'}
					<td class="text"><img src='img/flags/{$items[user].country}.gif' alt='{$items[user].country}'> </td>
				{/if}
				<td class="text text-center">{$items[user].hits}</td>
				<td class="text text-center">{if $items[user].isValid eq 'y'} Yes {else} No {/if}</td>
				<td class="action">
					{capture name=site_actions}
						{strip}
							{$libeg}<a href="tiki-directory_admin_sites.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;siteId={$items[user].siteId}">
								{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
							</a>{$liend}
							{$libeg}<a href="tiki-directory_admin_sites.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$items[user].siteId}">
								{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
							</a>{$liend}
						{/strip}
					{/capture}
					{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
					<a
						class="tips"
						title="{tr}Actions{/tr}"
						href="#"
						{if $js === 'y'}{popup delay="0|2000" fullhtml="1" center=true text=$smarty.capture.site_actions|escape:"javascript"|escape:"html"}{/if}
						style="padding:0; margin:0; border:0"
					>
						{icon name='wrench'}
					</a>
					{if $js === 'n'}
						<ul class="dropdown-menu" role="menu">{$smarty.capture.site_actions}</ul></li></ul>
					{/if}
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td class="text" colspan="6"><i> {tr}Directory Categories:{/tr}{assign var=fsfs value=1}
					{section name=ii loop=$items[user].cats}
						{if $fsfs}{assign var=fsfs value=0}{else}, {/if}
						{$items[user].cats[ii].path|escape}
					{/section} </i>
				</td>
			</tr>
			{sectionelse}
				{if $prefs.directory_country_flag eq 'y'}
					{norecords _colspan=7}
				{else}
					{norecords _colspan=6}
				{/if}
			{/section}
		</table>
	</div>
	{if $items}
		{tr}Perform action with selected:{/tr}
		<input type="submit" class="btn btn-default btn-sm" name="groupdel" value=" {tr}Delete{/tr} ">
	{/if}
</form>
{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
