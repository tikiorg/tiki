{foreach item=x from=$listfields name=foo}"{$x}"{if !$smarty.foreach.foo.last},{/if}{/foreach}

{section name=ix loop=$users}
{foreach item=x from=$listfields name=foo}"{$users[ix].$x}"{if !$smarty.foreach.foo.last},{/if}{/foreach}

{/section}
