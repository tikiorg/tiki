<a class="pagetitle" href="tiki-admin_html_pages.php">{tr}Admin HTML pages{/tr}</a>
  
{if $prefs.feature_help eq 'y'}
  <a href="{$prefs.helpurl}Html+Pages" target="tikihelp" class="tikihelp" title="{tr}Admin Html Pages{/tr}">
  {icon _id='help'}</a>
{/if}

{if $prefs.feature_view_tpl eq 'y'}
  <a href="tiki-edit_templates.php?template=tiki-admin_html_pages.tpl" target="tikihelp" class="tikihelp" title="{tr}View template{/tr}: {tr}Admin Html Pages Template{/tr}">
  {icon _id='shape_square_edit'}</a>
{/if}


{if $pageName ne ''}
  <div class="navbar"><a href="tiki-admin_html_pages.php" class="linkbut">{tr}Create new HTML page{/tr}</a></div>
{/if}

<div class="rbox" name="tip">
  <div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
  <div class="rbox-data" name="tip">{tr}Use {literal}{ed id=name}{/literal} or {literal}{ted id=name}{/literal} to insert dynamic zones{/tr}
  </div>
</div>

{if $preview eq 'y'}
  <h2>{tr}Preview{/tr}</h2>
  <div class="wikitext">{$parsed}</div>
{/if}

{if $pageName eq ''}
  <h2>{tr}Create new HTML page{/tr}</h2>
{else}
  <h2>{tr}Edit this HTML page:{/tr} {$pageName}</h2>
{/if}

<form action="tiki-admin_html_pages.php" method="post" id='editpageform'>
  <input type="hidden" name="pageName" value="{$pageName|escape}" />
  <table class="normal">
    <tr class="formcolor">
      <td class="formcolor" style="width:150px;">{tr}Page name{/tr}:</td>
      <td class="formcolor"><input type="text" maxlength="255" size="40" name="pageName" value="{$info.pageName|escape}" />
      </td>
    </tr>

    {if $tiki_p_use_content_templates eq 'y'}
    <tr class="formcolor">
        <td class="formcolor">{tr}Apply template{/tr}:</td>
        <td class="formcolor">
          <select name="templateId"{if !$templates} disabled="disabled"{/if} onchange="javascript:document.getElementById('editpageform').submit();">
            <option value="0">{tr}none{/tr}</option>
            {section name=ix loop=$templates}
            <option value="{$templates[ix].templateId|escape}">{tr}{$templates[ix].name}{/tr}</option>
            {/section}
          </select>
        </td>
      </tr>
    {/if}

    <tr class="formcolor">
      <td>{tr}Type{/tr}:</td>
      <td>
        <select name="type">
          <option value='d'{if $info.type eq 'd'} selected="selected"{/if}>{tr}Dynamic{/tr}</option>
          <option value='s'{if $info.type eq 's'} selected="selected"{/if}>{tr}Static{/tr}</option>
        </select>
      </td>
    </tr>
    <tr class="formcolor">
      <td>{tr}Refresh rate (if dynamic){/tr}:</td>
      <td>
        <input type="text" size="5" name="refresh" value="{$info.refresh|escape}" /> {tr}seconds{/tr}
      </td>
    </tr>

    <tr class="formcolor">
      <td>
        {tr}Content{/tr}:<br />
{include file="textareasize.tpl" area_name='htmlcode' formId='editpageform'} 
      </td>
      <td>
        <textarea name="content" id="htmlcode" rows="25" style="width:95%;">{$info.content|escape}</textarea>
      </td>
    </tr>
    <tr class="formcolor">
      <td></td>
      <td>
        <input type="submit" name="preview" value="{tr}Preview{/tr}" /> 
        <input type="submit" name="save" value="{tr}Save{/tr}" />
      </td>
    </tr>
  </table>
</form>

<br />
<h2>{tr}HTML pages{/tr}</h2>
<div align="center">
{if $channels}
  {include file='find.tpl' _sort_mode='y'}
{/if}
<table class="normal">
  <tr>
    <td class="heading">
      <a class="tableheading" href="tiki-admin_html_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a>
    </td>
    
    <td class="heading">
      <a class="tableheading" href="tiki-admin_html_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}">{tr}Type{/tr}</a>
    </td>

    <td class="heading">
      <a class="tableheading" href="tiki-admin_html_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Last Modif{/tr}</a>
    </td>

    <td class="heading" style="width:100px;">{tr}Action{/tr}</td>
  </tr>
  
  {cycle values="odd,even" print=false}
  {section name=user loop=$channels}
  <tr>
    <td class="{cycle advance=false}">{$channels[user].pageName}</td>
    <td class="{cycle advance=false}">{$channels[user].type} {if $channels[user].type eq 'd'}({$channels[user].refresh} secs){/if}</td>
    <td class="{cycle advance=false}">{$channels[user].created|tiki_short_datetime}</td>
    <td class="{cycle advance=true}">
      <a class="link" href="tiki-admin_html_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;pageName={$channels[user].pageName|escape:"url"}" title="{tr}Edit{/tr}">
        {icon _id='page_edit'}
      </a>
      
      <a class="link" href="tiki-page.php?pageName={$channels[user].pageName|escape:"url"}" title="View">
        {icon _id='monitor' alt='{tr}View{/tr}'}
      </a>
      
      <a class="link" href="tiki-admin_html_page_content.php?pageName={$channels[user].pageName|escape:"url"}" title='{tr}Admin dynamic zones{/tr}'>
        {icon _id='page_gear' alt='{tr}Admin dynamic zones{/tr}'}
      </a> 
      
      <a class="link" href="tiki-admin_html_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].pageName|escape:"url"}" title="{tr}Delete{/tr}">
        {icon _id='cross' alt='{tr}Delete{/tr}'}
      </a>
    </td>
  </tr>
  {sectionelse}
  <tr>
    <td colspan="4" class="odd">{tr}No records found{/tr}</td>
  </tr>
  {/section}
</table>

<div class="mini">
  {if $prev_offset >= 0}
    [<a class="prevnext" href="tiki-admin_html_pages.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
  {/if}
  {tr}Page{/tr}: {$actual_page}/{$cant_pages}
  {if $next_offset >= 0}
    &nbsp;[<a class="prevnext" href="tiki-admin_html_pages.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
  {/if}
  
  {if $prefs.direct_pagination eq 'y'}
    <br />
    {section loop=$cant_pages name=foo}
      {assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
      <a class="prevnext" href="tiki-admin_html_pages.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
      {$smarty.section.foo.index_next}</a>&nbsp;
    {/section}
  {/if}
</div>
</div>

