<h1><a href="tiki-invited.php" class="pagetitle">{tr}Invitation{/tr}</a></h1>

{if $error}
 {$error|escape}
{else}

 <div  class="wikitext">{$parsed}</div>

 {if $user}
  You are currently logged as "{$user|escape}", if you want to validate this invitation on this account, click here :
  <form method='POST'><input type='submit' name='validate-existing-account' value='Validate'/></form>
 {else}
  <div>
   If you want to validate this invitation on an already existing account, please login first: : <a href='tiki-login_scr.php'>login</a>
  </div>
  <div>
   If you don't have any account yet, you can create one <a href="tiki-register.php?invit={$invit|escape}&amp;email={$email|escape:'url'}">here</a>
  </div>
 {/if}
{/if}