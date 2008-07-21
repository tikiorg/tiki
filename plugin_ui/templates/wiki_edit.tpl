{if isset($smarty.request.zoom) && $smarty.request.zoom|cat:'.tpl' eq $smarty.template}
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

<div id='edit-zone'>
	{if $zoom_mode eq 'n' and $prefs.quicktags_over_textarea neq 'y'}<table style="border:0; width:100%"><tr><td style="border:0;">{/if}
	<div id='textarea-toolbar' style='padding:3px; font-size:10px; {if $zoom_mode eq 'n' and $prefs.quicktags_over_textarea neq 'y'}float:left;{/if}'>
		{if $zoom_mode eq 'n'}
		<div style='float:left; margin-right:5px'>{include file='textareasize.tpl' area_name='editwiki' formId='editpageform' ToolbarSet='Tiki'}</div>
		{/if}
		{include file=tiki-edit_help_tool.tpl area_name='editwiki' zoom_enable='y}
	</div>
	{if $zoom_mode eq 'n' and $prefs.quicktags_over_textarea neq 'y'}</td><td style="border:0;">{/if}
	<textarea id='editwiki' class='wikiedit' name='edit' rows='{$rows}' cols='{$cols}' style='width:99%'>{$pagedata|escape:'htmlall':'UTF-8'}</textarea>
	{if $zoom_mode eq 'n' and $prefs.quicktags_over_textarea neq 'y'}</td></tr></table>{/if}
</div>

{if $zoom_mode eq 'y'}
<div style="width:99%; text-align:right;">
{include file='wiki_edit_actions.tpl'}
</div>

<script type='text/javascript'>
<!--//--><![CDATA[//><!--
document.getElementById('editwiki').style.height = ( getWindowHeight() - document.getElementById('textarea-toolbar').offsetHeight - 10 ) + 'px';
document.getElementById('tiki-center').style.padding = '0px';
document.body.style.backgroundColor = '#c1c1c1';
//--><!]]>
</script>
</form>
{/if}
