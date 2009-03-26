{title help="Edit+CSS"}{tr}Edit Style Sheet{/tr}{/title}

<div class="navbar">
{if ($prefs.feature_view_tpl eq 'y' || $prefs.feature_edit_templates eq 'y') && $tiki_p_edit_templates == 'y'}
	{button href="tiki-edit_templates.php" _text="{tr}View Templates{/tr}"}
{/if}
</div>

<form method="post" action="tiki-edit_css.php">
	{tr}Style Sheet{/tr} : 
{if $action eq "edit"}
	<input type="text" name="editstyle" value="{$editstyle}" />
	<input type="submit" name="save" value="{tr}Save{/tr}" />
	<input type="submit" name="save2" value="{tr}Save{/tr} &amp; {tr}Display{/tr}" />
	{button  href="tiki-edit_css.php?editstyle=$editstyle" _text="{tr}Cancel{/tr}"}
	{if $tikidomain}
		{tr}The file will be saved in:{/tr} styles/{$tikidomain}
	{/if}
	<div style="padding:4px;border-bottom:1px solid #c3b3a3;">
		<textarea name="data" rows="42" cols="80" wrap="virtual" style="padding:7px;padding-right:0;">{$data|escape}</textarea>
	</div>
{else}
	{assign var=shortStyle value=$prefs.style|replace:'.css':''}
	<select name="editstyle">
		<option value="" style="background-color:#efdece;color:#766656;border-bottom:1px dashed #787878;">{tr}choose a stylesheet{/tr}</option>
	{section name=t loop=$list}
		<option value="{$list[t]|escape}"{if $list[t] eq $editstyle or (empty($editstyle) and $list[t] eq $shortStyle)} selected="selected"{/if}>{$list[t]|escape}</option>
	{/section}
	</select>
	<input type="submit" name="try" value="{tr}Try{/tr}" />
	<input type="submit" name="display" value="{tr}Display{/tr}" />
	<input type="submit" name="edit" value="{tr}Edit{/tr}" />

	{section name=l loop=$css}
	<div style="padding:4px;">
		<div style="float:right;">{$css[l].id|escape}</div>
		<div class="comment"><pre><em>{$css[l].comment|escape}</em></pre></div>
		{section name=i loop=$css[l].items}
		<div style="font-weight: bold;">{$css[l].items[i]|escape}</div>
		{/section}
		{foreach item=v key=a from=$css[l].attributes}
		<div style="margin-left:10px;"><code>{$a|string_format:"%'.-22s"|escape} : {$v|string_format:"%-56.s"|escape}</code>
		{if $v[0] eq "#"}
			<span style="height:8px;width:30px;background-color:{$v|escape};">&nbsp;&nbsp;&nbsp;&nbsp;</span>
		{elseif $a|truncate:6:"" eq "border"}
			<span style="height:8px;width:30px;{$a|escape}:{$v|escape};">&nbsp;&nbsp;&nbsp;&nbsp;</span>
		{/if}
		</div>
		{/foreach}
	</div>
	<hr />
	{/section}
{/if}

</form>
