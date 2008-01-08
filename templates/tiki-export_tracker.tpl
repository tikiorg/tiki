"status","itemId"{
if $which eq 'all' or ($which eq 'list' and $tracker_info.showCreated eq 'y') or ($which eq 'item' and $tracker_info.showCreatedView eq 'y')},"created"{/if}{
if $which eq 'all' or ($which eq 'list' and $tracker_info.showLastModif eq 'y') or ($which eq 'item' and $tracker_info.showLastModifView eq 'y')},"lastModif"{/if},"categories",{
foreach item=x key=ix from=$listfields}"{$x.name} -- {$ix}",{/foreach}

{section name=user loop=$items}

"{$items[user].status}","{$items[user].itemId}"{
if $which eq 'all' or ($which eq 'list' and $tracker_info.showCreated eq 'y') or ($which eq 'item' and $tracker_info.showCreatedView eq 'y')},"{$items[user].created|tiki_short_datetime}"{/if}{
if $which eq 'all' or ($which eq 'list' and $tracker_info.showLastModif eq 'y') or ($which eq 'item' and $tracker_info.showLastModifView eq 'y')},"{$items[user].lastModif|tiki_short_datetime}"{/if},"{$items[user].categs}",{
section name=ix loop=$items[user].field_values}{

if $items[user].field_values[ix].type eq 'a'}
"{$items[user].field_values[ix].value|replace:"\r\n":"%%%"}",{
elseif isset($items[user].field_values[ix].links)}
{foreach key=k item=l from=$items[user].field_values[ix].links name=links}{$l}{if !$smarty.foreach.links.last},{/if}{/foreach}",{
else}
"{$items[user].field_values[ix].value}",{/if}
{/section}
{/section}
