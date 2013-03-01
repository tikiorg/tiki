{* $Id$ *}

<div class='edit-zone'>
	{if $textarea__toolbars ne 'n'}
		<div class='textarea-toolbar' id='{$textarea_id|default:editwiki}_toolbar'>
			{toolbars area_id=$textarea_id|default:editwiki comments=$comments switcheditor=$switcheditor section=$toolbar_section}
		</div>
	{/if}
	<textarea {$textarea_attributes}>{$textareadata|escape}</textarea>
</div>

{if isset($diff_style) and $diff_style}
	<input type="hidden" name="oldver" value="{$diff_oldver|escape}">
	<input type="hidden" name="newver" value="{$diff_newver|escape}">
	<input type="hidden" name="source_page" value="{$source_page|escape}">
{/if}

