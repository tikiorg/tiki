
{* $Id$ *}

{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}Enable/disable Tiki features in {/tr}<a class="alert-link" href="tiki-admin.php?page=features">{tr}Control Panels{/tr}&nbsp;{$prefs.site_crumb_seper}&nbsp;{tr}Features{/tr}</a>{tr}, but configure them elsewhere{/tr}.
	<br/>
	{tr}See <strong>more options</strong> after you enable more <a class='alert-link' target='tikihelp' href='https://doc.tiki.org/Preference+Filters'>Preference Filters</a> above ({icon name="filter"}){/tr}.
{/remarksbox}

<div class="clearfix">
	{foreach from=$admin_icons key=page item=info}
			{if $info.disabled}
				{assign var=class value="admbox advanced btn btn-primary disabled"}
			{else}
				{assign var=class value="admbox basic btn btn-primary"}
			{/if}
				{* FIXME: Buttons are forced to be squares, not fluid. Labels which exceed 2 lines will be cut. *}
				<a href="tiki-admin.php?page={$page}" alt="{$info.title} {$info.description}" class="{$class} tips bottom slow {if $info.disabled}disabled-clickable{/if}" title="{$info.title|escape}{if $info.disabled} ({tr}Disabled{/tr}){/if}|{$info.description}">
					{icon name="admin_$page"}
					<span class="title">{$info.title|escape}</span>
				</a>
	{/foreach}
</div>
