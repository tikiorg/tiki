{* based on /cvsroot/tikiwiki/tiki/templates/modules/mod-last_modif_pages.tpl,v 1.29 2007/10/14 17:51:01 mose *}

{if $prefs.feature_wiki eq 'y'}
{if !isset($tpl_module_title)}
	{if $nonums eq 'y'}
		{eval var="{*<a href=\"tiki-lastchanges.php\">*}{tr}Last `$module_rows` changes{/tr}{*</a>*}" assign="tpl_module_title"}
{else}
{eval var="{*<a href=\"tiki-lastchanges.php\">*}{tr}Last changes{/tr}{*</a>*}" assign="tpl_module_title"}
{/if}
{/if}
	{tikimodule title=$tpl_module_title name="last_modif_pages" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
	{if $nonums != 'y'}<ol>{else}<ul>{/if}
    {section name=ix loop=$modLastModif}
	<li>
      {if $absurl == 'y'}
          <a class="linkmodule" href="{$feature_server_name}tiki-index.php?page={$modLastModif[ix].pageName|escape:"url"}" title="{$modLastModif[ix].lastModif|tiki_short_datetime}, {tr}by{/tr} {if $modLastModif[ix].user ne ''}{$modLastModif[ix].user}{else}{tr}Anonymous{/tr}{/if}{if (strlen($modLastModif[ix].pageName) > $maxlen) && ($maxlen > 0)}, {$modLastModif[ix].pageName}{/if}">
        {if $maxlen > 0}{* 0 is default value for maxlen eq to 'no truncate' *}
         {$modLastModif[ix].pageName|escape|truncate:$maxlen:"...":true}
        {else}
         {$modLastModif[ix].pageName|escape}
        {/if}
          </a>
		  {else}
       <a class="linkmodule" href="tiki-index.php?page={$modLastModif[ix].pageName|escape:"url"}" title="{$modLastModif[ix].lastModif|tiki_short_datetime}, {tr}by{/tr} {if $modLastModif[ix].user ne ''}{$modLastModif[ix].user}{else}{tr}Anonymous{/tr}{/if}{if (strlen($modLastModif[ix].pageName) > $maxlen) && ($maxlen > 0)}, {$modLastModif[ix].pageName}{/if}">
        {if $maxlen > 0}{* 0 is default value for maxlen eq to 'no truncate' *}
         {$modLastModif[ix].pageName|escape|truncate:$maxlen:"...":true}
        {else}
         {$modLastModif[ix].pageName|escape}
        {/if}
       </a>
	   {/if}
     </li>
    {/section}
	{if $nonums != 'y'}</ol>{else}</ul>{/if}
	<a style="margin-left: 20px" href="tiki-lastchanges.php">...{tr}more{/tr}</a>
	{/tikimodule}
{/if}
