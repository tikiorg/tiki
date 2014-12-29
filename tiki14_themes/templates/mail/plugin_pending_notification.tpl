{tr _0=$plugin_name _1={object_link type=$type id=$objectId}}Plugin %0 is pending approval on %1.{/tr}

{tr _0="{$base_url}tiki-plugins.php"}See all the pending plugins in the <a href='%0'>plugin approval page</a>.{/tr}

{if !empty($arguments)}
	<b>{tr}Plugin arguments:{/tr}</b>
	{foreach $arguments as $key => $value}
		* {$key}: {$value}
	{/foreach}
{/if}

{if !empty($body)}
	<b>{tr}Plugin body:{/tr}</b>
	{$body}
{/if}