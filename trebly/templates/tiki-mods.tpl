{* $Id$ *}
{strip}
{title help="mods"}{tr}Tiki Mods{/tr}{/title}
<div class="navbar">
	{button href="tiki-mods_admin.php" _text="{tr}Mods Configuration{/tr}"}
	{button href="tiki-mods.php?reload=1$findarg$typearg$versionarg" _text="{tr}Update remote index{/tr}"}
	{button href="tiki-mods.php?rebuild=1$findarg$typearg$versionarg" _text="{tr}Rebuild local list{/tr}"}
	{if $prefs.feature_mods_provider eq 'y'}
		{button href="tiki-mods.php?republishall=1$findarg$typearg$versionarg" _text="{tr}Republish all{/tr}"}
		{button href="tiki-mods.php?publishall=1$findarg$typearg$versionarg" _text="{tr}Publish all{/tr}"}
		{button href="tiki-mods.php?unpublishall=1$findarg$typearg$versionarg" _text="{tr}Unpublish all{/tr}"}
	{/if}
</div>

{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}Tiki "mods" are additional features not included in the public release. Learn more at <a target="tikihelp" href="http://mods.tiki.org">mods.tiki.org</a>.{/tr}
{/remarksbox}
{remarksbox type="note" title="{tr}Note{/tr}"}
	<p><strong>{tr}Tiki "mods" are undergoing refurbishment.{/tr}</strong></p>
	<p>{tr}You will probably some find mods shown as being compatible with older versions of Tiki will work with later versions with little or no modification.{/tr}</p>
	<p><em>{tr}Your help is needed! Please visit <a href="http://dev.tiki.org/Mods">dev.tiki.org</a> to find out more.{/tr}</em></p>
{/remarksbox}

