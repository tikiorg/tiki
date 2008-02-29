{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_modif_pages.tpl,v 1.29.2.2 2008-02-29 15:36:35 sylvieg Exp $ *}

{if $prefs.feature_wiki eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="<a href=\"`$url`\">{tr}Last `$module_rows` Page Changes{/tr}</a>" assign="tpl_module_title"}
{else}
{eval var="<a href=\"`$url`\">{tr}Last Page Changes{/tr}</a>" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule title=$tpl_module_title name="last_modif_pages" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
   <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modLastModif}
     <tr>
      {if $nonums != 'y'}
        <td class="module" valign="top">{$smarty.section.ix.index_next})</td>
      {/if}
      <td class="module">
		{if $absurl == 'y'}
          <a class="linkmodule" href="{$base_url}tiki-index.php?page={$modLastModif[ix].pageName|escape:"url"}" title="{$modLastModif[ix].lastModif|tiki_short_datetime}, {tr}by{/tr} {if $modLastModif[ix].user ne ''}{$modLastModif[ix].user|username}{else}{tr}Anonymous{/tr}{/if}{if (strlen($modLastModif[ix].pageName) > $maxlen) && ($maxlen > 0)}, {$modLastModif[ix].pageName}{/if}">
        {if $maxlen > 0}{* 0 is default value for maxlen eq to 'no truncate' *}
         {$modLastModif[ix].pageName|escape|truncate:$maxlen:"...":true}
        {else}
         {$modLastModif[ix].pageName|escape}
        {/if}
          </a>
		  {else}
       <a class="linkmodule" href="{$modLastModif[ix].pageName|sefurl}" title="{$modLastModif[ix].lastModif|tiki_short_datetime}, {tr}by{/tr} {if $modLastModif[ix].user ne ''}{$modLastModif[ix].user|username}{else}{tr}Anonymous{/tr}{/if}{if (strlen($modLastModif[ix].pageName) > $maxlen) && ($maxlen > 0)}, {$modLastModif[ix].pageName}{/if}">
        {if $maxlen > 0}{* 0 is default value for maxlen eq to 'no truncate' *}
         {$modLastModif[ix].pageName|escape|truncate:$maxlen:"...":true}
        {else}
         {$modLastModif[ix].pageName|escape}
        {/if}
       </a>
	   {/if}
      </td>
     </tr>
    {/section}
   </table>
{/tikimodule}
{/if}
