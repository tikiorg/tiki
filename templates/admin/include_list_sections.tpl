
{* $Id$ *}
{*
 * If you want to change this page, check http://tiki.org/AdministrationDev
 * there you"ll find attached a gimp image containing this page with icons in separated layers
 *}

{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}Enable/disable Tiki features in {/tr}<a class="rbox-link" href="tiki-admin.php?page=features">{tr}Admin{/tr}&nbsp;{$prefs.site_crumb_seper}&nbsp;{tr}Features{/tr}</a>{tr}, but configure them elsewhere{/tr}
{/remarksbox}

<div class="clearfix cbox-data" style="padding-left: 10px;">
	{foreach from=$icons key=page item=info}
		{if $info.position}
			{if $info.disabled}
				{assign var=class value="admbox off advanced"}
			{else}
				{assign var=class value="admbox basic"}
			{/if}
			{self_link page=$page _class=$class _style="background-image: url('img/icons/sprite/admin.fullpanel.png'); background-position: `$info.position`" _title=$info.title}
				<img src="img/trans.png" alt="{$info.title|escape}" title="{$info.title|escape}{if $info.disabled} ({tr}Disabled{/tr}){/if}">
				<span>{$info.title|escape}</span>
			{/self_link}
		{/if}
	{/foreach}
</div>
