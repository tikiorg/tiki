{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-listpages_content.tpl,v 1.25.2.13 2008-03-20 13:19:19 pkdille Exp $ *}

{if $cant_pages > 1 or $initial or $find}{initials_filter_links}{/if}

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

    {if $prefs.wiki_list_id eq 'y'}
      {assign var='cntcol' value=$cntcol+1}
      <td class="heading">{self_link _class='tableheading' _sort_arg='sort_mode' _sort_field='page_id'}{tr}Id{/tr}{/self_link}</td>
    {/if}

    {if $prefs.wiki_list_name eq 'y'}
      {assign var='cntcol' value=$cntcol+1}
      <td class="heading">{self_link _class='tableheading' _sort_arg='sort_mode' _sort_field='pageName'}{tr}Page{/tr}{/self_link}</td>
    {/if}

    {if $prefs.wiki_list_hits eq 'y'}
      {assign var='cntcol' value=$cntcol+1}
      <td class="heading" style="text-align:right;">{self_link _class='tableheading' _sort_arg='sort_mode' _sort_field='hits'}{tr}Hits{/tr}{/self_link}</td>
    {/if}

    {if $prefs.wiki_list_lastmodif eq 'y'}
      {assign var='cntcol' value=$cntcol+1}
      <td class="heading">{self_link _class='tableheading' _sort_arg='sort_mode' _sort_field='lastModif'}{tr}Last mod{/tr}{/self_link}</td>
    {/if}

    {if $prefs.wiki_list_creator eq 'y'}
      {assign var='cntcol' value=$cntcol+1}
      <td class="heading">{self_link _class='tableheading' _sort_arg='sort_mode' _sort_field='creator'}{tr}Creator{/tr}{/self_link}</td>
    {/if}

    {if $prefs.wiki_list_user eq 'y'}
      {assign var='cntcol' value=$cntcol+1}
      <td class="heading">{self_link _class='tableheading' _sort_arg='sort_mode' _sort_field='user'}{tr}Last author{/tr}{/self_link}</td>
    {/if}

    {if $prefs.wiki_list_lastver eq 'y'}
      {assign var='cntcol' value=$cntcol+1}
      <td class="heading">{self_link _class='tableheading' _sort_arg='sort_mode' _sort_field='version'}{tr}Last ver{/tr}{/self_link}</td>
    {/if}

    {if $prefs.wiki_list_comment eq 'y'}
      {assign var='cntcol' value=$cntcol+1}
      <td class="heading">{self_link _class='tableheading' _sort_arg='sort_mode' _sort_field='comment'}{tr}Com{/tr}{/self_link}</td>
    {/if}

    {if $prefs.wiki_list_status eq 'y'}
      {assign var='cntcol' value=$cntcol+1}
      <td class="heading">{self_link _class='tableheading' _sort_arg='sort_mode' _sort_field='flag'}{tr}Status{/tr}{/self_link}</td>
    {/if}

    {if $prefs.wiki_list_versions eq 'y'}
      {assign var='cntcol' value=$cntcol+1}
      <td class="heading">{self_link _class='tableheading' _sort_arg='sort_mode' _sort_field='versions'}{tr}Vers{/tr}{/self_link}</td>
    {/if}

    {if $prefs.wiki_list_links eq 'y'}
      {assign var='cntcol' value=$cntcol+1}
      <td class="heading">{self_link _class='tableheading' _sort_arg='sort_mode' _sort_field='links'}{tr}Links{/tr}{/self_link}</td>
    {/if}

    {if $prefs.wiki_list_backlinks eq 'y'}
      {assign var='cntcol' value=$cntcol+1}
      <td class="heading">{self_link _class='tableheading' _sort_arg='sort_mode' _sort_field='backlinks'}{tr}Backlinks{/tr}{/self_link}</td>
    {/if}

    {if $prefs.wiki_list_size eq 'y'}
      {assign var='cntcol' value=$cntcol+1}
      <td class="heading">{self_link _class='tableheading' _sort_arg='sort_mode' _sort_field='size'}{tr}Size{/tr}{/self_link}</td>
    {/if}

    {if $prefs.wiki_list_language eq 'y'}
      {assign var='cntcol' value=$cntcol+1}
      <td class="heading">{self_link _class='tableheading' _sort_arg='sort_mode' _sort_field='lang'}{tr}Language{/tr}{/self_link}</td>
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
        <a class="link" href="tiki-editpage.php?page={$listpages[changes].pageName|escape:"url"}">{icon _id='page_edit'}</a>
      {/if}
  
      {if $tiki_p_assign_perm_wiki_page eq 'y'}
        <a class="link" href="tiki-objectpermissions.php?objectName={$listpages[changes].pageName|escape:"url"}&amp;objectType=wiki+page&amp;permType=wiki&amp;objectId={$listpages[changes].pageName|escape:"url"}">{icon _id='key' alt='{tr}Perms{/tr}'}</a>
      {/if}
      </td>
    {/if}

    {if $prefs.wiki_list_id eq 'y'}
      <td class="{cycle advance=false}">
        <a href="{$listpages[changes].pageName|sefurl}" class="link" title="{tr}View page{/tr}&nbsp;{$listpages[changes].pageName}">{$listpages[changes].page_id}</a>
      </td>
    {/if}

    {if $prefs.wiki_list_name eq 'y'}
      <td class="{cycle advance=false}">
        <a href="{$listpages[changes].pageName|sefurl}" class="link" title="{tr}View page{/tr}&nbsp;{$listpages[changes].pageName}">{$listpages[changes].pageName|truncate:$prefs.wiki_list_name_len:"...":true}</a>
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
	  {icon _id='lock' alt='{tr}Locked{/tr}'}
	{else}
	  {icon _id='lock_break' alt='{tr}unlocked{/tr}'}
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
        {foreach item=categ from=$listpages[changes].categname name=categ}
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
{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
