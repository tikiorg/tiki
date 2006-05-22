{section name=ix loop=$fields}"{$fields[ix].name|default:"&nbsp;"}";{/section}{if $tracker_info.showCreated eq 'y'};"{tr}created{/tr}"{/if}{if $tracker_info.showLastModif eq 'y'};"{tr}lastModif{/tr}"{/if}
{section name=user loop=$items}

{section name=ix loop=$items[user].field_values}{

if $items[user].field_values[ix].type eq 'l'}
"({foreach key=tid item=tlabel from=$items[user].field_values[ix].links}'{$tlabel|escape}',{/foreach})";{

elseif $items[user].field_values[ix].type eq 'f'}
"{$items[user].field_values[ix].value|tiki_short_datetime|truncate:255:"..."|default:"&nbsp;"}";{

elseif $items[user].field_values[ix].type eq 'c'}
"{$items[user].field_values[ix].value|replace:"y":"Yes"|replace:"n":"No"|replace:"on":"Yes"}";{

elseif $items[user].field_values[ix].type eq 'a'}
"{$items[user].field_values[ix].pvalue}";{

elseif $items[user].field_values[ix].type eq 'i'}
"";{

elseif $items[user].field_values[ix].type eq 'e'}
"({foreach item=ii from=$items[user].field_values[ix].categs}'{$ii.name}',{/foreach})";{

elseif $items[user].field_values[ix].type eq 'y'}
"{tr}{$items[user].field_values[ix].value}{/tr}";{

elseif $items[user].field_values[ix].type eq 's' and $items[user].field_values[ix].name eq "Rating" and $tiki_p_tracker_view_ratings eq 'y'}
"{$items[user].field_values[ix].value|default:"-"}";{

else}
"{$items[user].field_values[ix].value}";{/if}{/section}{

if $tracker_info.showCreated eq 'y'}
"{$items[user].created|tiki_short_datetime}";{/if}{

if $tracker_info.showLastModif eq 'y'}
"{$items[user].lastModif|tiki_short_datetime}";{/if}
{/section}

