{if isset($zoom_mode) and $zoom_mode eq 'y'}
<form id='editpageform' name='editpageform' method='post' action='tiki-editpage.php' enctype='multipart/form-data'>
{* The line below is used to generate all input hidden tags needed to keep modifications (e.g. categories, freetags, ...) between zoom mode and normal mode *}
{query _type='form_input' edit=NULL zoom=NULL zoom_value=NULL zoom_x=NULL zoom_y=NULL}
{/if}

{if !isset($textarea_attributes)}
	{assign var=textarea_attributes value=" rows='$rows' cols='$cols' style='width:99%'"}
{/if}
<div id='edit-zone'>
	{if $textarea__toolbars ne 'n'}
	<div class='textarea-toolbar' id='{$textarea_id|default:editwiki}_toolbar'>
		{toolbars area_id=$textarea_id|default:editwiki comments=$comments switcheditor=$switcheditor section=$toolbar_section}
	</div>
	{/if}
	<textarea id="{$textarea_id|default:editwiki}" class="{$textarea_class|default:wikiedit}" name="{$textarea_name|default:edit}" {$textarea_attributes}>{$pagedata}</textarea>
</div>

{if isset($diff_style) and $diff_style}
	<input type="hidden" name="oldver" value="{$diff_oldver|escape}"/>
	<input type="hidden" name="newver" value="{$diff_newver|escape}"/>										<input type="hidden" name="source_page" value="{$source_page|escape}"/>

{/if}
{if isset($zoom_mode) and $zoom_mode eq 'y'}
<div id='{$textarea_id|default:editwiki}_actions' style="width:99%; text-align:right;">
{include file='wiki_edit_actions.tpl'}
</div>
{jq}
$('#{{$textarea_id|default:editwiki}}').height($(window).height() - $('#{{$textarea_id|default:editwiki}}_toolbar').height() - $('#{{$textarea_id|default:editwiki}}_actions').height() - 15);
{/jq}
</form>
{/if}
