{if $template}
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

<div class="navbar">
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
		{cycle values="odd,even" print=false}
		{section name=user loop=$files}
		<tr class="{cycle}">
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
				{tr}If you edit this (or any TPL) file via the built-in editor below, any javascript may be sanitized or completely stripped out 
					by Tiki security filtering, which would cause certain functions to stop working (e.g. menus no longer collapse or expand){/tr}
			</li>
			<li>
				{tr}You should only modify default header.tpl and other important files via a text code editor, through console, 
					or SSH, or FTP edit commands--and only if you know what you are doing! ;-){/tr}
			</li>
			<li>
				{tr}Extensive customizations can be made safely through the <a href="tiki-admin.php?page=look" class="titletips" 
					title="Look & Feel">Look & Feel</a> admin panel custom code or general layout areas.{/tr}
			</li>
			<li>
				{tr}To be safe and to make upgrades easier, it is recommended that you create a custom theme before modifying tpl files.
					See <a href="http://doc.tiki.org/Customizing+Themes" class="titletips" title="Customizing Themes help page">
					doc.tiki.org/Customizing+Themes</a> for how to do that.{/tr}
			</li>
		</ul>
		{/remarksbox}
	{/if}

	<form action="tiki-edit_templates.php" method="post">
		<textarea name="data" rows="20" cols="80"
			{if $prefs.feature_edit_templates ne 'y' or $tiki_p_edit_templates ne 'y'}
				class="readonly" readonly="readonly"
			{/if}
		>
			{$data|escape}
		</textarea>
		<div align="center">
			<input type="hidden" name="template" value="{$template|escape}" />
			{if $prefs.feature_edit_templates eq 'y' and $tiki_p_edit_templates eq 'y'}
				{if $prefs.style_local eq 'n'}
					<input type="submit" name="save" value="{tr}Save{/tr}" />
				{/if}
				<input type="submit" name="saveTheme" value="{tr}Save Only in the Theme:{/tr} {$prefs.style|replace:'.css':''}" />
				{if $prefs.style_local eq 'y'}
					<a class="blogt" href="tiki-edit_templates.php?template={$template}&amp;delete=y}">
						<img src="pics/icons/cross.png" alt="{tr}Delete the copy in the theme:{/tr} {$prefs.style|replace:'.css':''}" 
							title="{tr}Delete the copy in the theme:{/tr} {$prefs.style|replace:'.css':''}" />
					</a>
				{/if}
			{/if}
		</div>
	</form>
{/if}
