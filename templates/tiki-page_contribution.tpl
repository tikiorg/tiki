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
  <td style="text-align: right;"><span class="{$stats.style}">{$author}</span></td>
  <td style="text-align: right;">{$stats.words}</td>
  <td style="text-align: right;">({$stats.words_percent|percent}%)</td>
  <td style="text-align: right;">{$stats.deleted_words}</td>
  <td style="text-align: right;">({$stats.deleted_words_percent|percent}%)</td>
  <td style="text-align: right;">{$stats.whitespaces}</td>
  <td style="text-align: right;">({$stats.whitespaces_percent|percent}%)</td>
  <td style="text-align: right;">{$stats.deleted_whitespaces}</td>
  <td style="text-align: right;">({$stats.deleted_whitespaces_percent|percent}%)</td>
  <td style="text-align: right;">{$stats.characters}</td>
  <td style="text-align: right;">({$stats.characters_percent|percent}%)</td>
  <td style="text-align: right;">{$stats.deleted_characters}</td>
  <td style="text-align: right;">({$stats.deleted_characters_percent|percent}%)</td>
  <td style="text-align: right;">{$stats.printables}</td>
  <td style="text-align: right;">({$stats.printables_percent|percent}%)</td>
  <td style="text-align: right;">{$stats.deleted_printables}</td>
  <td style="text-align: right;">({$stats.deleted_printables_percent|percent}%)</td>
 </tr>{/foreach}
  <tr class="{cycle}">
  <td style="text-align: right;"><strong>{tr}Total{/tr}</strong></td>
  <td style="text-align: right;"><strong>{$total.words}</strong></td>
  <td style="text-align: right;"><strong>(100.0%)</strong></td>
  <td style="text-align: right;"><strong>{$total.deleted_words}</strong></td>
  <td style="text-align: right;"><strong>(100.0%)</strong></td>
  <td style="text-align: right;"><strong>{$total.whitespaces}</strong></td>
  <td style="text-align: right;"><strong>(100.0%)</strong></td>
  <td style="text-align: right;"><strong>{$total.deleted_whitespaces}</strong></td>
  <td style="text-align: right;"><strong>(100.0%)</strong></td>
  <td style="text-align: right;"><strong>{$total.characters}</strong></td>
  <td style="text-align: right;"><strong>(100.0%)</strong></td>
  <td style="text-align: right;"><strong>{$total.deleted_characters}</strong></td>
  <td style="text-align: right;"><strong>(100.0%)</strong></td>
  <td style="text-align: right;"><strong>{$total.printables}</strong></td>
  <td style="text-align: right;"><strong>(100.0%)</strong></td>
  <td style="text-align: right;"><strong>{$total.deleted_printables}</strong></td>
  <td style="text-align: right;"><strong>(100 %)</strong></td>
 </tr>
</table>
{/if}
{if $showpage==1}
<h2>{tr}Page changes{/tr}</h2>
<div id="top" class="content wikitext clearfix">
{$parsed}
</div>
{/if}
