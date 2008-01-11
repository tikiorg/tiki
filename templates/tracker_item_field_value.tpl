{strip}
{* $Header: /cvsroot/tikiwiki/tiki/templates/tracker_item_field_value.tpl,v 1.19.2.3 2008-01-11 21:55:34 sylvieg Exp $ *}
{* param: list_mode(y|n, default n), showlinks(y|n, default y), tiki_p_perm for this tracker, $field_value(type,value,displayedvalue,linkId,trackerId,itemId,links,categs,options_array, isMain), item *}

{if $field_value.type ne 'x'}
{* ******************** link to the item ******************** *}
{if $showlinks ne 'y'}
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

{* -------------------- static text -------------------- *}
{elseif $field_value.type eq 'S'}
	{if $field_value.options_array[0] ne '' and $list_mode eq 'y'}
		{$field_value.description|truncate:$field_value.options_array[0]:"...":true|escape}
	{else}
		{wiki}{$field_value.description|escape|nl2br}{/wiki}
	{/if}

{* -------------------- empty field -------------------- *}
{elseif empty($field_value.value) and $field_value.type ne 'U'}
	&nbsp;

{* -------------------- test field, numeric, grop down, radio,user/group/IP selector, autopincrement, dynamic list *}
{elseif $field_value.type eq  't' or $field_value.type eq 'n' or $field_value.type eq 'd' or $field_value.type eq 'D' or $field_value.type eq 'R' or $field_value.type eq 'u' or $field_value.type eq 'g' or $field_value.type eq 'I' or $field_value.type eq 'q' or $field_value.type eq 'w' or $field_value.type eq 'C'}
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

{* -------------------- Multimedia -------------------- *}
{elseif $field_value.type eq 'M'}
	{if $field_value.value ne ''}	
	{if  $field_value.options_array[1] ne '' }
		{assign var='Height' value=$prefs.MultimediaDefaultHeight}
	{else}
		{assign var='Height' value=$field_value.options_array[1]}
	{/if}
	{if  $field_value.options_array[2] ne '' }
		{assign var='Lenght' value=$field_value.options_array[2]}
	{else}
		{assign var='Lenght' value=$prefs.MultimediaDefaultLength}
	{/if}
	{if $ModeVideo eq 'y' } { assign var="Height" value=$Height+$prefs.VideoHeight}{/if}
	{include file=multiplayer.tpl url=$field_value.value w=$Lenght h=$Height video=$ModeVideo}
	{/if}


{* -------------------- textarea -------------------- *}
{elseif $field_value.type eq 'a'}
	{if $field_value.options_array[4] ne '' and $list_mode eq 'y'}
		{wiki}{$field_value.value|truncate:$field_value.options_array[4]:"...":true}{/wiki}
	{else}
		{wiki}{$field_value.value}{/wiki}
	{/if}

{* -------------------- date -------------------- *}
{elseif $field_value.type eq 'f' or $field_value.type eq 'j'}
	{if $field_value.value}
		{if $field_value.options_array[0] eq 'd'}
			{$field_value.value|tiki_short_date}
		{else}
			{$field_value.value|tiki_short_datetime}
		{/if}
	{else}&nbsp;{/if}

{* -------------------- checkbox -------------------- *}
{elseif $field_value.type eq 'c'}
	{if $field_value.value eq 'y' or $field_value.value eq 'on'}
		{tr}Yes{/tr}
	{elseif $field_value.value eq 'n'}
		{tr}No{/tr}
	{else}
		{$field_value.value}
	{/if}

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
{elseif $field_value.type eq 's' and ($field_value.name eq "Rating" or $field_value.name eq tra("Rating")) and $tiki_p_tracker_view_ratings eq 'y'}
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
{elseif $field_value.type eq 'h'}
	<h2>{$field_value.value}</h2>

{* -------------------- subscription -------------------- *}
{elseif $field_value.type eq 'U'}
	{$field_value.value|how_many_user_inscriptions} {tr}Subscriptions{/tr}
	{if $list_mode ne 'y'}
	{if $field_value.maxsubscriptions}(max : {$field_value.maxsubscriptions}){/if} :
	{foreach from=$field_value.users_array name=U_user item=U_user}
		{$U_user.login|userlink}{if $U_user.friends} (+{$U_user.friends}){/if}{if $smarty.foreach.U_user.last}{else},&nbsp;{$last}{/if}
	{/foreach}
	{if $user}
		<br />
		{if $field_value.user_subscription} {tr}You have ever subscribed{/tr}.{else}{tr}You have not yet subscribed{/tr}.{/if}
		<form method="POST" action="{$smarty.server.REQUEST_URI}" >
		<input type="hidden" name="U_fieldId" value="{$field_value.fieldId}" />
		<input type="hidden" name="itemId" value="{$itemId}" />
		<input type="hidden" name="trackerId" value="{$trackerId}" />
		<input type="submit" name="user_subscribe" value="{tr}Subscribe{/tr}" /> {tr}with{/tr}
		{if $U_liste}
			{html_options options=$U_liste name="user_friends" selected=$field_value.user_nb_friends} {tr}friends{/tr}
		{else}
			<input type="text" size="4" name="user_friends" value="{$field_value.user_nb_friends}" /> {tr}friends{/tr}
		{/if}
		{if $field_value.user_subscription}<br /><input type="submit" name="user_unsubscribe" value="{tr}Unsubscribe{/tr}" />{/if}
		</form>
	{/if}
	{/if}

{* -------------------- google map -------------------- *}
{elseif $field_value.type eq 'G'}
	{if $prefs.feature_gmap eq 'y'}
	Google Map : X = {$field_value.x} ; Y = {$field_value.y} ; Zoom = {$field_value.z}
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key={$prefs.gmap_key}" type="text/javascript">
	</script>
	<div id="map" style="width: 500px; height: 400px;border: 1px solid #000;">
	</div>
	<script type="text/javascript">
	//<![CDATA[
	function load() {literal}{{/literal}
	var map = new GMap2(document.getElementById("map"));
	  map.addControl(new GLargeMapControl());
	  map.addControl(new GMapTypeControl());
	  map.addControl(new GScaleControl());
	  map.setCenter(new GLatLng({$field_value.y}, {$field_value.x}), {$field_value.z});
	  map.addOverlay(new GMarker(new GLatLng({$field_value.y},{$field_value.x})));

/*	  GEvent.addListener(map, "zoomend", function(gold, gnew) {literal}{{/literal}
	    document.getElementById('defz').value = gnew;
	    document.getElementById('pointz').value = gnew;
	  {literal}});{/literal}

	  GEvent.addListener(map, "moveend", function() {literal}{{/literal}
	    document.getElementById('defx').value = map.getCenter().x;
	    document.getElementById('defy').value = map.getCenter().y;
	  {literal}});{/literal}
*/
	{literal}}{/literal}
//	load();
	//]]>
	window.onload=load;
	</script>
	{else}
	  {tr}Google Maps is not enabled.{/tr}
	{/if}

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
