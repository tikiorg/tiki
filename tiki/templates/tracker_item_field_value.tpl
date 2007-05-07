{* $Header: /cvsroot/tikiwiki/tiki/templates/tracker_item_field_value.tpl,v 1.2 2007-05-07 20:50:14 sylvieg Exp $ *}
{strip}
{* param: list_mode(y|n, default n), showlinks(y|n, default y), tiki_p_perm for this tracker, $item(type,value,displayedvalue,linkId,trackerId,itemId,links,categs,options_array, isMain) *}

{if $field_value.type ne 'x' and $field_value.type ne 'G'}
{* ******************** link to the item ******************** *}
{if $showlinks ne 'n'}
	{assign var='is_link' value='n'}
{elseif $field_value.isMain eq 'y'
 and ($tiki_p_view_trackers eq 'y' or $tiki_p_modify_tracker_items eq 'y' or $tiki_p_comment_tracker_items eq 'y'
 or ($tracker_info.writerCanModify eq 'y' and $user and $my eq $user) or ($tracker_info.writerCanModify eq 'y' and $group and $ours eq $group))}
	<a class="tablename" href="tiki-view_tracker_item.php?itemId={$item.itemId}&amp;trackerId={$item.trackerId}&amp;show=view{foreach key=urlkey item=urlval from=$urlquery}{if $urlval}&amp;{$urlkey}={$urlval|escape:"url"}{/if}{/foreach}">
	{assign var='is_link' value='y'}
{else}
	{assign var='is_link' value='n'}
{/if}

{* ******************** field with preprend ******************** *}
{if  ($field_value.type eq 't' or $field_value.type eq 'n' or $field_value.type eq 'c') 
 and !empty($field_value.options_array[2])}
	<span class="formunit">&nbsp;{$field_value.options_array[2]}</span>
{/if}

{* ******************** field handling emptiness in a specific way  ******************** *}
{* -------------------- category -------------------- *}
{if $field_value.type eq 'e'}
	{foreach from=$field_value.categs item=categ name=fcategs}
		{$categ.name}
		{if !$smarty.foreach.fcategs.last}<br />{/if}
	{/foreach}

{* -------------------- items list -------------------- *}
{elseif $field_value.type eq 'l'}
	{foreach key=tid item=tlabel from=$field_value.links}
		{if $field_value.options_array[4] eq '1'}
			<div><a href="tiki-view_tracker_item.php?itemId={$tid}&amp;trackerId={$field_value.options_array[0]}&amp;show=view" class="link">{$tlabel|truncate:255:"..."}</a></div>
		{else}
			<div>{$tlabel|truncate:255:"..."}</div>
	{/if}
	{/foreach}

{* -------------------- empty field -------------------- *}
{elseif empty($field_value.value)}
	&nbsp;

{* -------------------- test field, numeric, grop down, radio,user/group/IP selector, autopincrement, dynamic list *}
{elseif $field_value.type eq  't' or $field_value.type eq 'n' or $field_value.type eq 'd' or $field_value.type eq 'D' or $field_value.type eq 'R' or $field_value.type eq 'u' or $field_value.type eq 'g' or $field_value.type eq 'I' or $field_value.type eq 'q' or $field_value.type eq 'w'}
	{if $list_mode eq 'y'}
		{$field_value.value|truncate:255:"..."|default:"&nbsp;"}
	{else}
		{$field_value.value}
	{/if}

{* -------------------- image -------------------- *}
{elseif $field_value.type eq 'i'}
	{if $field_value.value ne ''}
		<img border="0" src="{$field_value.value}"  width="{$field_value.options_array[0]}" height="{$field_value.options_array[1]}" alt="n/a" />
	{else}
		<img border="0" src="img/icons/na_pict.gif" alt="n/a" />
	{/if}

{* -------------------- textarea -------------------- *}
{elseif $field_value.type eq 'a'}
	{if $field_value.options_array[4] ne '' and $list_mode eq 'y'}
		{$field_value.value|truncate:$field_value.options_array[4]:"...":true}
	{else}
		{$field_value.value}
	{/if}

{* -------------------- date -------------------- *}
{elseif $field_value.type eq 'f' or $field_value.type eq 'j'}
	{$field_value.value|tiki_short_datetime|truncate:255:"..."|default:"&nbsp;"}

{* -------------------- checkbox -------------------- *}
{elseif $field_value.type eq 'c'}
	{$field_value.value|replace:"y":"Yes"|replace:"n":"No"|replace:"on":"Yes"}

