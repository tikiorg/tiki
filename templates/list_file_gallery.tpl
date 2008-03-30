{* $Id$ *}

{if ( isset($tree) and count($tree) gt 0 && $tiki_p_list_file_galleries != 'n' && $fgal_options.show_explorer.value eq 'y' && $tiki_p_view_fgal_explorer eq 'y' ) or ( $gallery_path neq '' && $fgal_options.show_path.value eq 'y' && $tiki_p_view_fgal_path eq 'y' ) }
<div class="fgal_top_bar" style="height:16px; vertical-align:middle">

{if isset($tree) and count($tree) gt 0 && $tiki_p_list_file_galleries != 'n' && $fgal_options.show_explorer.value eq 'y' && $tiki_p_view_fgal_explorer eq 'y'}
{if $prefs.javascript_enabled eq 'y'}

  <div id="fgalexplorer_close" style="float:left; vertical-align:middle; display:{if isset($smarty.session.tiki_cookie_jar.show_fgalexplorer) and $smarty.session.tiki_cookie_jar.show_fgalexplorer eq 'y'}none{else}inline{/if};"><a href="#" onclick="flip('fgalexplorer','');hide('fgalexplorer_close',false);show('fgalexplorer_open',false);return false;">{icon _id='application_side_tree' alt="{tr}Show Tree{/tr}"}</a></div>

  <div id="fgalexplorer_open" style="float:left; vertical-align:middle; display:{if ! isset($smarty.session.tiki_cookie_jar.show_fgalexplorer) or $smarty.session.tiki_cookie_jar.show_fgalexplorer neq 'y'}none{else}inline{/if};"><a href="#" onclick="flip('fgalexplorer','');hide('fgalexplorer_open',false);show('fgalexplorer_close',false);return false;">{icon _id='application_side_contract' alt="{tr}Hide Tree{/tr}"}</a></div>

{else}

  <div style="float:left; vertical-align:middle">
  {if isset($smarty.request.show_fgalexplorer) and $smarty.request.show_fgalexplorer eq 'y'}
    {self_link _icon='application_side_contract' show_fgalexplorer='n'}{tr}Hide Tree{/tr}{/self_link}
  {else}
    {self_link _icon='application_side_tree' show_fgalexplorer='y'}{tr}Show Tree{/tr}{/self_link}
  {/if}
  </div>

{/if}
{/if}

{if $gallery_path neq '' && $fgal_options.show_path.value eq 'y' && $tiki_p_view_fgal_path eq 'y'}
  <div class="gallerypath" style="vertical-align:middle">&nbsp;&nbsp;{$gallery_path}</div>
{/if}

</div>
{/if}

