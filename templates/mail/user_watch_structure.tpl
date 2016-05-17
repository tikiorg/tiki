{* $Id$ *}{if $action eq 'add'}
{tr}A page has been added to your watched {$prefs.mail_template_custom_text}sub-structure:{/tr}
{$name}
{$mail_machine}/tiki-index.php?page_ref_id={$page_ref_id}
{elseif $action eq 'remove'}
{tr}A page has been removed from your watched {$prefs.mail_template_custom_text}sub-structure:{/tr}
{$name}
{elseif $action eq 'move_up'}
{tr}A page has been promoted in your watched {$prefs.mail_template_custom_text}sub-structure:{/tr}
{$mail_machine}/tiki-index.php?page_ref_id={$page_ref_id}
{elseif $action eq 'move_down'}
{tr}A page has been demoted in your watched {$prefs.mail_template_custom_text}structure:{/tr}
{$mail_machine}/tiki-index.php?page_ref_id={$page_ref_id}
{/if}
