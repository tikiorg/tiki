{foreach item=x from=$listfields name=foo}"{$x}"{if !$smarty.foreach.foo.last},{/if}{/foreach}

{section name=ix loop=$users}
{foreach item=x from=$listfields name=foo}"{if $x eq 'lastLogin'}{$users[ix].$x|tiki_short_datetime}{else}{$users[ix].$x|replace:'"':'""'}{/if}"{if !$smarty.foreach.foo.last},{/if}{/foreach}

{/section}
