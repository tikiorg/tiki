{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-featured_links.tpl,v 1.7 2003-08-07 20:56:53 zaufi Exp $ *}

{if $feature_featuredLinks eq 'y'}
 <div class="box">
  <div class="box-title">
    {include file="modules/module-title.tpl" module_title="{tr}Featured links{/tr}" module_name="featured_links"}
  </div>
  <div class="box-data">
   <table width="100%" border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$featuredLinks}
     {if $featuredLinks[ix].type eq 'f'}
      <tr>
       <td class="module">
        <a class="linkmodule" href="tiki-featured_link.php?type={$featuredLinks[ix].type}&amp;url={$featuredLinks[ix].url|escape:"url"}">
         {$featuredLinks[ix].title}
        </a>
       </td>
      </tr>
     {else}
      <tr>
       <td class="module">
        <a class="linkmodule" {if $featuredLinks[ix].type eq 'n'}target='_blank'{/if} href="{$featuredLinks[ix].url}">
         {$featuredLinks[ix].title}
        </a>
       </td>
      </tr>
     {/if}
    {/section}
   </table>
  </div>
 </div>
{/if}
