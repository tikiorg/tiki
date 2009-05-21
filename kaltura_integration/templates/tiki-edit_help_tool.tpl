{* $Id$ *}
<div class="quicktag">
{literal}
<script type="text/javascript">
<!--//--><![CDATA[//><!--
function taginsert(area_name,tagid)
{
//fill variables
{/literal}
  var tag = new Array();
  {section name=qtg loop=$quicktags}
  tag[{$quicktags[qtg].tagId}]='{$quicktags[qtg].taginsert|escape:"javascript"}';
  {/section}
//done
{literal} 
	if (tag[tagid].indexOf("popup_plugin_form") == 0)  {
		tag[tagid] = tag[tagid].replace("popup_plugin_form(", "popup_plugin_form('"+area_name+"',");
		eval(tag[tagid]);
	} else {
		insertAt(area_name,tag[tagid]);
	}
}
//--><!]]>
</script>
{/literal}
{if (!isset($zoom_mode) || $zoom_mode eq 'n') and $prefs.quicktags_over_textarea neq 'y'}
  <br clear="all" />
  <a href="javascript:flip('helptool{$qtnum}');" onclick="needToConfirm=false;" class="link">
    {icon _id='bullet_toggle_plus' alt='+'}&nbsp;{tr}Quicktags{/tr} ...
  </a>
  <br /><br />
{ /if}
{*get_strings {tr}text, bold{/tr}
              {tr}text, italic{/tr}
              {tr}text, underline{/tr}
              {tr}table{/tr}
              {tr}table new{/tr}
              {tr}external link{/tr}
              {tr}wiki link'{/tr}
              {tr}heading1{/tr}
              {tr}title bar{/tr}
              {tr}box{/tr}
              {tr}rss feed{/tr}
              {tr}dynamic content{/tr}
              {tr}tagline{/tr}
              {tr}horizontal rule{/tr}
              {tr}center text{/tr}
              {tr}colored text{/tr}
              {tr}dynamic variable{/tr}
              {tr}image{/tr}
              {tr}New wms Metadata{/tr}
              {tr}New Class{/tr}
              {tr}New Projection{/tr}
              {tr}New Query{/tr}
              {tr}New Scalebar{/tr}
              {tr}New Layer{/tr}
              {tr}New Label{/tr}
              {tr}New Reference{/tr}
              {tr}New Legend{/tr}
              {tr}New Web{/tr}
              {tr}New Outputformat{/tr}
              {tr}New Mapfile{/tr} 
              {tr}Add image from File Gallery{/tr} 
              {tr}quote{/tr} 
              {tr}code{/tr} 
              {tr}flash{/tr} 
              *}
<div id='helptool{$qtnum}'
  {assign var=show value="show_helptool"|cat:$qtnum}

{if (!isset($zoom_mode) || $zoom_mode eq 'n') and $prefs.quicktags_over_textarea neq 'y'}
  {if isset($smarty.session.tiki_cookie_jar.$show) and $smarty.session.tiki_cookie_jar.$show eq 'y'}
    style="display:block;"
  {else}
    style="display:none;"
  {/if}
{/if}
>
  <div class='helptool-user' style="float:left">
  
  {if (!isset($zoom_mode) || $zoom_mode eq 'n') and $prefs.quicktags_over_textarea neq 'y'}
    <div style="white-space:nowrap;">
    {cycle name='cycle'|cat:$qtnum values=$qtcycle|default:",,,</div><div>" advance=false print=false}
  {/if}
    {section name=qtg loop=$quicktags}
	  {assign var='label' value=$quicktags[qtg].taglabel|regex_replace:"/^ +/":""}
      <a class="icon" title="{tr interactive='n'}{$label}{/tr}" href="javascript:taginsert('{$area_name}','{$quicktags[qtg].tagId}');" onclick="needToConfirm = false;"><img class="icon" src='{$quicktags[qtg].tagicon}' alt='{tr interactive="n"}{$label}{/tr}' title='{tr interactive="n"}{$label}{/tr}' /></a>
      {if (!isset($zoom_mode) || $zoom_mode eq 'n') and $prefs.quicktags_over_textarea neq 'y'}{cycle name='cycle'|cat:$qtnum}{/if}
    {/section}

  {if ($prefs.feature_filegals_manager eq 'y') and ($prefs.feature_file_galleries eq 'y') and ($tiki_p_list_file_galleries eq 'y') and ($prefs.feature_wiki_pictures eq 'y')}
    {literal}
