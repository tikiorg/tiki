{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="directory_last_sites" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
  {section name=ix loop=$modLastdirSites}
     <li>
          <a class="linkmodule" href="{if $absurl == 'y'}{$base_url}{/if}tiki-directory_redirect.php?siteId={$modLastdirSites[ix].siteId}" {if $prefs.directory_open_links eq 'n'}target="_new"{/if}>
          {$modLastdirSites[ix].name|escape}
          </a>
      </li>
  {/section}
{if $nonums != 'y'}</ol>{else}</ul>{/if}
{if $module_params.more eq 'y'}
	<div class="more">
		{if isset($module_params.categoryId)}
			{assign var='thisparent' value='?parent='|cat:$module_params.categoryId}
		{else}
			{assign var='thisparent' value=''}
		{/if}
		{button href="tiki-directory_browse.php$thisparent" _text="{tr}More...{/tr}"}
	</div>
{/if}
{/tikimodule}