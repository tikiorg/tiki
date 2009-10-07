{if $prefs.feature_template_zoom eq 'y' && isset($smarty.request.zoom) && $smarty.request.zoom|cat:'.tpl' eq $smarty.template}
	{assign var=zoom_mode value='y'}
	{popup_init src="lib/overlib.js"}
{else}
	{assign var=zoom_mode value='n'}
{/if}

{if $zoom_mode eq 'y'}
<form id='editpageform' name='editpageform' method='post' action='tiki-editpage.php' enctype='multipart/form-data'>
{* The line below is used to generate all input hidden tags needed to keep modifications (e.g. categories, freetags, ...) between zoom mode and normal mode *}
{query _type='form_input' edit=NULL zoom=NULL zoom_value=NULL zoom_x=NULL zoom_y=NULL}
{/if}

{if !isset($textarea_attributes)}
	{assign var=textarea_attributes value=" rows='$rows' cols='$cols' style='width:99%'"}
{/if}
<div id='edit-zone'>
	{if isset($quicktags) or isset($enlarge)}
		{if $zoom_mode eq 'n' and $prefs.quicktags_over_textarea neq 'y'}<table style="border:0; width:100%"><tr><td style="border:0;">{/if}
	<div id='textarea-toolbar' style='padding:3px; font-size:10px; {if $zoom_mode eq 'n' and $prefs.quicktags_over_textarea neq 'y'}float:left;{/if}'>
		{if $zoom_mode eq 'n'}
		<div style='float:left; margin-right:5px'>{include file='textareasize.tpl' area_name='editwiki' formId='editpageform' ToolbarSet='Tiki'}</div>
		{/if}
		{if isset($quicktags)}
			{include file=tiki-edit_help_tool.tpl area_name='editwiki' zoom_enable='y}
		{/if}
	</div>
	{/if}
	{if $zoom_mode eq 'n' and $prefs.quicktags_over_textarea neq 'y'}</td><td style="border:0;">{/if}
	<!--autosave -->
	{capture name=autosave}{if $prefs.feature_ajax eq 'y' and $prefs.feature_ajax_autosave eq 'y' and $noautosave neq 'y'}{autosave id=$textarea_id|default:editwiki default=$pagedata preview=$preview}{else}{$pagedata}{/if}{/capture}
	{if $prefs.feature_ajax eq 'y' and $prefs.feature_ajax_autosave eq 'y' and $noautosave neq 'y' and $has_autosave eq 'y'} 
	{remarksbox type="warning" title="{tr}AutoSave{/tr}"}
	{tr}If you want the saved version instead of the autosaved one{/tr}&nbsp;{self_link noautosave='y' _ajax='n'}{tr}Click Here{/tr}{/self_link}
	{/remarksbox}
	{/if} 
	<textarea id="{$textarea_id|default:editwiki}" class="{$textarea_class|default:wikiedit}" name="{$textarea_name|default:edit}"{$textarea_attributes}>{$smarty.capture.autosave|escape}</textarea>
	{if $prefs.feature_ajax eq 'y' && $prefs.feature_ajax_autosave eq 'y'}
		<!-- autosave -->
		<script type='text/javascript'>
		<!--//--><![CDATA[//><!--
			register_id('{$textarea_id|default:editwiki}');
			auto_save();
		//--><!]]>
		</script>
		<!-- autosave -->
	{/if}
	{if $zoom_mode eq 'n' and $prefs.quicktags_over_textarea neq 'y'}</td></tr></table>{/if}
</div>

{if $zoom_mode eq 'y'}
<div style="width:99%; text-align:right;" id="textarea-actions">
{include file='wiki_edit_actions.tpl'}
</div>

<script type='text/javascript'>
<!--//--><![CDATA[//><!--
document.getElementById('{$textarea_id|default:editwiki}').style.height = ( getWindowHeight() - document.getElementById('textarea-toolbar').offsetHeight - document.getElementById('textarea-actions').offsetHeight - 10 ) + 'px';
document.getElementById('tiki-center').style.padding = '0px';
document.body.style.backgroundColor = '#c1c1c1';
//--><!]]>
</script>
</form>
{/if}
