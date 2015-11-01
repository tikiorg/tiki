{title help="Edit+CSS"}{tr}Edit CSS{/tr}{/title}
<div class="t_navbar">
	{if ($prefs.feature_view_tpl eq 'y' || $prefs.feature_edit_templates eq 'y') && $tiki_p_edit_templates == 'y'}
		{button href="tiki-edit_templates.php" class="btn btn-default" _text="{tr}View Templates{/tr}"}
	{/if}
</div>
<form method="post" action="tiki-edit_css.php" class="form">
	{if $action eq "edit"}
		<div class="form-group">
			<label for="theme" class="control-label">
				{tr}Theme{/tr}
			</label>
			<input type="text" name="theme" value="{$theme}" class="form-control" readonly>
			<small class="help-block">CSS {tr}file{/tr}: {$file}</small>
			<div class="input-group">
				<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
				<input type="submit" class="btn btn-primary btn-sm" name="save_and_view" value="{tr}Save{/tr} &amp; {tr}View{/tr}">
				{button href="tiki-edit_css.php?theme=$theme" _class="btn-sm" _text="{tr}Cancel{/tr}"}
			</div>
			{if $tikidomain}
				{tr}The file will be saved in:{/tr} themes/{$tikidomain}
			{/if}
			{if !empty($theme) && !$writable}
				{remarksbox type="warning" title="{tr}Warning{/tr}"}
					{tr}Cannot write the file{/tr}: {$file}
				{/remarksbox}
			{/if}
		</div>
		<div class="form-group">
			<textarea data-syntax="css" data-codemirror="true" data-line-numbers="true" name="data" wrap="virtual" class="form-control" rows="24">{$data|escape}</textarea>
		</div>
	{else}
		{if $try_active}
			{remarksbox type="tip" title="{tr}Currently trying{/tr}: {$try_theme}{if $try_theme_option}/{$try_theme_option}{/if}" close="n"}
				<input type="submit" class="btn btn-default btn-sm" name="cancel_try" value="{tr}Cancel Try{/tr}">
			{/remarksbox}
		{/if}
		<div class="form-group clearfix">
			<label for="theme" class="control-label">
				{tr}Theme{/tr}
			</label>
			<select name="theme" class="form-control input-sm" required>
				<option value="">{tr}Select{/tr}...</option>
				{foreach from=$themes key=theme_key item=theme_name}
					<option value="{$theme_key|escape}" {if $theme eq $theme_key}selected="selected"{/if}>{$theme_name}</option>
				{/foreach}
			</select>
			{if $theme}	
				<small class="help-block">CSS {tr}file{/tr}: {$file}</small>
			{/if}
			<div class="input-group">
				<input type="submit" class="btn btn-default btn-sm" name="try" value="{tr}Try{/tr}">
				<input type="submit" class="btn btn-default btn-sm" name="view" value="{tr}View{/tr}">
				<input type="submit" class="btn btn-default btn-sm" name="edit" value="{tr}Edit{/tr}">
			</div>
		</div>
		{section name=l loop=$css}
			<div style="padding:4px;text-align:left">
				<div style="float:right;">{$css[l].id|escape}</div>
				{if $css[l].comment}
					<div class="comment"><pre><em>{$css[l].comment|escape}</em></pre></div>
				{/if}
				{section name=i loop=$css[l].items}
					<div style="font-weight: bold;">{$css[l].items[i]|escape}</div>
				{/section}
				{foreach item=v key=a from=$css[l].attributes}
					<div style="margin-left:10px;">
						<code>{$a|string_format:"%'.-22s"|escape} : {$v|string_format:"%-56.s"|escape}</code>
						{if $v[0] eq "#"}
							<span style="height:8px;width:30px;background-color:{$v|escape};">&nbsp;&nbsp;&nbsp;&nbsp;</span>
						{elseif $a|truncate:6:"" eq "border"}
							<span style="height:8px;width:30px;{$a|escape}:{$v|escape};">&nbsp;&nbsp;&nbsp;&nbsp;</span>
						{/if}
					</div>
				{/foreach}
			</div>
			<hr>
		{/section}
	{/if}
</form>
