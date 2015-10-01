{* $Id$ *}

<div class='col-md-9 edit-zone'> {* grid class added here for correct layout in form-horizontal tracker plugin; hopefully no unintended side-effects *}
	{if $textarea__toolbars ne 'n'}
		<div class='textarea-toolbar nav-justified' id='{$textarea_id|default:editwiki}_toolbar'>
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

