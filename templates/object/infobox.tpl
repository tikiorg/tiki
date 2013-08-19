{if $plain}
{$content}
{else}
<h5>{object_link type=$type id=$object}</h5>
<div>{$content}</div>
{/if}
