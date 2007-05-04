{* $Header: /cvsroot/tikiwiki/tiki/templates/tracker_item_field_value.tpl,v 1.1 2007-05-04 14:07:50 sylvieg Exp $ *}
{strip}
{* param: list_mode, tiki_p_perm for this tracker, $item(type,value,displayedvalue,links,categs,options_array) *}

{if $item.type ne 'x' and $item.type ne 'G'}
{* ******************** link to the item ******************** *}
{if $item.isMain eq 'y'
 and ($tiki_p_view_trackers eq 'y' or $tiki_p_modify_tracker_items eq 'y' or $tiki_p_comment_tracker_items eq 'y'
 or ($tracker_info.writerCanModify eq 'y' and $user and $my eq $user) or ($tracker_info.writerCanModify eq 'y' and $group and $ours eq $group))}
<a class="tablename" href="tiki-view_tracker_item.php?itemId={$item.itemId}&amp;show=view&amp;offset={$offset}&amp;reloff={$itemoff}{foreach key=urlkey item=urlval from=$urlquery}{if $urlval}&amp;{$urlkey}={$urlval|escape:"url"}{/if}{/foreach}">
{/if}

{* ******************** field with preprend ******************** *}
{if  ($item.type eq 't' or $item.type eq 'n' or $item.type eq 'c') 
 and !empty($item.options_array[2])}
	<span class="formunit">&nbsp;{$item.options_array[2]}</span>
{/if}

{* ******************** field handling emptiness in a specific way  ******************** *}
{* -------------------- category -------------------- *}
{if $item.type eq 'e'}
	{foreach from=$item.categs item=categ name=fcategs}
		{$categ.name}
		{if !$smarty.foreach.fcategs.last}<br />{/if}
	{/foreach}

{* -------------------- empty field -------------------- *}
{elseif empty($item.value)}
	&nbsp;

{* -------------------- test field, numeric, grop down, radio,user/group/IP selector, autopincrement *}
{elseif $item.type eq  't' or $item.type eq 'n' or $item.type eq 'd' or $item.type eq 'D' or $item.type eq 'R' or $item.type eq 'u' or $item.type eq 'g' or $item.type eq 'I' or $item.type eq 'q'}
	{if $list_mode eq 'y'}
		{$item.value|truncate:255:"..."|default:"&nbsp;"}
	{else}
		{$item.value}
	{/if}

{* -------------------- image -------------------- *}
{elseif $item.type eq 'i'}
	{if $item.value ne ''}
		<img border="0" src="{$item.value}"  width="{$item.options_array[0]}" height="{$item.options_array[1]}" alt="n/a" />
	{else}
		<img border="0" src="img/icons/na_pict.gif" alt="n/a" />
	{/if}

{* -------------------- textarea -------------------- *}
{elseif $item.type eq 'a'}
	{if $item.options_array[4] ne '' and $list_mode eq 'y'}
		{$item.value|truncate:$item.options_array[4]:"...":true}
	{else}
		{$item.value}
	{/if}

{* -------------------- date -------------------- *}
{elseif $item.type eq 'f' or $item.type eq 'j'}
	{$item.value|tiki_short_datetime|truncate:255:"..."|default:"&nbsp;"}

{* -------------------- checkbox -------------------- *}
{elseif $item.type eq 'c'}
	{$item.value|replace:"y":"Yes"|replace:"n":"No"|replace:"on":"Yes"}

{* -------------------- item link -------------------- *}
{elseif $item.type eq 'r'}
    {if $item.displayedvalue ne ""}
        {$item.displayedvalue}
    {else}
        {$item.value}
    {/if}

{* -------------------- items list -------------------- *}
{elseif $item.type eq 'l'}
	{foreach key=tid item=tlabel from=$item.links}
		{if $item.options_array[4] eq '1'}
			<div><a href="tiki-view_tracker_item.php?itemId={$tid}&trackerId={$item.options_array[0]}" class="link">{$tlabel|truncate:255:"..."}</a></div>
		{else}
			<div>{$tlabel|truncate:255:"..."}</div>
	{/if}
	{/foreach}

