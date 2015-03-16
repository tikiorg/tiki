{if isset($template)}
	{title help="Edit+Templates" url="tiki-edit_templates.php?mode=listing&template=$template"}
		{if $prefs.feature_edit_templates ne 'y' or $tiki_p_edit_templates ne 'y'}
			{tr}View template:{/tr}
		{else}
			{tr}Edit template:{/tr}
		{/if}
		{$template}
	{/title}
{else}
	{title help="Edit+Templates"}{tr}Edit templates{/tr}{/title}
{/if}

<div class="t_navbar margin-bottom-md">
	{if $prefs.feature_editcss eq 'y'}
		{button href="tiki-edit_css.php" _text="{tr}Edit CSS{/tr}"}
	{/if}
	{if $mode eq 'editing'}
		{button href="tiki-edit_templates.php" _text="{tr}Template listing{/tr}"}
	{/if}
</div>

{if $mode eq 'listing'}
	<h2>
		{tr}Available templates:{/tr}
	</h2>
	<table border="1" cellpadding="0" cellspacing="0" >
		<tr>
			<th>{tr}Template{/tr}</th>
		</tr>

		{section name=user loop=$files}
		<tr>
			<td>
				<a class="link" href="tiki-edit_templates.php?template={$files[user]}">
					{$files[user]}
				</a>
			</td>
		</tr>
		{sectionelse}
			{norecords _colspan=1}
		{/section}
	</table>
{/if}
{if $mode eq 'editing'}
	{if $prefs.feature_edit_templates eq 'y' and $tiki_p_edit_templates eq 'y'}
		{remarksbox type="warning" title="{tr}Important!{/tr}" highlight="y"}
		<ul>
			<li>
				{tr}You should only modify default header.tpl and other important files via a text code editor, through console,
					or SSH, or FTP edit commands--and only if you know what you are doing! ;-){/tr}
			</li>
			<li>
				{tr}Extensive customizations can be made safely through the <a href="tiki-admin.php?page=look" class="tips"
					title="Look & Feel">Look & Feel</a> admin panel custom code or general layout areas.{/tr}
			</li>
			<li>
				{tr}To be safe and to make upgrades easier, it is recommended that you create a custom theme before modifying tpl files.
					See <a href="http://doc.tiki.org/Customizing+Themes" class="tips" title="Customizing Themes help page">
					doc.tiki.org/Customizing+Themes</a> for how to do that.{/tr}
			</li>
		</ul>
		{/remarksbox}
	{/if}

	<form action="tiki-edit_templates.php" method="post">
		<textarea data-syntax="smarty" data-codemirror="true" data-line-numbers="true" name="data" rows="20" cols="80"
			{if $prefs.feature_edit_templates ne 'y' or $tiki_p_edit_templates ne 'y'}
				class="readonly" readonly="readonly"
			{/if}
		>{$data|escape}</textarea>
		<div align="center">
			<input type="hidden" name="template" value="{$template|escape}">
			{if $prefs.feature_edit_templates eq 'y' and $tiki_p_edit_templates eq 'y'}
				{if $style_local eq 'n'}
					<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
				{/if}
				<input type="submit" class="btn btn-default btn-sm" name="saveTheme" value="{tr}Save Only in the Theme:{/tr} {$prefs.style|replace:'.css':''}">
				{if $style_local eq 'y'}
					<a href="tiki-edit_templates.php?template={$template|escape}&amp;delete=y">
						<img src="img/icons/cross.png" alt="{tr}Delete the copy in the theme:{/tr} {$prefs.style|replace:'.css':''}"
							title="{tr}Delete the copy in the theme:{/tr} {$prefs.style|replace:'.css':''}">
					</a>
				{/if}
			{/if}
		</div>
	</form>
{/if}
