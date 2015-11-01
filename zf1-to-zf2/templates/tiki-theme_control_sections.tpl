{title help="Theme+Control"}{tr}Theme Control{/tr}: {tr}Sections{/tr}{/title}
<div class="t_navbar btn-group">
	{button href="tiki-theme_control.php" class="btn btn-default" _text="{tr}Control by Categories{/tr}"}
	{button href="tiki-theme_control_objects.php" class="btn btn-default" _text="{tr}Control by Objects{/tr}"}
</div>
<h2>{tr}Assign themes to sections{/tr}</h2>
<form action="tiki-theme_control_sections.php" method="post" class="form-inline" role="form">
	<div class="form-group">
		<label for="section">{tr}Section{/tr}</label>
		<select name="section" class="form-control input-sm">
			{foreach key=sec item=ix from=$sections}
				<option value="{$sec|escape}" {if $a_section eq $sec}selected="selected"{/if}>{$sec}</option>
			{/foreach}
		</select>
	</div>
	<div class="form-group">
		<label for="theme">{tr}Theme{/tr}</label>
		<select name="theme" class="form-control input-sm">
			{foreach from=$themes key=theme item=theme_name}
				<option value="{$theme|escape}">{$theme_name}</option>
			{/foreach}
		</select>
	</div>
	<input type="submit" class="btn btn-primary btn-sm" name="assign" value="{tr}Assign{/tr}">
</form>

<h2>{tr}Assigned sections{/tr}</h2>
<form action="tiki-theme_control_sections.php" method="post" role="form" class="form">
    <div class="table-responsive">
		<table class="table">
			<tr>
				<th>
					<button type="submit" class="btn btn-warning btn-sm" name="delete" title="{tr}Delete selected{/tr}">
						{icon name="delete"}
					</button>
				</th>
				<th>
					<a href="tiki-theme_control_sections.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'section_desc'}section_asc{else}section_desc{/if}">
						{tr}Section{/tr}
					</a>
				</th>
				<th>
					<a href="tiki-theme_control_sections.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'theme_desc'}theme_asc{else}theme_desc{/if}">
						{tr}Theme{/tr}
					</a>
				</th>
			</tr>
			{section name=user loop=$channels}
				<tr>
					<td class="checkbox-cell">
						<input type="checkbox" name="sec[{$channels[user].section}]">
					</td>
					<td class="text">
						{$channels[user].section}
					</td>
					<td class="text">
						{$channels[user].theme}
					</td>
				</tr>
			{/section}
		</table>
    </div>
</form>
