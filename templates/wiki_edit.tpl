{* $Id$ *}

{if $update_translation eq 'y'} 
	{* When updating a translation, split window in two frames with
	   full inline diff on the left, and fullscreen edit on the right*}
    <table>
    <tr>
    	<td colspan="2">{tr}Translate changes which appear in pink on the left, in the page shown on the right.{/tr}</td>
    </tr>
    <tr>
   	 {* Left side shows diffs that have happened in source language*}
    <td width="50%" height="80%">
    	<div style='height:80%'>
			{include file='pagehistory.tpl'}
		</div>
	</td>

	<td width="50%">
	
{/if}

{if isset($zoom_mode) and $zoom_mode eq 'y'}
<form id='editpageform' name='editpageform' method='post' action='tiki-editpage.php' enctype='multipart/form-data'>
{* The line below is used to generate all input hidden tags needed to keep modifications (e.g. categories, freetags, ...) between zoom mode and normal mode *}
{query _type='form_input' edit=NULL zoom=NULL zoom_value=NULL zoom_x=NULL zoom_y=NULL}
{/if}

{if !isset($textarea_attributes)}
	{assign var=textarea_attributes value=" rows='$rows' cols='$cols' style='width:99%'"}
{/if}
<div id='edit-zone'>
	<div class='textarea-toolbar' id='{$textarea_id|default:editwiki}_toolbar'>
		{toolbars area_name=$textarea_name|default:edit}
	</div>
	<textarea id="{$textarea_id|default:editwiki}" class="{$textarea_class|default:wikiedit}" name="{$textarea_name|default:edit}" {$textarea_attributes}>{$pagedata}</textarea>
</div>

{if $diff_style}
	<input type="hidden" name="oldver" value="{$diff_oldver|escape}"/>
	<input type="hidden" name="newver" value="{$diff_newver|escape}"/>
										<input type="hidden" name="source_page" value="{$source_page|escape}"/>
{/if}
{if isset($zoom_mode) and $zoom_mode eq 'y'}
<div id='{$textarea_id|default:editwiki}_actions' style="width:99%; text-align:right;">
{include file='wiki_edit_actions.tpl'}
</div>
{jq}
$jq('#{{$textarea_id|default:editwiki}}').height($jq(window).height() - $jq('#{{$textarea_id|default:editwiki}}_toolbar').height() - $jq('#{{$textarea_id|default:editwiki}}_actions').height() - 15);
{/jq}
</form>
{/if}

{if $update_translation eq 'y'}
	{* Close the table used for side-by-side display of diff of source language and
	   edit form for the target language *}
	</td>
	</tr>
	</table> 
{/if}
