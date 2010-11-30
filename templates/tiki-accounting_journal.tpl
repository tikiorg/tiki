<a href="tiki-accounting_export.php?bookId={$bookId}&action=print&what=journal{if isset($accountId)}&accountId={$accountId}{/if}" target="new"><img src="images/ico_print.gif" border="0" align="right" alt="{tr}printable version{/tr}"/></a>
<a href="tiki-accounting_export.php?bookId={$bookId}&action=settings&what=journal{if isset($accountId)}&accountId={$accountId}{/if}"><img src="images/ico_table.gif" border="0" align="right" alt="{tr}export table{/tr}"/></a>
<table class="normal">
 <tr>
  <th rowspan="2">{tr}Id{/tr}</th>
  <th rowspan="2">{tr}Date{/tr}</th>
  <th rowspan="2">{tr}Description{/tr}</th>
  <th colspan="3">{tr}Debit{/tr}</th>
  <th colspan="3">{tr}Credit{/tr}</th>
 </tr>
 <tr>
  <th>{tr}Account{/tr}</th>
  <th>{tr}Amount{/tr}</th>
  <th>{tr}Text{/tr}</th>
  <th>{tr}Account{/tr}</th>
  <th>{tr}Amount{/tr}</th>
  <th>{tr}Text{/tr}</th>
 </tr>
{foreach from=$journal" item=j}{cycle values="odd,even" assign="style"}
 <tr class="{$style}">
  <td class="journal{if $j.journalCancelled==1}deleted{/if}"{if $j.maxcount>1} rowspan="{$j.maxcount}"{/if} style="text-align:right">{$j.journalId}</td>
  <td class="journal{if $j.journalCancelled==1}deleted{/if}"{if $j.maxcount>1} rowspan="{$j.maxcount}"{/if} style="text-align:right">{$j.journalDate|date_format:"%Y-%m-%d"}</td>
  <td class="journal{if $j.journalCancelled==1}deleted{/if}"{if $j.maxcount>1} rowspan="{$j.maxcount}"{/if}>{$j.journalDescription|escape}</td>
{section name=posts loop=$j.maxcount}{assign var='i' value=`$smarty.section.posts.iteration-1`}
 {if !$smarty.section.posts.first}<tr class="{$style}">{/if}
  <td class="journal{if $j.journalCancelled==1}deleted{/if}" style="text-align:right">{if $i<$j.debitcount}{$j.debit[$i].itemAccountId}{/if}&nbsp;</td>
  <td class="journal{if $j.journalCancelled==1}deleted{/if}" style="text-align:right">{if $i<$j.debitcount}{if $book.bookCurrencyPos==-1}{$book.bookCurrency} {/if}{$j.debit[$i].itemAmount|currency}{if $book.bookCurrencyPos==1} {$book.bookCurrency}{/if}&nbsp;{/if}</td>
  <td class="journal{if $j.journalCancelled==1}deleted{/if}">{if $i<$j.debitcount}{$j.debit[$i].itemText|escape}{/if}&nbsp;</td>
  <td class="journal{if $j.journalCancelled==1}deleted{/if}" style="text-align:right">{if $i<$j.creditcount}{$j.credit[$i].itemAccountId}{/if}&nbsp;</td>
  <td class="journal{if $j.journalCancelled==1}deleted{/if}" style="text-align:right">{if $i<$j.creditcount}{if $book.bookCurrencyPos==-1}{$book.bookCurrency} {/if}{$j.credit[$i].itemAmount|currency}{if $book.bookCurrencyPos==1} {$book.bookCurrency}{/if}&nbsp;{/if}</td>
  <td class="journal{if $j.journalCancelled==1}deleted{/if}">{if $i<$j.creditcount}{$j.credit[$i].text|escape}{/if}&nbsp;</td>
 </tr>
{/section}
{foreachelse}
<tr><td colspan="9">{tr}No records{/tr}</td></tr>
{/foreach}
{if isset($totals)}
 <tr>
  <td class="journal"><b>{tr}Balance{/tr}</b></td>
  <td class="journal" style="text-align:right"><b>{if $book.bookCurrencyPos==-1}{$book.bookCurrency} {/if}{$totals.total|currency}{if $book.bookCurrencyPos==1} {$book.bookCurrency}{/if}</b></td>
  <td class="journal">&nbsp;</td>
  <td class="journal"><b>{tr}Debit{/tr}</b></td>
  <td class="journal" style="text-align:right"><b>{if $book.bookCurrencyPos==-1}{$book.bookCurrency} {/if}{$totals.debit|currency}{if $book.bookCurrencyPos==1} {$book.bookCurrency}{/if}</b></td>
  <td class="journal">&nbsp;</td>
  <td class="journal"><b>{tr}Credit{/tr}</b></td>
  <td class="journal" style="text-align:right"><b>{if $book.bookCurrencyPos==-1}{$book.bookCurrency} {/if}{$totals.credit|currency}{if $book.bookCurrencyPos==1} {$book.bookCurrency}{/if}</b></td>
  <td class="journal">&nbsp;</td>
 </tr> 
{/if}
</table>
