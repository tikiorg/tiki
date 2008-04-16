{* $Id$ *}

{if $prefs.feature_directory eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` Sites{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last Sites{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule title=$tpl_module_title name="directory_last_sites" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
  <table  border="0" cellpadding="1" cellspacing="0" width="100%">
  {section name=ix loop=$modLastdirSites}
    <tr>
      {if $nonums != 'y'}<td valign="top" class="module">{$smarty.section.ix.index_next})</td>{/if}
      <td class="module">
	  	{if $absurl == 'y'}
          <a class="linkmodule" href="{$base_url}tiki-directory_redirect.php?siteId={$modLastdirSites[ix].siteId}" {if $prefs.directory_open_links eq 'n'}target="_new"{/if}>
          {$modLastdirSites[ix].name}
          </a>
		  {else}
        <a class="linkmodule" href="tiki-directory_redirect.php?siteId={$modLastdirSites[ix].siteId}" {if $prefs.directory_open_links eq 'n'}target="_new"{/if}>
          {$modLastdirSites[ix].name}
        </a>
		{/if}
      </td>
    </tr>
  {/section}
  </table>
{if $module_params.more eq 'y'}
	<div class="more">
		 <a class="linkbut" href="tiki-directory_browse.php{if $module_params.categoryId}?parent={$module_params.categoryId}{/if}">{tr}More...{/tr}</a>
	</div>
{/if}
{/tikimodule}
{/if}