{* -------------------- item link -------------------- *}
{elseif $field_value.type eq 'r'}
    {if $field_value.options_array[2] eq '1'}
		<a href="tiki-view_tracker_item.php?trackerId={$field_value.options_array[0]}&amp;itemId={$field_value.linkId}" class="link">
	{/if}
	{if $field_value.displayedvalue ne ""}
        {$field_value.displayedvalue}
    {else}
        {$field_value.value}
    {/if}
	{if $field_value.options_array[2] eq '1'}
		</a>
	{/if}

{* -------------------- country -------------------- *}
{elseif $field_value.type eq 'y'}
	{if !empty($field_value.value) and $field_value.value ne 'None'}
		{assign var=o_opt value=$field_value.options_array[0]}
		{capture name=flag}
		{tr}{$field_value.value}{/tr}
		{/capture}
		{if $o_opt ne '1'}<img border="0" src="img/flags/{$field_value.value}.gif" title="{$smarty.capture.flag|replace:'_':' '}" alt="{$smarty.capture.flag|replace:'_':' '}" />{/if}
		{if $o_opt ne '1' and $o_opt ne '2'}&nbsp;{/if}
		{if $o_opt ne '2'}{$smarty.capture.flag|replace:'_':' '}{/if}
	{else}
		&nbsp;
	{/if}

{* -------------------- mail -------------------- *}
{elseif $field_value.type eq 'm'}
	{if $field_value.options_array[0] eq '1' and $field_value.value}
		{mailto address=$field_value.value|escape encode="hex"}
	{elseif $field_value.options_array[0] eq '2' and $field_value.value}
		{mailto address=$field_value.value|escape encode="none"}
	{else}
		{$field_value.value|escape|default:"&nbsp;"}
	{/if}

{* -------------------- rating -------------------- *}
{elseif $field_value.type eq 's' and $field_value.name eq "Rating" and $tiki_p_tracker_view_ratings eq 'y'}
		<b title="{tr}Rating{/tr}: {$field_value.value|default:"-"}, {tr}Number of voices{/tr}: {$field_value.numvotes|default:"-"}, {tr}Average{/tr}: {$field_value.voteavg|default:"-"}">
			&nbsp;{$field_value.value|default:"-"}&nbsp;
		</b>
	{if $tiki_p_tracker_vote_ratings eq 'y'}
		<div nowrap="nowrap">
			<span class="button2">
			{if $field_value.my_rate eq NULL}
				<b class="linkbut highlight">-</b>
			{else}
				<a href="{$smarty.server.PHP_SELF}{if $query_string}?{$query_string}{else}?{/if}
					trackerId={$field_value.trackerId}
					&amp;rateitemId={$field_value.itemId}
					&amp;fieldId={$rateFieldId}
					&amp;rate_{$field_value.trackerId}=NULL"
					class="linkbut">-</a>
			{/if}
				{section name=i loop=$field_value.options_array}
					{if $field_value.options_array[i] eq $field_value.my_rate}
						<b class="linkbut highlight">{$field_value.options_array[i]}</b>
					{else}
						<a href="{$smarty.server.PHP_SELF}?
						trackerId={$field_value.trackerId}
						&amp;rateitemId={$field_value.itemId}
						&amp;fieldId={$rateFieldId}
						&amp;rate_{$field_value.trackerId}={$field_value.options_array[i]}"
						class="linkbut">{$field_value.options_array[i]}</a>
					{/if}
				{/section}
			</span>
		</div>
	{/if}

{* -------------------- header ------------------------- *}
(elseif $field_value.type eq 'h'}
	<h2>{$field_value.value}</h2>

{* -------------------- subscription -------------------- *}
{elseif $field_value.type eq 'U'}
	{$field_value.value|how_many_user_inscriptions} {tr}subscriptions{/tr}


{* -------------------- other field -------------------- *}
{* w *}
{else}
	{if $list_mode eq 'y'}
		{$field_value.value|truncate:255:"..."|default:"&nbsp;"}
	{else}
		{$field_value.value}
	{/if}
{/if}

{* ******************** append ******************** *}
{if ($field_value.type eq 't' or $field_value.type eq 'n' or $field_value.type eq 'c') 
 and $field_value.options_array[3]}<span class="formunit">&nbsp;{$field_value.options_array[3]}</span>
{/if}

{* ******************** link ******************** *}
{if $is_link eq 'y'}
	</a>
{/if}

{/if}
{/strip}