<script type="text/javascript">
<!--//--><![CDATA[//><!--
if (typeof fgals_window == "undefined") {
	var fgals_window = null;
}
function openFgalsWindow() {
	if(fgals_window && fgals_window.document) {
		fgals_window.focus();
	} else {{/literal}
		fgals_window=window.open('{filegal_manager_url area_name=$area_name}','_blank','menubar=1,scrollbars=1,resizable=1,height=500,width=800,left=50,top=50');
	{literal}}
}
//--><!]]>
</script>
    {/literal}
    <a title="{tr}Add Image from File Gallery{/tr}" href="#" onclick="needToConfirm=false; openFgalsWindow();return false;">{icon _id='pictures' alt='{tr}Add Image from File Gallery{/tr}'}</a>
    {if (!isset($zoom_mode) || $zoom_mode eq 'n') and $prefs.quicktags_over_textarea neq 'y'}{cycle name='cycle'|cat:$qtnum}{/if}
  {/if}

    <a title="{tr}special chars{/tr}" href="#" onclick="javascript:window.open('tiki-special_chars.php?area_name={$area_name}','','menubar=no,width=252,height=25');">{icon _id='world_edit' alt='{tr}special characters{/tr}'}</a>

  {if (!isset($zoom_mode) || $zoom_mode eq 'n') and $prefs.quicktags_over_textarea neq 'y'}
    </div>
  {/if}
  </div>

  {if (!isset($zoom_mode) || $zoom_mode eq 'n') and $prefs.quicktags_over_textarea neq 'y'}
  <hr style="width:90%; clear:both;" />
  {/if}

  {if $tiki_p_admin eq 'y' or $tiki_p_admin_quicktags eq 'y'}
  <div class='helptool-admin'
  {if (!isset($zoom_mode) || $zoom_mode eq 'n') and $prefs.quicktags_over_textarea neq 'y'}
	style="float: left"
  {else}
	style="float: right; border-left: medium double lightgrey; padding-left:8px; margin-left:8px"
  {/if}>
	<a href="tiki-admin_quicktags.php{if isset($section)}?category={$section|escape:'url'}{/if}" onclick="needToConfirm = true;">{icon _id='wrench' alt="{tr}Admin Quicktags{/tr}"}</a>
  </div>
  {/if}

  {if $prefs.feature_template_zoom eq 'y' && $zoom_enable eq 'y'}
  <div class='helptool-zoom'
  {if (!isset($zoom_mode) || $zoom_mode eq 'n') and $prefs.quicktags_over_textarea neq 'y'}
	style="float: left"
  {else}
	style="float: right; border-left: medium double lightgrey; padding-left:8px; margin-left:8px"
  {/if}>
    {if isset($smarty.request.zoom) && $smarty.request.zoom eq 'wiki_edit'}
      {icon _id='application_put' _tag='input_image' alt="{tr}Leave Fullscreen Edit{/tr}" name="preview" onclick="needToConfirm=false;"}
    {else}
      {* The line below is a hack for IE6 to get a value for the zoom button *}
      <input type="hidden" name="zoom_value" value="wiki_edit" />
      {icon _id='application_get' _tag='input_image' alt="{tr}Fullscreen Edit{/tr}" name="zoom" value="wiki_edit" onclick="needToConfirm=false;"}
    {/if}
  </div>
  {/if}

  <br clear="all" />

</div> <!-- helptool{$qtnum} -->
</div> <!-- Quicktag -->