<table border="0" cellpadding="3" cellspacing="3" width="100%" style="clear: both">
  <tr>
    {if isset($tree) && count($tree) gt 0 && $tiki_p_list_file_galleries != 'n' && $fgal_options.show_explorer.value eq 'y' && $tiki_p_view_fgal_explorer eq 'y'}
    <td width="25%" class="fgalexplorer" id="fgalexplorer" {if ( ! isset($smarty.session.tiki_cookie_jar.show_fgalexplorer) or $smarty.session.tiki_cookie_jar.show_fgalexplorer neq 'y') and ( ! isset($smarty.request.show_fgalexplorer) or $smarty.request.show_fgalexplorer neq 'y' ) }style="display:none"{/if}>
      <div style="overflow-x:auto; overflow-y:hidden">
      {include file='file_galleries.tpl'}
      </div>
    </td>
    <td width="75%" class="fgallisting">
    {else}
    <td width="100%" class="fgallisting">
    {/if}
      <div style="padding:1px; overflow-x:auto; overflow-y:hidden;">

      <form name="fgalformid" id="fgalform" method="post" action="{$smarty.server.PHP_SELF}{if $filegals_manager eq 'y'}?filegals_manager=y{/if}" enctype="multipart/form-data">
        <input type="hidden" name="galleryId" value="{$gal_info.galleryId|escape}" />
        <input type="hidden" name="find" value="{$find|escape}" />

	{if $prefs.fgal_asynchronous_indexing eq 'y'}<input type="hidden" name="fast" value="y" />{/if} 
        {if !empty($sort_mode)}<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />{/if}
        {if isset($file_info)}<input type="hidden" name="fileId" value="{$file_info.fileId|escape}" />{/if}
        {if isset($page)}<input type="hidden" name="page" value="{$page|escape}" />{/if}
      
        {assign var=nbCols value=0}
        {assign var=other_columns value=''}
        {assign var=other_columns_selected value=''}
        {assign var=show_infos value='n'}
      
        <table class="normal">
          <tr>
          {if $gal_info.show_checked ne 'n' and $tiki_p_admin_file_galleries eq 'y'}
            {assign var=nbCols value=`$nbCols+1`}
            <td class="heading" style="width:1%">&nbsp;</td>
          {/if}
          {if ( $prefs.use_context_menu_icon eq 'y' or $prefs.use_context_menu_text eq 'y' ) and $gal_info.show_action neq 'n' and $prefs.javascript_enabled eq 'y'}
            {assign var=nbCols value=`$nbCols+1`}
            <td class="heading" style="width:1%">&nbsp;</td>
          {/if}
      
          {foreach from=$fgal_listing_conf item=item key=propname}
            {if isset($item.key)}
              {assign var=key_name value=$item.key}
            {else}
              {assign var=key_name value="show_$propname"}
            {/if}
            {if isset($gal_info.$key_name) and $gal_info.$key_name eq 'o'}
              {assign var=show_infos value='y'}
              {if $sort_mode eq $propname|cat:'_asc' or $sort_mode eq $propname|cat:'_desc'}
                {assign var=other_columns_selected value=$propname}
              {else}
                {capture assign=other_columns}
                  {$other_columns}
                  {self_link sort_mode=$propname|cat:'_asc'}{$fgal_listing_conf.$propname.name}{/self_link}<br />
                {/capture}
              {/if}
            {/if}
            {if isset($gal_info.$key_name) and ( $gal_info.$key_name eq 'y' or $gal_info.$key_name eq 'i' or $gal_info.$key_name eq 'a' or $propname eq 'name' ) }
              {assign var=propval value=$item.name}
              {assign var=link_title value=''}
              {assign var=td_args value=' class="heading"'}
      
              {if $gal_info.$key_name eq 'i' or $propname eq 'type' or ( $propname eq 'lockedby' and $gal_info.$key_name eq 'a') }
                {assign var=propval value=$item.name[0]}
                {assign var=link_title value=$item.name}
                {assign var=td_args value=$td_args|cat:' style="width:1%; text-align:center"'}
              {/if}
      
              {if $propname eq 'name' and ( $gal_info.show_name eq 'a' or $gal_info.show_name eq 'f' ) }
                {assign var=nbCols value=`$nbCols+1`}
                <td{$td_args}>{self_link _class="tableheading" _sort_arg="sort_mode" _sort_field='filename'}{tr}Filename{/tr}{/self_link}</td>
              {/if}
              {if $propname neq 'name' or ( $gal_info.show_name eq 'a' or $gal_info.show_name eq 'n' ) }
                {assign var=nbCols value=`$nbCols+1`}
                <td{$td_args}>{self_link _class="tableheading" _sort_arg="sort_mode" _sort_field=$propname _title=$link_title}{$propval}{/self_link}</td>
              {/if}
            {/if}
          {/foreach}
      
          {if $other_columns neq ''}
            {capture name=over_other_columns}{strip}
            <div class='opaque'>
              <div class='box-title'>{tr}Other Sorts{/tr}</div>
              <div class='box-data'>
                {if $other_columns_selected neq ''}
                  {self_link sort_mode='NULL'}{tr}No Additionnal Sort{/tr}{/self_link}
                  <hr />
                {/if}
                {$other_columns}
              </div>
            </div>
            {/strip}{/capture}
          {/if}
      
          {if $other_columns_selected neq ''}
            {assign var=nbCols value=`$nbCols+1`}
            <td class="heading">
            {self_link _class='tableheading' _sort_arg='sort_mode' _sort_field=$other_columns_selected _title=$fgal_listing_conf.$other_columns_selected.name}{$fgal_listing_conf.$other_columns_selected.name}{/self_link}
            </td>
          {/if}
      
          {if ( $prefs.use_context_menu_icon neq 'y' and $prefs.use_context_menu_text neq 'y' ) or $gal_info.show_action eq 'y' or $prefs.javascript_enabled neq 'y'}
            {assign var=nbCols value=`$nbCols+1`}
            <td class="heading">{tr}Actions{/tr}</td>
          {/if}
      
          {if ( $other_columns neq '' or $other_columns_selected neq '' ) and $prefs.javascript_enabled eq 'y'}
            {assign var=nbCols value=`$nbCols+1`}
            <td class="heading" style="width:1%">
            {if $other_columns neq ''}
              <a href='#' {popup trigger="onClick" sticky=1 mouseoff=1 fullhtml="1" text=$smarty.capture.over_other_columns|escape:"javascript"|escape:"html"} title='{tr}Other Sorts{/tr}'>{/if}{icon _id='timeline_marker' alt='{tr}Other Sorts{/tr}' title=''}{if $other_columns neq ''}</a>
            {/if}
            </td>
          {/if}

          </tr>

          {cycle values="odd,even" print=false}
          {section name=changes loop=$files}
          
            {if ( $prefs.use_context_menu_icon eq 'y' or $prefs.use_context_menu_text eq 'y' ) and $gal_info.show_action neq 'y'}
              {capture name=over_actions}{strip}
              <div class='opaque'>
                <div class='box-title'>{tr}Actions{/tr}</div>
                <div class='box-data'>
                  {include file=fgal_context_menu.tpl menu_icon=$prefs.use_context_menu_icon menu_text=$prefs.use_context_menu_text}
                </div>
              </div>
              {/strip}{/capture}
            {/if}
     
            {capture name=over_preview}{strip}
            {if $files[changes].type|truncate:6:'':true eq 'image/'}
            <div class='opaque'>
              <img src="tiki-download_file.php?fileId={$files[changes].id}&thumbnail" />
            </div>
            {/if}
            {/strip}{/capture}
 
            {assign var=nb_over_infos value=0}
            {capture name=over_infos}{strip}
            <div class='opaque'>
              <div class='box-title'>{tr}Properties{/tr}</div>
              <div class='box-data'>
                <div>
                {foreach item=prop key=propname from=$fgal_listing_conf}
                  {if isset($prop.key)}
                    {assign var=propkey value=$item.key}
                  {else}
                    {assign var=propkey value="show_$propname"}
                  {/if}
                  {assign var=propval value=$files[changes].$propname}
          
                  {* Format property values *}
                  {if $propname eq 'created' or $propname eq 'lastmodif'}
                    {assign var=propval value=$propval|tiki_long_date}
                  {elseif $propname eq 'last_user' or $propname eq 'author' or $propname eq 'creator'}
                    {assign var=propval value=$propval|username}
                  {elseif $propname eq 'size'}
                    {assign var=propval value=$propval|kbsize}
                  {/if}
            
                  {if isset($gal_info.$propkey) and $propval neq '' and ( $gal_info.$propkey eq 'a' or $gal_info.$propkey eq 'o' ) }
                    <b>{$fgal_listing_conf.$propname.name}</b>: {$propval}<br />
                    {assign var=nb_over_infos value=`$nb_over_infos+1`}
                  {/if}
                {/foreach}
                </div>
              </div>
            </div>
            {/strip}{/capture}

            {if $nb_over_infos gt 0}
              {assign var=over_infos value=$smarty.capture.over_infos}
            {else}
              {assign var=over_infos value=''}
            {/if}

          <tr>
      
          {if $gal_info.show_checked neq 'n' and $tiki_p_admin_file_galleries eq 'y'}
            <td style="text-align:center;" class="{cycle advance=false}">
              {if $files[changes].isgal eq 1}
                {assign var='checkname' value='subgal'}
              {else}
                {assign var='checkname' value='file'}
              {/if}
              <input type="checkbox" name="{$checkname}[]" value="{$files[changes].id|escape}" {if $smarty.request.$checkname and in_array($files[changes].id,$smarty.request.$checkname)}checked="checked"{/if} />
            </td>
          {/if}
      
          {if ( $prefs.use_context_menu_icon eq 'y' or $prefs.use_context_menu_text eq 'y' ) and $gal_info.show_action neq 'n' and $prefs.javascript_enabled eq 'y'}
            <td style="white-space: nowrap" class="{cycle advance=false}">
              <a class="fgalname" title="{tr}Actions{/tr}" href="#" {popup trigger="onClick" sticky=1 mouseoff=1 fullhtml="1" text=$smarty.capture.over_actions|escape:"javascript"|escape:"html"} style="padding:0; margin:0; border:0">{icon _id='wrench' alt='{tr}Actions{/tr}'}</a>
            </td>
          {/if}
      
          {foreach from=$fgal_listing_conf item=item key=propname}
            {if isset($item.key)}
              {assign var=key_name value=$item.key}
            {else}
              {assign var=key_name value="show_$propname"}
            {/if}
            {if isset($gal_info.$key_name) and ( $gal_info.$key_name eq 'y' or $gal_info.$key_name eq 'a' or $gal_info.$key_name eq 'i' or $propname eq 'name' or ( $other_columns_selected neq '' and $propname eq $other_columns_selected ) ) }
              {assign var=propval value=$files[changes].$propname|escape}
          
              {* build link *}
              {capture assign=link}{strip}
                {if $files[changes].isgal eq 1}
                  href="tiki-list_file_gallery.php?galleryId={$files[changes].id}{if $filegals_manager eq 'y'}&amp;filegals_manager=y{/if}"
                {else}
                  {if $filegals_manager eq 'y'}
                    href="javascript:window.opener.SetUrl('{$url_path}tiki-download_file.php?fileId={$files[changes].id}&display');javascript:window.close() ;"
                  {elseif $tiki_p_download_files eq 'y'}
                    {if $gal_info.type eq 'podcast' or $gal_info.type eq 'vidcast'}
                      href="{$download_path}{$files[changes].path}"
                    {else}
                    href="tiki-download_file.php?fileId={$files[changes].id}"
                    {/if}
                  {/if}
                  {if $smarty.capture.over_preview neq ''}
                    {popup fullhtml="1" text=$smarty.capture.over_preview|escape:"javascript"|escape:"html"}
                  {/if}
                {/if}
              {/strip}{/capture}
          
              {* Format property values *}
              {if $propname eq 'id' or $propname eq 'name'}
                {assign var=propval value="<a class='fgalname' $link>$propval</a>"}
              {elseif $propname eq 'created' or $propname eq 'lastmodif'}
                {assign var=propval value=$propval|tiki_short_date}
              {elseif $propname eq 'last_user' or $propname eq 'author' or $propname eq 'creator'}
                {assign var=propval value=$propval|userlink}
              {elseif $propname eq 'size'}
                {assign var=propval value=$propval|kbsize}
              {elseif $propname eq 'type'}
                {if $files[changes].isgal eq 1}
                  {capture assign=propval}{icon _id='folder' class=''}{/capture}
                {else}
                  {assign var=propval value=$files[changes].filename|iconify:$files[changes].type}
                {/if}
              {elseif $propname eq 'description' and $gal_info.max_desc gt 0}
                {assign var=propval value=$propval|truncate:$gal_info.max_desc:"...":false}
              {elseif $propname eq 'lockedby' and $propval neq ''}
                {if $gal_info.show_lockedby eq 'i' or $gal_info.show_lockedby eq 'a'}
                  {assign var=propval value=$propval|username}
                  {capture assign=propval}{icon _id='lock_gray' class='' alt="{tr}Locked by{/tr}: "|cat:$propval}{/capture}
                {else}
                  {assign var=propval value=$propval|userlink}
                {/if}
              {/if}
          
              {if $propname eq 'name' and ( $gal_info.show_name eq 'a' or $gal_info.show_name eq 'f' ) }
                <td class="{cycle advance=false}">
                  {if $link neq ''}<a class='fgalname' {$link}>{/if}{$files[changes].filename}{if $link neq ''}</a>{/if}
                </td>
              {/if}
              {if $other_columns_selected neq '' and $propname eq $other_columns_selected}
                {assign var=other_columns_selected_val value=$propval}
              {else}
                {if $propname neq 'name' or ( $gal_info.show_name eq 'a' or $gal_info.show_name eq 'n' ) }
                  <td class="{cycle advance=false}">{$propval}</td>
                {/if}
              {/if}
            {/if}
          {/foreach}
      
          {if $other_columns_selected neq ''}
            <td class="{cycle advance=false}">{$other_columns_selected_val}</td>
          {/if}
          
          {if ( $prefs.use_context_menu_icon neq 'y' and $prefs.use_context_menu_text neq 'y' ) or $gal_info.show_action eq 'y' or $prefs.javascript_enabled neq 'y'}
            <td class="{cycle advance=false}">{include file=fgal_context_menu.tpl}</td>
          {/if}
          
          {if ( $other_columns neq '' or $other_columns_selected neq '' ) and $prefs.javascript_enabled eq 'y'}
            <td class="{cycle advance=false}">
            {if $show_infos eq 'y'}
              {if $over_infos eq ''}
                {icon _id='information_gray' class='' alt='{tr}No information{/tr}'}
              {else}
                <a class="fgalname" href="#" {popup fullhtml="1" text=$over_infos|escape:"javascript"|escape:"html"} style="cursor:help">{icon _id='information' class='' title=''}</a>
              {/if}
            {/if}
            </td>
          {/if}
      
          </tr>
          {cycle print=false}

          {sectionelse}
          <tr><td class="odd" colspan="{$nbCols}">
            <b>{tr}No records found{/tr}</b>
          </td></tr>
          {/section}
{if $files}
          {if $gal_info.show_checked ne 'n' and $tiki_p_admin_file_galleries eq 'y'}
          <tr><td colspan="{$nbCols}"><input name="switcher" id="clickall" type="checkbox" onclick="switchCheckboxes(this.form,'file[]',this.checked); switchCheckboxes(this.form,'subgal[]',this.checked);"/>
            <label for="clickall">{tr}Select All{/tr}</label>
          </td></tr>
          {/if}
{/if}
        </table>
      
        {if $files and $gal_info.show_checked ne 'n' and $tiki_p_admin_file_galleries eq 'y'}
        <div>
          <div style="float:left">
          {tr}Perform action with checked:{/tr} 
          {if !isset($file_info)}
            {if $offset}<input type="hidden" name="offset" value="{$offset}" />{/if}
            {icon _id='arrow_right' _tag='input_image' name='movesel' alt='{tr}Move{/tr}' title='{tr}Move Selected Files{/tr}' style='vertical-align: middle;'}
          {/if}
          {icon _id='cross' _tag='input_image' _confirm='{tr}Are you sure you want to delete the selected files?{/tr}' name='delsel' alt='{tr}Delete{/tr}' style='vertical-align: middle;'}
          </div>
          {if $smarty.request.movesel_x and !isset($file_info)} 
          <div>
            {tr}Move to{/tr}:
            <select name="moveto">
              {section name=ix loop=$all_galleries}
                {if $all_galleries[ix].galleryId ne $gal_info.galleryId}
                  <option value="{$all_galleries[ix].id|escape}">{$all_galleries[ix].name}</option>
                {/if}
              {/section}
            </select>
            <input type='submit' name='movesel' value='{tr}Move{/tr}' />
          </div>
          {/if}
        </div>
        <br style="clear:both"/>
        {/if}

      </form>
      
        {reindex_file_pixel id=$reindex_file_id}<br />

        {pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}

      </div>
    </td>
  </tr>
</table>
