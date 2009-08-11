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
	<div id='textarea-toolbar' style='padding:3px; font-size:10px;'>
		{toolbars area_name=$textarea_name|default:edit zoom_enable=$enlarge|default:y}
	</div>
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
</div>

{if $zoom_mode eq 'y'}
<div style="width:99%; text-align:right;">
{include file='wiki_edit_actions.tpl'}
</div>

<script type='text/javascript'>
<!--//--><![CDATA[//><!--
document.getElementById('{$textarea_id|default:editwiki}').style.height = ( getWindowHeight() - document.getElementById('textarea-toolbar').offsetHeight - 10 ) + 'px';
document.getElementById('tiki-center').style.padding = '0px';
document.body.style.backgroundColor = '#c1c1c1';
//--><!]]>
</script>
</form>
{/if}
