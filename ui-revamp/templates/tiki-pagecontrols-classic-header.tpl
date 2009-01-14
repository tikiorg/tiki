<div class="wikiactions" style="float: right; padding-left:10px; white-space: nowrap">
	<div class="icons" style="float: left;">
		{if $controls.actions.pdf}{$controls.actions.pdf.icon}{/if}
		{if $controls.tabs.edit}{$controls.tabs.edit.icon}{/if}
		{if $controls.actions.wiki3d}{$controls.actions.wiki3d.icon}{/if}
		{if $controls.actions.refresh}{$controls.actions.refresh.icon}{/if}
		{if $controls.actions.print}{$controls.actions.print.icon}{/if}
		{if $controls.actions.tellafriend}{$controls.actions.tellafriend.icon}{/if}
		{if $controls.actions.notepad}{$controls.actions.notepad.icon}{/if}
		{if $controls.actions.watch}{$controls.actions.watch.icon}{/if}
		{if $controls.actions.structwatch}{$controls.actions.structwatch.icon}{/if}

		{if $controls.watchgroup}
			{popup_link block=page_group_watch}{$controls.watchgroup.icon}{/popup_link}
		{/if}

		{if $controls.structwatchgroup}
			{popup_link block=structure_group_watch}{$controls.structwatchgroup.icon}{/popup_link}
		{/if}

		<div id="page_group_watch" class="popup-group-watch">
			{foreach from=$controls.watchgroup.items item=watch}
				<div>{$watch.icon} {$watch.text}</div>
			{/foreach}
		</div>
		<div id="structure_group_watch" class="popup-group-watch">
			{foreach from=$controls.structwatchgroup.items item=watch}
				<div>{$watch.icon} {$watch.text}</div>
			{/foreach}
		</div>
	</div>
	{if $controls.backlinks}
		<form action="tiki-index.php" method="get" style="display: block; float: left">
			<select name="page" onchange="page.form.submit()">
				<option>{tr}Backlinks{/tr}...</option>
				{foreach from=$backlinks item=back}
					<option value="{$back.fromPage|escape}">{$back.fromPage|escape}</option>
				{/foreach}
			</select>
		</form>
	{/if}

	{if $controls.language}
		<form action="tiki-index.php" method="get">
			<select name="page" onchange="document.location.href=this.options[this.selectedIndex].value;">
				{foreach from=$controls.language.items item=opt}
					<option value="{$opt.link.href|escape}"{if $opt.selected} selected="selected"{/if}>{$opt.text|escape}</option>
				{/foreach}
			</select>
		</form>
	{/if}
</div>
{if $prefs.feature_page_title eq 'y'}
	{title link=$controls.heading.link.href}{$controls.heading.text|escape}{/title}
{/if}
