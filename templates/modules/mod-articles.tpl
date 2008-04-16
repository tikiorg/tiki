{* $Id$ *}

{if $prefs.feature_articles eq 'y'}
{if !isset($tpl_module_title)}{eval assign=tpl_module_title var="{tr}$module_title{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="articles" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
  <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modArticles}
    <tr>
      {if $module_params.nonums != 'y'}
        <td class="module" valign="top">{$smarty.section.ix.index_next})</td>
      {/if}
      <td class="module">&nbsp;
        <a class="linkmodule" href="tiki-read_article.php?articleId={$modArticles[ix].articleId}">
          {$modArticles[ix].title}
        </a>
        </td>
    </tr>
    {/section}
  </table>
{if $module_params.more eq 'y'}
	<div class="more">
		 {assign var=sep value='?'}
		 <a class="linkbut" href="tiki-view_articles.php{if $module_params.topicId}{$sep}topic={$module_params.topicId}{assign var=sep value='&amp;'}{/if}{if $module_params.topic}{$sep}topicName={$module_params.topic|escape:url}{assign var=sep value='&amp;'}{/if}{if $module_params.categId}{$sep}categId={$module_params.categId}{assign var=sep value='&amp;'}{/if}{if $module_params.type}{$sep}type={$module_params.type|escape:url}{assign var=sep value='&amp;'}{/if}{if $module_params.lang}{$sep}lang={$module_params.lang|escape:url}{assign var=sep value='&amp;'}{/if}">{tr}More...{/tr}</a>
	</div>
{/if}
{/tikimodule}
{/if}
