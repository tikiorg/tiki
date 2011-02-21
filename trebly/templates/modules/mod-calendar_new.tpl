{* $Id$ *}
{if $show_calendar_module eq 'y'}
{tikimodule error=$module_params.error title=$tpl_module_title name=$name flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{if $viewlist eq 'list'}
	{include file='tiki-calendar_listmode.tpl'}
{else}
	{include file='tiki-calendar_nav.tpl' ajax='n' module='y'}

	<table cellpadding="0" cellspacing="0" border="0" class="caltable" style="text-align:center;">
		<tr>
			{section name=dn loop=$daysnames_abr}
				<th class="days" width="14%">{$daysnames_abr[dn]|ucfirst}</th>
			{/section}
		</tr>
		{cycle values="odd,even" print=false}
		{section name=w loop=$cell}
			<tr>
				{section name=d loop=$daysnames_abr}
					{if empty($cell[w][d].date)}{assign var=date value=$cell[w][d].day}{else}{assign var=date value=$cell[w][d].date}{/if}
					{if in_array($viewmode, array('bimester', 'trimester', 'quarter', 'semester', 'year'))}
						{if in_array($prefs.display_field_order, array('DMY', 'DYM', 'YDM'))}
							{assign var=day_cursor value=$date|tiki_date_format:"%d-%m"}
						{else}
							{assign var=day_cursor value=$date|tiki_date_format:"%m-%d"}
						{/if}
					{else}
						{assign var=day_cursor value=$date|tiki_date_format:"%d"}
					{/if}
					{assign var=month_cursor value=$date|tiki_date_format:"%m"}
					{assign var=day_today value=$smarty.now|tiki_date_format:"%d"}
					{assign var=month_today value=$smarty.now|tiki_date_format:"%m"}

					{if $cell[w][d].focus}
						{cycle values="odd,even" print=false}
					{else}
						{cycle values="notoddoreven" print=false}
					{/if}
					<td class="{cycle advance=false}{if $date eq $today} highlight{/if}{if isset($cell[w][d].items[0]) and ($cell[w][d].items[0].modifiable eq "y" 
								|| $cell[w][d].items[0].visible eq 'y')} focus{/if}" width="14%" style="text-align:center; font-size:0.8em;">

						{if isset($cell[w][d].over)}
							{assign var=over value=$cell[w][d].over}
						{elseif isset($cell[w][d].items[0])}
							{assign var=over value=$cell[w][d].items[0].over}{else}{assign var=over value=""}
						{/if}
						{if isset($cell[w][d].items[0]) and ($cell[w][d].items[0].modifiable eq "y" || $cell[w][d].items[0].visible eq 'y')}
							<a style="text-decoration: underline; font-weight: bold" href="{$myurl}?todate={$date}&amp;viewmode={$viewmodelink}" 
							{if $sticky_popup eq 'y' or ($prefs.calendar_sticky_popup eq "y" and $cell[w][d].items[0].calitemId)}
									{popup sticky=true fullhtml="1" text=$over|escape:"javascript"|escape:"html"}
								{else}
									{popup fullhtml="1" text=$over|escape:"javascript"|escape:"html"}
								{/if}
								>
								{$day_cursor}
							</a>
						{elseif $linkall eq 'y'}
							<a style="text-decoration: underline; font-weight: bold" href="{$myurl}?todate={$cell[w][d].day}&amp;viewmode={$viewmodelink}">{$day_cursor}</a>
						{else}
							{$day_cursor}
						{/if}

					</td>
				{/section}
			</tr>
		{/section}
	</table>
{/if}
{if $tiki_p_add_events eq 'y' && (empty($module_params.showaction) || $module_params.showaction ne 'n')}
	<p><a href="tiki-calendar_edit_item.php"><img src="pics/icons/add.png" alt="" /> {tr}Add event{/tr}</a></p>
{/if}
{/tikimodule}
{/if}
