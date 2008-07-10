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
  insertAt(area_name,tag[tagid]);
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
{*get_strings {tr}bold{/tr}
              {tr}italic{/tr}
              {tr}underline{/tr}
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
              {tr}hr{/tr}
              {tr}center text{/tr}
              {tr}colored text{/tr}
              {tr}dynamic variable{/tr}
              {tr}Image{/tr}
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
      <a title="{tr interactive='n'}{$quicktags[qtg].taglabel}{/tr}" href="javascript:taginsert('{$area_name}','{$quicktags[qtg].tagId}');" onclick="needToConfirm = false;"><img class="icon" src='{$quicktags[qtg].tagicon}' alt='{tr interactive="n"}{$quicktags[qtg].taglabel}{/tr}' title='{tr interactive="n"}{$quicktags[qtg].taglabel}{/tr}' border='0' /></a>
      {if (!isset($zoom_mode) || $zoom_mode eq 'n') and $prefs.quicktags_over_textarea neq 'y'}{cycle name='cycle'|cat:$qtnum}{/if}
    {/section}

  {if $prefs.feature_filegals_manager eq 'y'}
    <a title="{tr}Add another image{/tr}" href="#" onclick="javascript:needToConfirm = false;javascript:window.open('{$url_path}tiki-list_file_gallery.php?gallery_id=0&amp;filegals_manager=y','_blank','menubar=1,scrollbars=1,resizable=1,height=400,width=800');return false;">{icon _id='image' alt='{tr}Add another image{/tr}'}</a>
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
	<a href="tiki-admin_quicktags.php" onclick="needToConfirm = true;">{icon _id='wrench' alt="{tr}Admin Quicktags{/tr}"}</a>
  </div>
  {/if}

  {if $prefs.feature_template_zoom eq 'y'}
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
