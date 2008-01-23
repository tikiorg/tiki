{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-listpages_content.tpl,v 1.25.2.6 2008-01-23 18:05:44 nyloth Exp $ *}

{if $cant_pages > 1 or $initial or $find}
  <div align="center">
    {section name=ini loop=$initials}
      {if $initial and $initials[ini] eq $initial}
        <span class="button2">
          <span class="linkbuton">{$initials[ini]|capitalize}</span>
        </span> . 
      {else}
        <a {ajax_href template="tiki-listpages_content.tpl" htmlelement="tiki-listpages-content"}{$smarty.server.PHP_SELF}?initial={$initials[ini]}&amp;maxRecords={$prefs.maxRecords}{if $sort_mode}&amp;sort_mode={$sort_mode}{/if}{if !empty($find)}&amp;find={$find|escape}{/if}{if !empty($find_lang)}&amp;lang={$find_lang}{/if}{if !empty($find_categId)}&amp;categId={$find_categId}{/if}{/ajax_href} class="prevnext">{$initials[ini]}</a> . 
      {/if}
    {/section}
    <a {ajax_href template="tiki-listpages_content.tpl" htmlelement="tiki-listpages-content"}{$smarty.server.PHP_SELF}?initial=&amp;maxRecords={$prefs.maxRecords}{if $sort_mode}&amp;sort_mode={$sort_mode}{/if}{if !empty($find)}&amp;find={$find|escape}{/if}{if !empty($find_lang)}&amp;lang={$find_lang}{/if}{if !empty($find_categId)}&amp;categId={$find_categId}{/if}{/ajax_href} class="prevnext">{tr}All{/tr}</a>
  </div>
{/if}

{if $tiki_p_remove eq 'y' or $prefs.feature_wiki_multiprint eq 'y'}
  {assign var='checkboxes_on' value='y'}
{else}
  {assign var='checkboxes_on' value='n'}
{/if}

{if $checkboxes_on eq 'y'}
  <form name="checkboxes_on" method="post" action="{$smarty.server.PHP_SELF}">
{/if}
  
<table class="normal">
  <tr>
    {if $checkboxes_on eq 'y'}
      <td class="heading">&nbsp;</td>
      {assign var='cntcol' value='1'}
    {/if}
  
    {if $tiki_p_edit eq 'y' or $tiki_p_assign_perm_wiki_page eq 'y'}
      <td class="heading">&nbsp;</td>
    {/if}

    {if $prefs.wiki_list_name eq 'y'}
      {assign var='cntcol' value=$cntcol+1}
        <td class="heading">
          <a class="tableheading" {ajax_href template="tiki-listpages_content.tpl" htmlelement="tiki-listpages-content"}{$smarty.server.PHP_SELF}?offset={$offset}&amp;sort_mode={if $sort_mode eq 'pageName_desc'}pageName_asc{else}pageName_desc{/if}{if $initial}&amp;initial={$initial}{/if}{if !empty($find)}&amp;find={$find|escape}{/if}{if !empty($find_lang)}&amp;lang={$find_lang}{/if}{if !empty($find_categId)}&amp;categId={$find_categId}{/if}&amp;maxRecords={$prefs.maxRecords}{/ajax_href}>{tr}Page{/tr}</a>
        </td>
    {/if}

    {if $prefs.wiki_list_hits eq 'y'}
      {assign var='cntcol' value=$cntcol+1}
        <td style="text-align:right;" class="heading"><a class="tableheading" {ajax_href template="tiki-listpages_content.tpl" htmlelement="tiki-listpages-content"}{$smarty.server.PHP_SELF}?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}{if $initial}&amp;initial={$initial}{/if}{if !empty($find)}&amp;find={$find|escape}{/if}{if !empty($find_lang)}&amp;lang={$find_lang}{/if}{if !empty($find_categId)}&amp;categId={$find_categId}{/if}&amp;maxRecords={$prefs.maxRecords}{/ajax_href}>{tr}Hits{/tr}</a>
        </td>
    {/if}

    {if $prefs.wiki_list_lastmodif eq 'y'}
      {assign var='cntcol' value=$cntcol+1}
        <td class="heading"><a class="tableheading" {ajax_href template="tiki-listpages_content.tpl" htmlelement="tiki-listpages-content"}{$smarty.server.PHP_SELF}?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}{if $initial}&amp;initial={$initial}{/if}{if !empty($find)}&amp;find={$find|escape}{/if}{if !empty($find_lang)}&amp;lang={$find_lang}{/if}{if !empty($find_categId)}&amp;categId={$find_categId}{/if}&amp;maxRecords={$prefs.maxRecords}{/ajax_href}>{tr}Last mod{/tr}</a>
        </td>
    {/if}

    {if $prefs.wiki_list_creator eq 'y'}
    {assign var='cntcol' value=$cntcol+1}
