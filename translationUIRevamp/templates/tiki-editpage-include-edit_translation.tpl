{title}{tr}Update '{$page|escape}'{/tr}{/title}

{* Side by side layout *} 

<div style="height:500px;width:50%;float:left;white-space:nowrap;" id="translation_interface_left_pane">
	{if $diff_style}
		{include file='tiki-editpage-include-show_diff.tpl'}
	{/if}
</div>

<div style="overflow:auto;width:50%;float:right;white-space:nowrap;" id="translation_interface_right_pane">
	{include file='tiki-editpage-include-wiki_editor.tpl'}
</div>


{* Top to bottom layout *}

{*
<div style="height:50%;float:top;white-space:nowrap;" id="translation_interface_top_pane">
	{if $diff_style}
		{include file='tiki-editpage-include-show_diff.tpl'}
	{/if}
</div>

<div style="overflow:auto;height:50%;float:bottom;white-space:nowrap;" id="translation_interface_bottom_pane">
	{include file='tiki-editpage-include-wiki_page_editor.tpl'}
</div>
*}


