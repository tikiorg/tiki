{* $Id$ *}

{title help="Banners" admpage=ads}{tr}Banners{/tr}{/title}

{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}To use a banner in a <a class="rbox-link" href="tiki-admin_modules.php">module</a> or a template, use {literal}{banner zone=ABC}{/literal}, where ABC is the name of the zone.{/tr}{/remarksbox}

{if $tiki_p_admin_banners eq 'y'}
	<div class="t_navbar margin-bottom-md">
		{button href="tiki-edit_banner.php" class="btn btn-link" _type="link" _icon_name="sticky-note-o" _text="{tr}Create banner{/tr}"}
	</div>
{/if}

{if $listpages or ($find ne '')}
	{include file='find.tpl'}
{/if}

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

<div class="{if $js === 'y'}table-responsive{/if}"> {*the table-responsive class cuts off dropdown menus *}
	<table class="table table-striped table-hover">
		<tr>
			<th>{self_link _sort_arg='sort_mode' _sort_field='bannerId'}{tr}Id{/tr}{/self_link}</th>
			<th>{self_link _sort_arg='sort_mode' _sort_field='client'}{tr}Client{/tr}{/self_link}</th>
			<th>{self_link _sort_arg='sort_mode' _sort_field='url'}{tr}URL{/tr}{/self_link}</th>
			<th>{self_link _sort_arg='sort_mode' _sort_field='zone'}{tr}Zone{/tr}{/self_link}</th>
			<th>{self_link _sort_arg='sort_mode' _sort_field='created'}{tr}Created{/tr}{/self_link}</th>
			<th>{self_link _sort_arg='sort_mode' _sort_field='which'}{tr}Method{/tr}{/self_link}</th>
			<th>{self_link _sort_arg='sort_mode' _sort_field='useDate'}{tr}Use Dates?{/tr}{/self_link}</th>
			<th>{self_link _sort_arg='sort_mode' _sort_field='maxImpressions'}{tr}Max Impressions{/tr}{/self_link}</th>
			<th>{self_link _sort_arg='sort_mode' _sort_field='impressions'}{tr}Impressions{/tr}{/self_link}</th>
			<th>{self_link _sort_arg='sort_mode' _sort_field='maxClicks'}{tr}Max Clicks{/tr}{/self_link}</th>
			<th>{self_link _sort_arg='sort_mode' _sort_field='clicks'}{tr}Clicks{/tr}{/self_link}</th>
			<th></th>
		</tr>

		{section name=changes loop=$listpages}
		<tr>
			<td class="id">{if $tiki_p_admin_banners eq 'y'}<a class="link" href="tiki-edit_banner.php?bannerId={$listpages[changes].bannerId}">{/if}{$listpages[changes].bannerId}{if $tiki_p_admin_banners eq 'y'}</a>{/if}</td>
			<td class="username">{$listpages[changes].client|username}</td>
			<td class="text">{$listpages[changes].url}</td>
			<td class="text">{$listpages[changes].zone|escape}</td>
			<td class="date">{$listpages[changes].created|tiki_short_date}</td>
			<td class="text">{$listpages[changes].which}</td>
			<td class="text">{$listpages[changes].useDates}</td>
			<td class="integer"><span class="badge">{$listpages[changes].maxImpressions}</span></td>
			<td class="integer"><span class="badge">{$listpages[changes].impressions}</span></td>
			<td class="integer"><span class="badge">{$listpages[changes].maxClicks}</span></td>
			<td class="integer"><span class="badge">{$listpages[changes].clicks}</span></td>
			<td class="action">
				{capture name=banner_actions}
					{strip}
						{$libeg}<a href="tiki-view_banner.php?bannerId={$listpages[changes].bannerId}">
							{icon name='chart' _menu_text='y' _menu_icon='y' alt="{tr}Stats{/tr}"}
						</a>{$liend}
						{if $tiki_p_admin_banners eq 'y'}
							{$libeg}<a href="tiki-edit_banner.php?bannerId={$listpages[changes].bannerId}">
								{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
							</a>{$liend}
							{$libeg}<a href="tiki-list_banners.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].bannerId}">
								{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
							</a>{$liend}
						{/if}
					{/strip}
				{/capture}
				{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
				<a
					class="tips"
					title="{tr}Actions{/tr}"
					href="#"
					{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.banner_actions|escape:"javascript"|escape:"html"}{/if}
					style="padding:0; margin:0; border:0"
				>
					{icon name='wrench'}
				</a>
				{if $js === 'n'}
					<ul class="dropdown-menu" role="menu">{$smarty.capture.banner_actions}</ul></li></ul>
				{/if}
			</td>
		</tr>
		{sectionelse}
			{norecords _colspan=12}
		{/section}
	</table>
</div>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