{if $iswritable}
	{remarksbox type="warning" title="{tr}Attention{/tr}"}
		{tr}Apache has the right to write in your file tree, which enables the installation, removal or upgrade of packages. When you are done with those operations, think to fix those permissions back to a safe state (by using "sh setup.sh" for example).{/tr}
	{/remarksbox}
{else}
	{remarksbox type="info" title="{tr}Attention{/tr}"}
		{tr}To install, remove or upgrade packages you need to give the apache user the right to write files in your web tree (you can use "sh setup.sh open" to set it up). After installation you need to remove that permission (using "sh setup.sh").{/tr}
	{/remarksbox}
{/if}
{if $installask}
<form method='post' action='?'>
	<div class="simplebox">
		<ul>
		{if $installask.wanted}
			<li>You asked to install these mods:
				<ul>{foreach from=$installask.wanted item=element}
					{if $element->repository eq 'unavailable'}
						<li>{$element->name|escape} ({$element->type|escape}) but is not in any repository</li>
					{else}
						<li><input type='checkbox' onchange='update_button_install();' name='install-wants[]' value='{$element->modname|escape}' checked />{$element->name|escape} {$element->revision} ({$element->type|escape})</li>
					{/if}
				{/foreach}</ul>
			</li>
		{/if}
		{if $installask.wantedtoremove}
			<li>You asked to <strong>remove</strong> these mods:
				<ul>{foreach from=$installask.wantedtoremove item=element}
					{if $element->repository eq 'installed'}
						<li><input type='checkbox' onchange='update_button_install();' name='install-wants[]' value='{$element->modname|escape}' checked />{$element->name|escape} {$element->revision} ({$element->type|escape})</li>
					{else}
						<li>{$element->name|escape} ({$element->type|escape}) but is not installed</li>
					{/if}
				{/foreach}</ul>
			</li>
		{/if}
		{if $installask.unavailable}
			<li style='color:#990000'>The following packages are required, but cannot be installed:
				<ul>{foreach from=$installask.unavailable item=element}
					<li>{$element->name|escape} ({$element->type|escape}) : 
						{foreach from=$element->tests item=test}
							{$test->test}{$test->revision}
						{/foreach}
						{foreach from=$element->errors item=error}
							{$error}
						{/foreach}
					</li>
			{/foreach}</ul>
		{/if}
		{if $installask.conflicts}
			<li style='color:#990000'>The following packages are required, but in conflicts:
			<ul>{foreach from=$installask.conflicts item=element}
					<li>{$element->name|escape} ({$element->type|escape}) : 
						{foreach from=$element->tests item=test}
							{$test->test}{$test->revision}
						{/foreach}
						{foreach from=$element->errors item=error}
							{$error}
						{/foreach}
					</li>
			{/foreach}</ul>
		{/if}
		{if $installask.toremove}
			<li>The following mods will be <strong>removed</strong>:
			<ul>{foreach from=$installask.toremove item=element}
					<li>{$element->name|escape} {$element->revision} ({$element->type|escape})</li>
			{/foreach}</ul>
			</li>
		{/if}
		{if $installask.toinstall}
			<li>The following mods will be <strong>installed</strong>:
			<ul>{foreach from=$installask.toinstall item=element}
				{if $element->repository eq 'unavailable'}
					<li>{$element->name|escape} ({$element->type|escape}) but is not in any repository</li>
				{else}
					<li>{$element->name|escape} {$element->revision} ({$element->type|escape}){if $element->repository eq 'remote'} (will be downloaded){/if}</li>
				{/if}
			{/foreach}</ul>
			</li>
		{/if}
		{if $installask.toupgrade}
			<li>The following mods will be <strong>upgraded</strong>:
			<ul>{foreach from=$installask.toupgrade item=element}
				{if $element.to->repository eq 'unavailable'}
					<li>{$element.to->name|escape} ({$element.to->type|escape}) but is not in any repository</li>
				{else}
					<li>{$element.to->name|escape} {$element.to->revision} (from {$element.from->revision}) ({$element.to->type|escape}){if $element.to->repository eq 'remote'} (will be downloaded){/if}</li>
				{/if}
			{/foreach}</ul>
			</li>
		{/if}
		{if $installask.suggests}
			<li>Suggested packages:
			<ul>{foreach from=$installask.suggests item=element}
				{if $element->repository eq 'unavailable'}
					<li>{$element[0]->name|escape} ({$element[0]->type|escape}) but is not in any repository</li>
				{else}
					<li><input type='checkbox' onchange='update_button_install();' name='install-wants[]' value='{$element[0]->modname|escape}' />{$element[0]->name|escape} ({$element[0]->type|escape})</li>
				{/if}
			{/foreach}</ul>
			</li>
		{/if}
		</ul>

		<br />
		{if $installask.wanted}
			<input type='submit' id='button_install' name='button-install' value="{tr}Install{/tr}"{if $installask.unavailable} style='display: none;'{/if} />
		{elseif $installask.wantedtoremove}
			<input type='submit' id='button_install' name='button-remove' value="{tr}Remove{/tr}"{if $installask.unavailable} style='display: none;'{/if} />
		{/if}
{jq}
function update_button_install() {
	var button=document.getElementById('button_install');
	button.name='button-check';
	button.value='Check again';
	button.style.display='';
}
{/jq}
	</div>
</form>
{/if}
<br />
{if $tikifeedback}
	<br />
	{section name=n loop=$tikifeedback}
		<div class="simplebox {if $tikifeedback[n].num > 0} highlight{/if}">
			{if $tikifeedback[n].num > 0}{icon _id=delete alt="Alert" style="vertical-align:middle;"}{/if}{$tikifeedback[n].mes}
		</div><br />
	{/section}
{/if}

