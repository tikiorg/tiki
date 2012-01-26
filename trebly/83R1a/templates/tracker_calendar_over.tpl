{* $Id: tracker_calendar_over.tpl 33949 2011-04-14 05:13:23Z chealer $ *}
{strip}
<div class="opaque calBox">
{if !empty($overs)}
<ul>
{foreach from=$overs item=over}
<li>{$over}</li>
{/foreach}
</ul>
</div>
{/if}
{/strip}
