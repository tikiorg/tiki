{* $Id$ *}
<h1 class="pagetitle"><strong><a href="tiki-index.php?page={$page}">{tr}Contributions to{/tr} {$page}
({if $lastversion==0}{tr}current version{/tr}{else}{tr}version{/tr} {$lastversion}{/if})</a></strong></h1>
{include file='tiki-page_contribution_form.tpl'}
{if $showstatistics==1}
<h2>{tr}Statistics{/tr}</h2>
<table class="normal">
 <tr>
  <th rowspan="2">{tr}Author{/tr}</th>
  <th colspan="4">{tr}Words{/tr}</th>
  <th colspan="4">{tr}Whitespaces{/tr}</th>
  <th colspan="4">{tr}Characters{/tr}</th>
  <th colspan="4">{tr}Printable characters{/tr}</th>
 </tr>
 <tr>
  <th colspan="2">{tr}Used{/tr}</th>
  <th colspan="2">{tr}Deleted{/tr}</th>
  <th colspan="2">{tr}Used{/tr}</th>
  <th colspan="2">{tr}Deleted{/tr}</th>
  <th colspan="2">{tr}Used{/tr}</th>
  <th colspan="2">{tr}Deleted{/tr}</th>
  <th colspan="2">{tr}Used{/tr}</th>
  <th colspan="2">{tr}Deleted{/tr}</th>
 </tr>
 {foreach from=$authors item="stats" key="author" name="authors"}
 <tr class="{cycle values="odd,even"}">
  <td class="text"><span class="{$stats.style}">{$author}</span></td>
  <td class="text">{$stats.words}</td>
  <td class="text">({$stats.words_percent|percent}%)</td>
  <td class="text">{$stats.deleted_words}</td>
  <td class="text">({$stats.deleted_words_percent|percent}%)</td>
  <td class="text">{$stats.whitespaces}</td>
  <td class="text">({$stats.whitespaces_percent|percent}%)</td>
  <td class="text">{$stats.deleted_whitespaces}</td>
  <td class="text">({$stats.deleted_whitespaces_percent|percent}%)</td>
  <td class="text">{$stats.characters}</td>
  <td class="text">({$stats.characters_percent|percent}%)</td>
  <td class="text">{$stats.deleted_characters}</td>
  <td class="text">({$stats.deleted_characters_percent|percent}%)</td>
  <td class="text">{$stats.printables}</td>
  <td class="text">({$stats.printables_percent|percent}%)</td>
  <td class="text">{$stats.deleted_printables}</td>
  <td class="text">({$stats.deleted_printables_percent|percent}%)</td>
 </tr>{/foreach}
  <tr class="{cycle}">
  <td class="text"><strong>{tr}Total{/tr}</strong></td>
  <td class="text"><strong>{$total.words}</strong></td>
  <td class="text"><strong>(100.0%)</strong></td>
  <td class="text"><strong>{$total.deleted_words}</strong></td>
  <td class="text"><strong>(100.0%)</strong></td>
  <td class="text"><strong>{$total.whitespaces}</strong></td>
  <td class="text"><strong>(100.0%)</strong></td>
  <td class="text"><strong>{$total.deleted_whitespaces}</strong></td>
  <td class="text"><strong>(100.0%)</strong></td>
  <td class="text"><strong>{$total.characters}</strong></td>
  <td class="text"><strong>(100.0%)</strong></td>
  <td class="text"><strong>{$total.deleted_characters}</strong></td>
  <td class="text"><strong>(100.0%)</strong></td>
  <td class="text"><strong>{$total.printables}</strong></td>
  <td class="text"><strong>(100.0%)</strong></td>
  <td class="text"><strong>{$total.deleted_printables}</strong></td>
  <td class="text"><strong>(100 %)</strong></td>
 </tr>
</table>
{/if}
{if $showpage==1}
<h2>{tr}Page changes{/tr}</h2>
<div id="top" class="content wikitext clearfix">
{$parsed}
</div>
{/if}