<td class="heading"><a class="tableheading" {ajax_href template="tiki-listpages_content.tpl" htmlelement="tiki-listpages-content"}{$smarty.server.PHP_SELF}?offset={$offset}&amp;sort_mode={if $sort_mode eq 'creator_desc'}creator_asc{else}creator_desc{/if}{if $initial}&amp;initial={$initial}{/if}{if !empty($find)}&amp;find={$find|escape}{/if}{if !empty($find_lang)}&amp;lang={$find_lang}{/if}{if !empty($find_categId)}&amp;categId={$find_categId}{/if}&amp;maxRecords={$prefs.maxRecords}{/ajax_href}>{tr}Creator{/tr}</a></td>
{/if}

    {if $prefs.wiki_list_user eq 'y'}
    {assign var='cntcol' value=$cntcol+1}
      <td class="heading"><a class="tableheading" {ajax_href template="tiki-listpages_content.tpl" htmlelement="tiki-listpages-content"}{$smarty.server.PHP_SELF}?offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}{if $initial}&amp;initial={$initial}{/if}{if !empty($find)}&amp;find={$find|escape}{/if}{if !empty($find_lang)}&amp;lang={$find_lang}{/if}{if !empty($find_categId)}&amp;categId={$find_categId}{/if}&amp;maxRecords={$prefs.maxRecords}{/ajax_href}>{tr}Last author{/tr}</a>
      </td>
    {/if}

    {if $prefs.wiki_list_lastver eq 'y'}
    {assign var='cntcol' value=$cntcol+1}
      <td style="text-align:right;" class="heading"><a class="tableheading" {ajax_href template="tiki-listpages_content.tpl" htmlelement="tiki-listpages-content"}{$smarty.server.PHP_SELF}?offset={$offset}&amp;sort_mode={if $sort_mode eq 'version_desc'}version_asc{else}version_desc{/if}{if $initial}&amp;initial={$initial}{/if}&amp;maxRecords={$prefs.maxRecords}{/ajax_href}>{tr}Last ver{/tr}</a>
      </td>
    {/if}

    {if $prefs.wiki_list_comment eq 'y'}
    {assign var='cntcol' value=$cntcol+1}
      <td class="heading"><a class="tableheading" {ajax_href template="tiki-listpages_content.tpl" htmlelement="tiki-listpages-content"}{$smarty.server.PHP_SELF}?offset={$offset}&amp;sort_mode={if $sort_mode eq 'comment_desc'}comment_asc{else}comment_desc{/if}{if $initial}&amp;initial={$initial}{/if}{if !empty($find_lang)}&amp;lang={$find_lang}{/if}{if !empty($find_categId)}&amp;categId={$find_categId}{/if}&amp;maxRecords={$prefs.maxRecords}{/ajax_href}>{tr}Com{/tr}</a>
      </td>
    {/if}

    {if $prefs.wiki_list_status eq 'y'}
    {assign var='cntcol' value=$cntcol+1}
      <td style="text-align:center;" class="heading"><a class="tableheading" {ajax_href template="tiki-listpages_content.tpl" htmlelement="tiki-listpages-content"}{$smarty.server.PHP_SELF}?offset={$offset}&amp;sort_mode={if $sort_mode eq 'flag_desc'}flag_asc{else}flag_desc{/if}{if $initial}&amp;initial={$initial}{/if}{if !empty($find_lang)}&amp;lang={$find_lang}{/if}{if !empty($find_categId)}&amp;categId={$find_categId}{/if}&amp;maxRecords={$prefs.maxRecords}{/ajax_href}>{tr}Status{/tr}</a>
      </td>
    {/if}

    {if $prefs.wiki_list_versions eq 'y'}
    {assign var='cntcol' value=$cntcol+1}
      <td style="text-align:right;" class="heading"><a class="tableheading" {ajax_href template="tiki-listpages_content.tpl" htmlelement="tiki-listpages-content"}{$smarty.server.PHP_SELF}?offset={$offset}&amp;sort_mode={if $sort_mode eq 'versions_desc'}versions_asc{else}versions_desc{/if}{if $initial}&amp;initial={$initial}{/if}{if !empty($find_lang)}&amp;lang={$find_lang}{/if}{if !empty($find_categId)}&amp;categId={$find_categId}{/if}&amp;maxRecords={$prefs.maxRecords}{/ajax_href}>{tr}Vers{/tr}</a>
      </td>
    {/if}

    {if $prefs.wiki_list_links eq 'y'}
    {assign var='cntcol' value=$cntcol+1}
      <td style="text-align:right;" class="heading"><a class="tableheading" {ajax_href template="tiki-listpages_content.tpl" htmlelement="tiki-listpages-content"}{$smarty.server.PHP_SELF}?offset={$offset}&amp;sort_mode={if $sort_mode eq 'links_desc'}links_asc{else}links_desc{/if}{if $initial}&amp;initial={$initial}{/if}{if !empty($find)}&amp;find={$find|escape}{/if}{if !empty($find_lang)}&amp;lang={$find_lang}{/if}{if !empty($find_categId)}&amp;categId={$find_categId}{/if}&amp;maxRecords={$prefs.maxRecords}{/ajax_href}>{tr}Links{/tr}</a>
      </td>
    {/if}

    {if $prefs.wiki_list_backlinks eq 'y'}
    {assign var='cntcol' value=$cntcol+1}
      <td style="text-align:right;" class="heading"><a class="tableheading" {ajax_href template="tiki-listpages_content.tpl" htmlelement="tiki-listpages-content"}{$smarty.server.PHP_SELF}?offset={$offset}&amp;sort_mode={if $sort_mode eq 'backlinks_desc'}backlinks_asc{else}backlinks_desc{/if}{if $initial}&amp;initial={$initial}{/if}{if !empty($find)}&amp;find={$find|escape}{/if}{if !empty($find_lang)}&amp;lang={$find_lang}{/if}{if !empty($find_categId)}&amp;categId={$find_categId}{/if}&amp;maxRecords={$prefs.maxRecords}{/ajax_href}>{tr}Backlinks{/tr}</a>
      </td>
    {/if}

    {if $prefs.wiki_list_size eq 'y'}
    {assign var='cntcol' value=$cntcol+1}
      <td style="text-align:right;" class="heading"><a class="tableheading" {ajax_href template="tiki-listpages_content.tpl" htmlelement="tiki-listpages-content"}{$smarty.server.PHP_SELF}?offset={$offset}&amp;sort_mode={if $sort_mode eq 'size_desc'}size_asc{else}size_desc{/if}{if $initial}&amp;initial={$initial}{/if}{if !empty($find)}&amp;find={$find|escape}{/if}{if !empty($find_lang)}&amp;lang={$find_lang}{/if}{if !empty($find_categId)}&amp;categId={$find_categId}{/if}&amp;maxRecords={$prefs.maxRecords}{/ajax_href}>{tr}Size{/tr}</a>
      </td>
    {/if}

    {if $prefs.wiki_list_language eq 'y'}
    {assign var='cntcol' value=$cntcol+1}
      <td style="text-align:right;" class="heading"><a class="tableheading" href="tiki-listpages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'language_desc'}lang_asc{else}lang_desc{/if}{if $initial}&amp;initial={$initial}{/if}{if $find}&amp;find={$find}{/if}{if $exact_match eq 'y'}&amp;exact_match=on{/if}{if !empty($lang)}&amp;lang={$lang}{/if}{if !empty($categId)}&amp;categId={$categId}{/if}">{tr}Language{/tr}</a>
      </td>
    {/if}

    {if $prefs.wiki_list_categories eq 'y'}
    {assign var='cntcol' value=$cntcol+1}
      <td class="heading">{tr}Categories{/tr}</td>
    {/if}

    {if $prefs.wiki_list_categories_path eq 'y'}
    {assign var='cntcol' value=$cntcol+1}
      <td class="heading">{tr}Categories{/tr}</td>
    {/if}

  </tr>

  {cycle values="even,odd" print=false}
  {section name=changes loop=$listpages}
  <tr>
    {if $checkboxes_on eq 'y'}
      <td class="{cycle advance=false}">
        <input type="checkbox" name="checked[]" value="{$listpages[changes].pageName|escape}"/>
      </td>
    {/if}

    {if $tiki_p_edit eq 'y' or $tiki_p_assign_perm_wiki_page eq 'y'}
      <td class="{cycle advance=false}">
  
      {if $tiki_p_edit eq 'y'}
        <a class="link" href="tiki-editpage.php?page={$listpages[changes].pageName|escape:"url"}"><img border='0' title='{tr}Edit{/tr}' alt='{tr}Edit{/tr}' src='pics/icons/page_edit.png' height='16' width='16' /></a>
      {/if}
  
      {if $tiki_p_assign_perm_wiki_page eq 'y'}
        <a class="link" href="tiki-objectpermissions.php?objectName={$listpages[changes].pageName|escape:"url"}&amp;objectType=wiki+page&amp;permType=wiki&amp;objectId={$listpages[changes].pageName|escape:"url"}"><img src='pics/icons/key.png' border='0' width='16' height='16' alt='{tr}Perms{/tr}' title='{tr}Perms{/tr}' /></a>
      {/if}
      </td>
    {/if}

    {if $prefs.wiki_list_name eq 'y'}
      <td class="{cycle advance=false}">
        <a href="tiki-index.php?page={$listpages[changes].pageName|escape:"url"}" class="link" title="{$listpages[changes].pageName}">{$listpages[changes].pageName|truncate:$prefs.wiki_list_name_len:"...":true}</a>
      </td>
    {/if}

    {if $prefs.wiki_list_hits eq 'y'}	
      <td style="text-align:right;" class="{cycle advance=false}">{$listpages[changes].hits}</td>
    {/if}

    {if $prefs.wiki_list_lastmodif eq 'y'}
      <td class="{cycle advance=false}">{$listpages[changes].lastModif|tiki_short_datetime}</td>
    {/if}

    {if $prefs.wiki_list_creator eq 'y'}
      <td class="{cycle advance=false}">{$listpages[changes].creator|userlink}</td>
    {/if}

    {if $prefs.wiki_list_user eq 'y'}
      <td class="{cycle advance=false}">{$listpages[changes].user|userlink}</td>
    {/if}

    {if $prefs.wiki_list_lastver eq 'y'}
      <td style="text-align:right;" class="{cycle advance=false}">{$listpages[changes].version}</td>
    {/if}

    {if $prefs.wiki_list_comment eq 'y'}
      <td class="{cycle advance=false}">{if $listpages[changes].comment eq ""}&nbsp;{else}{$listpages[changes].comment}{/if}</td>
    {/if}

    {if $prefs.wiki_list_status eq 'y'}
      <td style="text-align:center;" class="{cycle advance=false}">
        {if $listpages[changes].flag eq 'locked'}
	  <img src='pics/icons/lock.png' alt='{tr}Locked{/tr}' border='0' height='16' width='16' />
	{else}
	  <img src='pics/icons/lock_break.png' alt='{tr}unlocked{/tr}' border='0' height='16' width='16' />
	{/if}
      </td>
    {/if}

    {if $prefs.wiki_list_versions eq 'y'}
      {if $prefs.feature_history eq 'y' and $tiki_p_wiki_view_history eq 'y'}
        <td style="text-align:right;" class="{cycle advance=false}">
          <a class="link" href="tiki-pagehistory.php?page={$listpages[changes].pageName|escape:"url"}">{$listpages[changes].versions}</a>
        </td>
      {else}
        <td style="text-align:right;" class="{cycle advance=false}">{$listpages[changes].versions}</td>
      {/if}
    {/if}

    {if $prefs.wiki_list_links eq 'y'}
      <td style="text-align:right;" class="{cycle advance=false}">{$listpages[changes].links}</td>
    {/if}

    {if $prefs.wiki_list_backlinks eq 'y'}
      {if $prefs.feature_backlinks eq 'y'}
        <td style="text-align:right;" class="{cycle advance=false}">
          <a class="link" href="tiki-backlinks.php?page={$listpages[changes].pageName|escape:"url"}">{$listpages[changes].backlinks}</a>
        </td>
      {else}
        <td style="text-align:right;" class="{cycle advance=false}">{$listpages[changes].backlinks}</td>
      {/if}
    {/if}

    {if $prefs.wiki_list_size eq 'y'}
      <td style="text-align:right;" class="{cycle advance=false}">{$listpages[changes].len|kbsize}</td>
    {/if}

    {if $prefs.wiki_list_language eq 'y'}
      <td class="{cycle advance=false}">{$listpages[changes].lang}</td>
    {/if}

    {if $prefs.wiki_list_categories eq 'y'}
      <td class="{cycle advance=false}">
        {foreach item=categ from=$listpages[changes].categname}
	  {if !$smarty.foreach.categ.first}<br />{/if}
	    {$categ}
	{/foreach}
      </td>
    {/if}

    {if $prefs.wiki_list_categories_path eq 'y'}
      <td class="{cycle advance=false}">
	{foreach item=categpath from=$listpages[changes].categpath}
          {if !$smarty.foreach.categpath.first}<br />{/if}
	    {$categpath}
	{/foreach}
      </td>
    {/if}
    
    {cycle print=false}
  </tr>
  {sectionelse}

  <tr>
    <td colspan="{$cntcol}">
      <b>{tr}No records found{/tr}</b>
    </td>
  </tr>
  {/section}

  {if $checkboxes_on eq 'y' && count($listpages) > 0}
    <script type='text/javascript'>
      <!--
        // check / uncheck all.
        // in the future, we could extend this to happen serverside as well for the convenience of people w/o javascript.
        // for now those people just have to check every single box
        document.write("<tr><td><input name=\"switcher\" id=\"clickall\" type=\"checkbox\" onclick=\"switchCheckboxes(this.form,'checked[]',this.checked)\"/></td>");
        document.write("<td colspan=\"{$cntcols}\"><label for=\"clickall\">{tr}All{/tr}</label></td></tr>");
        //-->                     
    </script>
  {/if}
