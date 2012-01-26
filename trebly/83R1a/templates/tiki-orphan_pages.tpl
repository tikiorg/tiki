{* $Id: tiki-orphan_pages.tpl 33949 2011-04-14 05:13:23Z chealer $ *}

{title admpage="wiki" help="Using+Wiki+Pages#Backlinks_amp_Orphan_Pages"}{tr}Orphan Pages{/tr}{/title}

{if $listpages or ($find ne '')}
  {include file='find.tpl' find_show_languages='y' find_show_categories='y' find_show_num_rows='y'}
{/if}

<div id="tiki-listpages-content">
  {include file='tiki-listpages_content.tpl'}
</div>

