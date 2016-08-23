{* $Id$ *}
{if isset($show_calendar_module) and $show_calendar_module eq 'y'}
	{tikimodule error=$module_params.error title=$tpl_module_title name=$name flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
		{if $viewlist eq 'list'}
			{include file='tiki-calendar_listmode.tpl'}
		{else}
			{include file='tiki-calendar_nav.tpl' ajax='n' module='y'}

			<table class="caltable" style="text-align:center;">
				<tr>
					{section name=dn loop=$daysnames_abr}
						<th class="days" style="width:14%">{$daysnames_abr[dn]|ucfirst}</th>
					{/section}
				</tr>
				{cycle values="odd,even" print=false}
				{section name=w loop=$cell}
					<tr>
						{section name=d loop=$daysnames_abr}
							{if !empty($cell[w][d].date)}
								{assign var=date value=$cell[w][d].date}
							{elseif !empty($cell[w][d].day)}
								{assign var=date value=$cell[w][d].day}
							{/if}
							{if isset($date)}
								{if in_array($viewmode, array('bimester', 'trimester', 'quarter', 'semester', 'year'))}
									{if in_array($prefs.display_field_order, array('DMY', 'DYM', 'YDM'))}
										{assign var=day_cursor value=$date|tiki_date_format:"%d-%m"}
									{else}
										{assign var=day_cursor value=$date|tiki_date_format:"%m-%d"}
									{/if}
								{elseif $viewmode eq 'day' and (!$cell[w][d].focus)}
									{$day_cursor = ''}
								{else}
									{assign var=day_cursor value=$date|tiki_date_format:"%d"}
								{/if}
								{assign var=month_cursor value=$date|tiki_date_format:"%m"}
							{/if}
							{assign var=day_today value=$smarty.now|tiki_date_format:"%d"}
							{assign var=month_today value=$smarty.now|tiki_date_format:"%m"}

							{if isset($cell[w][d].focus) and $cell[w][d].focus}
								{cycle values="odd,even" print=false}
							{else}
								{cycle values="text-muted" print=false}
							{/if}
							<td class="{if isset($cell[w][d].day) and $date eq $today}calhighlight calborder{else}{cycle advance=false}{/if}{if isset($cell[w][d].items[0])
								and ((isset($cell[w][d].items[0].modifiable) and $cell[w][d].items[0].modifiable eq "y")
								|| $cell[w][d].items[0].visible eq 'y')} calmodfocus{/if}" style="text-align:center; font-size:0.8em; width=14%">
								{if isset($cell[w][d].over)}
									{assign var=over value=$cell[w][d].over}
								{elseif isset($cell[w][d].items[0])}
									{assign var=over value=$cell[w][d].items[0].over}{else}{assign var=over value=""}
								{/if}
								{if isset($cell[w][d].items[0]) and ((isset($cell[w][d].items[0].modifiable)
									and $cell[w][d].items[0].modifiable eq "y") || $cell[w][d].items[0].visible eq 'y')}
									{if empty($calendar_popup) or $calendar_popup eq "y"}
										<a href="{$myurl}?todate={$date}&amp;viewmode={$viewmodelink}" title="{tr}View{/tr}" {if (isset($sticky_popup) and $sticky_popup eq 'y')
											or ($prefs.calendar_sticky_popup eq "y" and $cell[w][d].items[0].calitemId)}{popup sticky=true fullhtml="1" text=$over|escape:"javascript"|escape:"html"}{else}{popup fullhtml="1" text=$over|escape:"javascript"|escape:"html"}{/if}>
											{if isset($day_cursor)}
												{$day_cursor}
											{/if}
										</a>
									{else}
										{if isset($day_cursor)}
											{$day_cursor}
										{/if}
										{$over}
									{/if}
								{elseif $linkall eq 'y'}
									<a href="{$myurl}?todate={$cell[w][d].day}&amp;viewmode={$viewmodelink}">
										{$day_cursor}
									</a>
								{else}
									{if isset($day_cursor)}
										{$day_cursor}
									{/if}
								{/if}
							</td>
						{/section}
					</tr>
				{/section}
			</table>
		{/if}
		{if $tiki_p_add_events eq 'y' && (empty($module_params.showaction) || $module_params.showaction ne 'n')}
			<br>
			<p>
				<a href="tiki-calendar_edit_item.php" style="display: block; margin: auto auto; width: 98px;">
					{icon name="add"}
					 {tr}Add Event{/tr}
				</a>
			</p>
		{/if}
	{/tikimodule}
{/if}