</table>

  {if $checkboxes_on eq 'y' && count($listpages) > 0} {* what happens to the checked items? *}
    <p align="left"> {*on the left to have it close to the checkboxes*}
      <select name="submit_mult" onchange="this.form.submit();">
        <option value="" selected="selected">{tr}with checked{/tr}:</option>
        {if $tiki_p_remove eq 'y'} 
          <option value="remove_pages" >{tr}Remove{/tr}</option>
        {/if}
    
        {if $prefs.feature_wiki_multiprint eq 'y'}
          <option value="print_pages" >{tr}Print{/tr}</option>
        {/if}

		{if $prefs.feature_wiki_usrlock eq 'y' and ($tiki_p_lock eq 'y' or $tiki_p_admin_wiki eq 'y')}
			<option value="lock_pages" >{tr}Lock{/tr}</option>
			<option value="unlock_pages" >{tr}Unlock{/tr}</option>
		{/if}
      
        {* add here e.g. <option value="categorize" >{tr}categorize{/tr}</option> *}
      </select>                
    
      <script type='text/javascript'>
        <!--
        // Fake js to allow the use of the <noscript> tag (so non-js-users can still submit)
        //-->
      </script>
    
      <noscript>
        <input type="submit" value="{tr}OK{/tr}" />
      </noscript>
    </p>
  {/if}
  {if $find && $tiki_p_edit eq 'y'}{tr}Create Page{/tr}: <a href="tiki-editpage.php?page={$find}" title="{tr}Create{/tr}"> {$find}</a>{/if}

</form>

<br />
{pagination_links cant=$cant template='tiki-listpages_content.tpl' htmlelement='tiki-listpages-content' step=$maxRecords offset=$offset}{/pagination_links}