{* -------------------- country -------------------- *}
{elseif $item.type eq 'y'}
	{if !empty($item.value) and $item.value ne 'None'}
		{assign var=o_opt value=$item.options_array[0]}
		{capture name=flag}
		{tr}{$item.value}{/tr}
		{/capture}
		{if $o_opt ne '1'}<img border="0" src="img/flags/{$item.value}.gif" title="{$smarty.capture.flag|replace:'_':' '}" alt="{$smarty.capture.flag|replace:'_':' '}" />{/if}
		{if $o_opt ne '1' and $o_opt ne '2'}&nbsp;{/if}
		{if $o_opt ne '2'}{$smarty.capture.flag|replace:'_':' '}{/if}
	{else}
		&nbsp;
	{/if}

{* -------------------- mail -------------------- *}
{elseif $item.type eq 'm'}
	{if $item.options_array[0] eq '1' and $item.value}
		{mailto address=$item.value|escape encode="hex"}
	{elseif $item.options_array[0] eq '2' and $item.value}
		{mailto address=$item.value|escape encode="none"}
	{else}
		{$item.value|escape|default:"&nbsp;"}
	{/if}

{* -------------------- rating -------------------- *}
{elseif $item.type eq 's' and $item.name eq "Rating" and $tiki_p_tracker_view_ratings eq 'y'}
		<b title="{tr}Rating{/tr}: {$item.value|default:"-"}, {tr}Number of voices{/tr}: {$item.numvotes|default:"-"}, {tr}Average{/tr}: {$item.voteavg|default:"-"}">
			&nbsp;{$item.value|default:"-"}&nbsp;
		</b>
	{if $tiki_p_tracker_vote_ratings eq 'y'}
		<div nowrap="nowrap">
			<span class="button2">
			{if $item.my_rate eq NULL}
				<b class="linkbut highlight">-</b>
			{else}
				<a href="{$smarty.server.PHP_SELF}{if $query_string}?{$query_string}{else}?{/if}
					trackerId={$item.trackerId}
					&amp;rateitemId={$item.itemId}
					&amp;fieldId={$rateFieldId}
					&amp;rate_{$item.trackerId}=NULL"
					class="linkbut">-</a>
			{/if}
				{section name=i loop=$item.options_array}
					{if $item.options_array[i] eq $item.my_rate}
						<b class="linkbut highlight">{$item.options_array[i]}</b>
					{else}
						<a href="{$smarty.server.PHP_SELF}?
						trackerId={$item.trackerId}
						&amp;rateitemId={$item.itemId}
						&amp;fieldId={$rateFieldId}
						&amp;rate_{$item.trackerId}={$item.options_array[i]}"
						class="linkbut">{$item.options_array[i]}</a>
					{/if}
				{/section}
			</span>
		</div>
	{/if}

{* -------------------- header ------------------------- *}
(elseif $item.type eq 'h'}
	<h2>{$item.value}</h2>

{* -------------------- subscription -------------------- *}
{elseif $item.type eq 'U'}
	{$item.value|how_many_user_inscriptions} {tr}subscriptions{/tr}


{* -------------------- other field -------------------- *}
{* w *}
{else}
	{if $list_mode eq 'y'}
		{$item.value|truncate:255:"..."|default:"&nbsp;"}
	{else}
		{$item.value}
	{/if}
{/if}

{* ******************** append ******************** *}
{if ($item.type eq 't' or $item.type eq 'n' or $item.type eq 'c') 
 and $item.options_array[3]}<span class="formunit">&nbsp;{$item.options_array[3]}</span>
{/if}

{* ******************** link ******************** *}
{if $tiki_p_view_trackers eq 'y' or $tiki_p_modify_tracker_items eq 'y' or $tiki_p_comment_tracker_items eq 'y'}
</a>
{/if}

{/if}
{/strip}