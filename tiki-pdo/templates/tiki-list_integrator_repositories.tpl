{* $Id$ *}

<h2>{tr}Available Repositories{/tr}</h2>

{if $tiki_p_admin eq 'y'}
<div id="page-bar">
  <table><tr>
    <td><div class="button2">
      <a href="tiki-admin_integrator.php" class="linkbut">{tr}Configure Repositories{/tr}</a>
    </div></td>
  </tr></table>
</div>
{/if}
<br />


{* Table with list of repositories (if array is not empty) *}
{if count($repositories) gt 0}
<table class="normal" id="integrator-repositories">
  <tr>
    <td class="heading">{tr}Name{/tr}</td>
    <td class="heading">{tr}Description{/tr}</td>
  </tr>
  {cycle values="odd,even" print=false}
  {section name=rep loop=$repositories}
    <tr>
      <td class="{cycle advance=false}">
        <a href="tiki-integrator.php?repID={$repositories[rep].repID|escape}">
          {$repositories[rep].name}
        </a>
      </td>
      <td class="{cycle}">{$repositories[rep].description}</td>
    </tr>
  {/section}
</table>
{else}

{* Here should be panel (let it be style 'info-panel') with info
 * Smth like: "No configured/visible repositories...", but if
 * current user with tiki_p_admin it continue with "Ypu may setup
 * repositories on the following page (or by press button above :)"
 *
 * Moreover such 'info' panels can be everywhere :) -- at least at
 * wiki edit help and comments help ... let it be standart way to
 * display hints. :) -- not separate styles... to be personalized
 * (if smbd needs :) it can contain id attribute... i.e. smth like
 * <div class='info-panel' id='wiki-help'>...</div>
 * <div class='info-panel' id='comments-help'>...</div>
 * <div class='info-panel' id='integrator-no-reps'>...</div>
 *}

{/if}
