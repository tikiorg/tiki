<div class="tocnav">
	<div class="clearfix">
		<div style="float: left; width: 100px">
  
    {if $home_info}{if $home_info.page_alias}{assign var=icon_title value=$home_info.page_alias}{else}{assign var=icon_title value=$home_info.pageName}{/if}
    	{self_link page=$home_info.pageName structure=$home_info.pageName page_ref_id=$home_info.page_ref_id}{icon _id='house' alt="{tr}TOC{/tr}" title=$icon_title}{/self_link}
    {/if}

    {if $prev_info and $prev_info.page_ref_id}{if $prev_info.page_alias}{assign var=icon_title value=$prev_info.page_alias}{else}{assign var=icon_title value=$prev_info.pageName}{/if}
    	<a href="{sefurl page=$prev_info.pageName structure=$home_info.pageName page_ref_id=$prev_info.page_ref_id}">
    		{icon _id='resultset_previous' alt="{tr}Previous page{/tr}" title=$icon_title}
    	</a>
    {else}
    	<img src="img/icons2/8.gif" alt="" height="1" width="8" />
    {/if}

    {if $parent_info}{if $parent_info.page_alias}{assign var=icon_title value=$parent_info.page_alias}{else}{assign var=icon_title value=$parent_info.pageName}{/if}
    	<a href="{sefurl page=$parent_info.pageName structure=$home_info.pageName page_ref_id=$parent_info.page_ref_id}">
    		{icon _id='resultset_up' alt="{tr}Parent page{/tr}" title=$icon_title}
    	</a>
    {else}
    	<img src="img/icons2/8.gif" alt="" height="1" width="8" />
    {/if}

    {if $next_info and $next_info.page_ref_id}{if $next_info.page_alias}{assign var=icon_title value=$next_info.page_alias}{else}{assign var=icon_title value=$next_info.pageName}{/if}<a href="{sefurl page=$next_info.pageName structure=$home_info.pageName page_ref_id=$next_info.page_ref_id}">{icon _id='resultset_next' alt="{tr}Next page{/tr}" title=$icon_title}</a>{else}<img src="img/icons2/8.gif" alt="" height="1" width="8" />{/if}

		</div>
  		<div style="float: left;">
{if $struct_editable eq 'y'}
    <form action="tiki-editpage.php" method="post">
    	<div class="form>
			<input type="hidden" name="current_page_id" value="{$page_info.page_ref_id}" />
			<input type="text" id="structure_add_page" name="page" />
			{if $prefs.javascript_enabled eq 'y' and $prefs.feature_jquery_autocomplete eq 'y'}
			{jq}$("#structure_add_page").tiki("autocomplete", "pagename");{/jq}
			{/if}
			{* Cannot add peers to head of structure *}
			{if $page_info and !$parent_info }
			<input type="hidden" name="add_child" value="checked" /> 
			{else}
			<input type="checkbox" name="add_child" /> {tr}Child{/tr}
			{/if}      
			<input type="submit" name="insert_into_struct" value="{tr}Add Page{/tr}" />
    	</div>
    </form>
{/if}
		</div>
	</div>
  	<div>
  	{self_link  _script="tiki-edit_structure.php" page_ref_id=$home_info.page_ref_id _alt="{tr}Structure{/tr}" _title="{tr}Structure{/tr} ($cur_pos)"}{icon _id='chart_organisation' alt="{tr}Structure{/tr}" title="{tr}Structure{/tr} ($cur_pos)"}{/self_link}&nbsp;&nbsp;
    {section loop=$structure_path name=ix}
      {if $structure_path[ix].parent_id}&nbsp;{$prefs.site_crumb_seper}&nbsp;{/if}
	  <a href="{sefurl page=$structure_path[ix].pageName structure=$home_info.pageName page_ref_id=$structure_path[ix].page_ref_id}">
      {if $structure_path[ix].page_alias}
        {$structure_path[ix].page_alias}
	  {else}
        {$structure_path[ix].pageName|pagename}
	  {/if}
	  </a>
	{/section}
	</div>
</div>