{if not $installask}
	<form method="get" action="tiki-mods.php">
		{tr}Find{/tr}
		<input type="text" name="find" value="{$find|escape}" />
		<input type="submit" name="f" value="{tr}Find{/tr}" />
		{tr}in{/tr} <select name="type" onchange="this.form.submit();">
			<option value="">{tr}all types{/tr}</option>
			{foreach key=it item=i from=$types}
				<option value="{$it|escape}"{if $it eq $type} selected="selected"{/if}>{$it}</option>
			{/foreach}
		</select>
		{tr}for version{/tr}
		<select name="version" onchange="this.form.submit();">
			<option value="">{tr}All versions{/tr}</option>
			{foreach key=it item=i from=$versions}
				<option value="{$i|escape}"{if $i eq $version} selected="selected"{/if}>{$it}</option>
			{/foreach}
		</select>
		{tr}or later{/tr}
	</form>
	{if !$display}
		{tr}No mods found.{/tr}
	{/if}
	
	<table cellspacing="0" cellpadding="2" border="0" class="normal">
		{foreach key=type item=i from=$display}
			<tr><td colspan="{if $prefs.feature_mods_provider eq 'y'}3{else}2{/if}">
				{assign var=thistype value=$type|escape:"url"}
				{button href="tiki-mods.php?type=$thistype$findarg" _title="{tr}Display only this type{/tr}" _text=$type}
			</td><td colspan="7">&nbsp;</td>
			</tr>
			{cycle values="odd,even" print=false}
			{foreach key=item item=it from=$display.$type}
				<tr class="{if $focus and $focus eq $display.$type.$item->name}focus{else}{cycle}{/if}">
				{if $prefs.feature_mods_provider eq 'y'}
					{assign var=mod value=$public.$type.$item->modname}
					{if $public.$type.$item}
						{if ModsLib::revision_compare($dist.$mod->revision,$local.$type.$item->revision) < 0}
							<td class="mods_hilite">
								<a href="tiki-mods.php?unpublish={$public.$type.$item->modname|escape:"url"}{$findarg}{$typearg}{$versionarg}" title="{tr}Unpublish{/tr}">[x]</a>
								{if $dist.$mod}
									<a href="tiki-mods.php?republish={$public.$type.$item->modname|escape:"url"}{$findarg}{$typearg}{$versionarg}" title="{tr}Republish{/tr}">{$dist.$mod->revision}&gt;{$local.$type.$item->revision}</a>
								{/if}
							</td>
						{else}
							<td class="mods_hilite">
								<a href="tiki-mods.php?unpublish={$public.$type.$item->modname|escape:"url"}{$findarg}{$typearg}{$versionarg}" title="{tr}Unpublish{/tr}">[x]</a>
								{if $dist.$mod}{$dist.$mod->revision}{/if}
							</td>
						{/if}
					{elseif $local.$type.$item}
						<td class="mods_revision"><a href="tiki-mods.php?publish={$local.$type.$item->modname|escape:"url"}{$findarg}{$typearg}{$versionarg}" title="{tr}Publish{/tr}">[+]</a></td>
					{else}
						<td class="mods_revision"></td>
					{/if}
				{/if}
				
				{if $remote.$type.$item}
					{if ModsLib::revision_compare($remote.$type.$item->revision, $local.$type.$item->revision) > 0}
						<td class="mods_hilite">
							<a href="tiki-mods.php?dl={$remote.$type.$item->modname|escape:"url"}-{$remote.$type.$item->revision}{$findarg}{$typearg}{$versionarg}" title="{tr}Download{/tr}">{$remote.$type.$item->revision}</a>
						</td>
					{else}
						<td class="mod_hilite">
							<a href="tiki-mods.php?dl={$remote.$type.$item->modname|escape:"url"}-{$remote.$type.$item->revision}{$findarg}{$typearg}{$versionarg}" title="{tr}Download{/tr}">{$remote.$type.$item->revision}</a>
						</td>
					{/if}
				{else}
					<td></td>
				{/if}
				
				{if $local.$type.$item}
					<td><b><a href="tiki-mods.php?focus={$local.$type.$item->modname|escape:"url"}{$findarg}{$typearg}{$versionarg}">{$local.$type.$item->name}</a></b></td>
					<td>{$local.$type.$item->version[0]}</td>
					<td>{$local.$type.$item->licence}</td>
					<td>{$local.$type.$item->description}</td>
					{if $installed.$type.$item} 
						{if $local.$type.$item->isnewerthan($installed.$type.$item)}
							<td class="mods_update">{$installed.$type.$item->revision}
								{if $iswritable}<a href="tiki-mods.php?action=upgrade&amp;package={$local.$type.$item->modname|escape:"url"}{$findarg}{$typearg}{$versionarg}">-&gt;{$local.$type.$item->revision}</a>{/if}
							</td>
						{elseif $remote.$type.$item and $remote.$type.$item->isnewerthan($installed.$type.$item)}
							<td class="mods_update">{$installed.$type.$item->revision}
								{if $iswritable}<a href="tiki-mods.php?action=upgrade&amp;package={$remote.$type.$item->modname|escape:"url"}{$findarg}{$typearg}{$versionarg}">-&gt;{$remote.$type.$item->revision}</a>{/if}
							</td>
						{else}
							<td class="mod_hilite">{$installed.$type.$item->revision}</td>
						{/if}
					<td class="mods_hilite">{if $iswritable}<a href="tiki-mods.php?action=remove&amp;package={$local.$type.$item->modname|escape:"url"}{$findarg}{$typearg}{$versionarg}">{tr}Remove{/tr}</a>{/if}</td>
					{else}
					<td></td>
					<td colspan="3">
						{if $iswritable}
							<a href="tiki-mods.php?action=install&amp;package={$local.$type.$item->modname|escape:"url"}{$findarg}{$typearg}{$versionarg}">{tr}Install{/tr}</a>
						{else}
							<b><s>{tr}Install{/tr}</s></b>
						{/if}
					</td>
					{/if}
				{else}
					<td>{$remote.$type.$item->name}</td>
					<td>{$remote.$type.$item->version[0]}</td>
					<td>{$remote.$type.$item->licence}</td>
					<td>{$remote.$type.$item->description}</td>
					<td></td>
					<td>
						{if $iswritable}
							<a href="tiki-mods.php?action=install&amp;package={$remote.$type.$item->modname|escape:"url"}{$findarg}{$typearg}{$versionarg}">{tr}Install{/tr}</a>
						{else}
							<b><s>{tr}Install{/tr}</s></b>
						{/if}
					</td>
				{/if}
				</tr>
				{if $focus and $focus eq $local.$type.$item->modname}
					<tr class="{cycle}">
						<td colspan="{if $prefs.feature_mods_provider eq 'y'}9{else}8{/if}">
							<table>
								<tr>
									<td>
										<div class="simplebox">
											{if $more->docurl}Documentation :<br />{foreach key=ku item=iu from=$more->docurl}<a href="{$iu}">{$iu}</a><br />{/foreach}{/if}
											{if $more->devurl}Development : <br />{foreach key=ku item=iu from=$more->devurl}<a href="{$iu}">{$iu}</a><br />{/foreach}{/if}
											{if $more->help}{$more.help}<br />{/if}
											{if $more->help}{$more.help}<br />{/if}
											{if $more->author}{tr}Author:{/tr} {$more->author[0]}<br />{/if}
											{if $more->licence}{tr}licence:{/tr} {$more->licence}<br />{/if}
											
											{tr}Last Modification:{/tr} {$more->lastmodif}<br />
											{tr}by:{/tr} {$more->contributor[0]}<br />
										</div>
									</td><td>
										{foreach key=kk item=ii from=$more->files}
											{$ii[0]} -&gt; <b>{$ii[1]}</b><br />
										{/foreach}
									</td>
								</tr>
							</table>
						</td>
					</tr>
				{/if}
			{/foreach}
		{/foreach}
	</table>
{/if}
{/strip}
