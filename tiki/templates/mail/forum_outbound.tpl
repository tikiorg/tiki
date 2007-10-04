{* $Header: /cvsroot/tikiwiki/tiki/templates/mail/forum_outbound.tpl,v 1.4 2007-10-04 22:17:46 nyloth Exp $ *}
{$prefs.title}

{tr}Author:{/tr} {$author}

{$data}
{if $reply_link}

---
Reply Link: <{$reply_link}>
{/if}