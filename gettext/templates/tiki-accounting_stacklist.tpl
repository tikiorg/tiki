<a class="icon" href="tiki-accounting_export.php?action=print&what=stack&bookId={$bookId}" target="new">{icon _id="printer" alt="{tr}printable version{/tr}"}</a>
<a class="icon" href="tiki-accounting_export.php?action=settings&what=stack&bookId={$bookId}">{icon _id="table" alt="{tr}export table{/tr}"}</a>
<table class="normal">
 <tr>
  <th rowspan="2">{tr}Id{/tr}</th>
  <th rowspan="2">{tr}Date{/tr}</th>
  <th rowspan="2">{tr}Description{/tr}</th>
  <th colspan="3">{tr}Debit{/tr}</th>
  <th colspan="3">{tr}Credit{/tr}</th>
  <th rowspan="2">&nbsp;</th>
 </tr>
 <tr>
  <th>{tr}Account{/tr}</th>
  <th>{tr}Amount{/tr}</th>
  <th>{tr}Text{/tr}</th>
  <th>{tr}Account{/tr}</th>
  <th>{tr}Amount{/tr}</th>
  <th>{tr}Text{/tr}</th>
 </tr>
{foreach from=$stack item=s}{cycle values="odd,even" assign="style"}
 <tr class="{$style}">
  <td class="journal"{if $s.maxcount>1} rowspan="{$s.maxcount}"{/if} style="text-align:right">
   <a href="tiki-accounting_stack.php?bookId={$bookId}&stackId={$s.stackId}">{$s.stackId}</a>
  </td>
  <td class="journal"{if $s.maxcount>1} rowspan="{$s.maxcount}"{/if} style="text-align:right">{$s.stackDate|date_format:"%Y-%m-%d"}</td>
  <td class="journal"{if $s.maxcount>1} rowspan="{$s.maxcount}"{/if}>{$s.stackDescription|escape}</td>
{section name=posts loop=$s.maxcount}{assign var='i' value=$smarty.section.posts.iteration-1}
 {if !$smarty.section.posts.first}<tr class="{$style}">{/if}
  <td class="journal" style="text-align:right">{if $i<$s.debitcount}{$j.debit[$i].stackItemAccountId}{/if}&nbsp;</td>
  <td class="journal" style="text-align:right">{if $i<$s.debitcount}{if $book.bookCurrencyPos==-1}{$book.bookCurrency} {/if}{$s.debit[$i].stackItemAmount|currency}{if $book.bookCurrencyPos==1} {$book.bookCurrency}{/if}&nbsp;{/if}</td>
  <td class="journal">{if $i<$j.debitcount}{$j.debit[$i].stackItemText|escape}{/if}&nbsp;</td>
  <td class="journal" style="text-align:right">{if $i<$s.creditcount}{$j.credit[$i].stackItemAccountId}{/if}&nbsp;</td>
  <td class="journal" style="text-align:right">{if $i<$s.creditcount}{if $book.bookCurrencyPos==-1}{$book.bookCurrency} {/if}{$s.credit[$i].stackItemAmount|currency}{if $book.bookCurrencyPos==1} {$book.bookCurrency}{/if}&nbsp;{/if}</td>
  <td class="journal">{if $i<$j.creditcount}{$j.credit[$i].stackItemText|escape}{/if}&nbsp;</td>
  {if $smarty.section.posts.first}<td rowspan="{$s.maxcount}">
   <a class="icon" href="tiki-accounting_stack.php?action=delete&bookId={$bookId}&stackId={$s.stackId}">{icon _id="delete" alt="{tr}delete this transaction from the stack{/tr}" _confirm="{tr}Are you sure you want to delete this transaction from stack?{/tr}"}</a><br />
   {if $canBook}<a class="icon" href="tiki-accounting_stack.php?action=confirm&bookId={$bookId}&stackId={$s.stackId}">{icon _id="arrow_right" alt="{tr}confirm this transaction{/tr}" _confirm="{tr}Are you sure you want to confirm this transaction?{/tr}"}</a><br />{/if}
  </td>{/if}
 </tr>
{/section}
{foreachelse}
	{norecords _colspan=9}
{/foreach}
</table>
