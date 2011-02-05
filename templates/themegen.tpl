{* $Id$ *}
{strip}
<div id="themegenerator_content">
	<div class="box-data">			
		{if !empty($smarty.session.tg_preview)}
			<div style="float:right;">
				<em class="highlight">{tr}* PREVIEW *{/tr}</em>
			</div>
		{/if}
		<label for="tg_css_file" style="display: inline;">{tr}Modifying:{/tr} </label>
		<select id="tg_css_file" name="tg_css_file">
			{foreach from=$tg_css_files item=val key=key}
				<option value="{$key}"{if $key eq $tg_css_file} selected="selected"{/if}>{$val}</option>
			{/foreach}
		</select>
	</div>
	<div class="themegenerator">
		<ul>
			{foreach from=$tg_data item=tg_section_data key=tg_section}
				<li><a href="#tg_section_{$tg_section}">{$tg_section_data.title}</a></li>
			{/foreach}
		</ul>
		{foreach from=$tg_data item=tg_section_data key=tg_section}

			<div id="tg_section_{$tg_section}">
				<div class="clearfix tgTools">
					<input type="checkbox" class="tgToggle" id="tgToggle_{$tg_section}" />
					<label for="tgToggle_{$tg_section}">{tr}Toggle checkboxes{/tr}</label>
					<input type="checkbox" class="tgToggleChanged" id="tgToggleChanged_{$tg_section}" />
					<label for="tgToggleChanged_{$tg_section}">{tr}Toggle changed{/tr}</label>
					<input type="checkbox" class="tgLivePreview" checked="checked" id="tgLivePreview_{$tg_section}" />
					<label for="tgLivePreview_{$tg_section}">{tr}Live preview{/tr}</label>
					{button _text="{tr}Reset selected{/tr}" _class="tgResetSection" href="#"}
				</div>
				{foreach from=$tg_section_data.types item=tg_data_type key=tg_type}
					<label for="tg_{$tg_type}" class="ui-corner-top">{$tg_data_type.title}</label>
					<ul id="tg_{$tg_type}" class="{$tg_section}Items tgItems clearfix ui-corner-bottom ui-corner-tr">
						{foreach from=$tg_data_type.items item=tg_item}
							<li class="tgItem{if $tg_item.old neq $tg_item.new} changed{/if}">
								<div class="clearfix tips" title="{$tg_data_type.title}|
										{tr}Found in:{/tr}
										{$tg_item.contexts}
										{if $tg_item.old neq $tg_item.new}
											{tr 0=$tg_item.old 1=$tg_item.new}Changed from %0 to %1{/tr}
										{else}
											{tr}Unchanged{/tr}
										{/if}">
									{if $tg_data_type.selector eq 'color'}
										<div class="colorSelector">
											<div style="background-color:{$tg_item.new};">&nbsp;</div>
										</div>
										<input type="text" name="tg_swaps[{$tg_type}][{$tg_item.old}]"
												value="{$tg_item.new}" class="tgValue" />
									{elseif $tg_data_type.selector eq 'size'}
										 <div class="tgLabel">
											{$tg_item.old}
										</div>
										<input type="text" name="tg_swaps[{$tg_type}][{$tg_item.old}]"
												value="{$tg_item.new}" class="tgValue" />
									{elseif $tg_data_type.selector eq 'borderstyle'}
										 <div class="tgLabel">
											{$tg_item.old}
										</div>
										<select name="tg_swaps[{$tg_type}][{$tg_item.old}]" class="tgBorderStyle">
											<option value="dotted"{if $tg_item.new eq 'dotted'} selected="selected"{/if}">dotted</option>
											<option value="dashed"{if $tg_item.new eq 'dashed'} selected="selected"{/if}">dashed</option>
											<option value="solid"{if $tg_item.new eq 'solid'} selected="selected"{/if}">solid</option>
											<option value="double"{if $tg_item.new eq 'double'} selected="selected"{/if}">double</option>
											<option value="groove"{if $tg_item.new eq 'groove'} selected="selected"{/if}">groove</option>
											<option value="ridge"{if $tg_item.new eq 'ridge'} selected="selected"{/if}">ridge</option>
											<option value="inset"{if $tg_item.new eq 'inset'} selected="selected"{/if}">inset</option>
											<option value="outset"{if $tg_item.new eq 'outset'} selected="selected"{/if}">outset</option>
										</select>
												
									{else}{* "fontfamily", "text" etc *}
										 <div class="tgLabel tgWide">
											{$tg_item.old|truncate:999}
										</div>
										<input type="text" name="tg_swaps[{$tg_type}][{$tg_item.old}]"
												value="{$tg_item.new}" class="tgValue tgWide" />
									{/if}
								</div>
								<input type="checkbox" value="{$tg_item.old}" />
							</li>
						{foreachelse}
							{tr}No definitions found{/tr}
						{/foreach}
					</ul>
				{foreachelse}
					{tr}No types found{/tr}
				{/foreach}
			</div>
		{/foreach}
	</div>
</div>
{/strip}
