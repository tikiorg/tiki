<a class="pagetitle" href="tiki-survey_stats_survey.php?surveyId={$surveyId}">{tr}Stats for survey{/tr}:{$survey_info.name}</a><br /><br />
[<a class="link" href="tiki-list_surveys.php">{tr}list surveys{/tr}</a>
|<a class="link" href="tiki-survey_stats.php">{tr}survey stats{/tr}</a>
|<a class="link" href="tiki-admin_surveys.php?surveyId={$surveyId}">{tr}edit this survey{/tr}</a>
{if $tiki_p_admin_surveys eq 'y'}|<a class="link" href="tiki-survey_stats_survey.php?surveyId={$surveyId}&amp;clear={$surveyId}">{tr}clear stats{/tr}</a>{/if}
|<a class="link" href="tiki-admin_surveys.php">{tr}admin surveys{/tr}</a>]<br /><br />
<h2>{tr}Survey stats{/tr}</h2>

<h2>{tr}Stats for this survey Questions {/tr}</h2>
{section name=ix loop=$channels}
  <table class="normal">
  <tr>
    <td colspan="4" class="heading">{$channels[ix].question}</td>
  </tr>
  {if $channels[ix].type eq 'r'}
    <tr>
      <td  class="odd">Votes:</td>
      <td  class="odd">{$channels[ix].votes}</td>
    </tr>
    <tr>
      <td class="odd">{tr}Average{/tr}:</td>
      <td class="odd">{$channels[ix].average|string_format:"%.2f"}</td>
    </tr>
  {elseif $channels[ix].type eq 's'}
    <tr>
      <td  class="odd">Votes:</td>
      <td  class="odd">{$channels[ix].votes}</td>
    </tr>
    <tr>
      <td class="odd">{tr}Average{/tr}:</td>
      <td class="odd">{$channels[ix].average|string_format:"%.2f"}/10</td>
    </tr>
  {else}
    {section name=jx loop=$channels[ix].qoptions}
    <tr>
      <td  class="odd">{$channels[ix].qoptions[jx].qoption}</td>
      <td  class="odd">{$channels[ix].qoptions[jx].votes}</td>
      <td  class="odd">{$channels[ix].qoptions[jx].average|string_format:"%.2f"}</td>
      <td  class="odd"><img src="img/leftbar.gif" alt="<" /><img alt="-" src="img/mainbar.gif" height="14" width="{$channels[ix].qoptions[jx].width}" /><img src="img/rightbar.gif" alt=">" /></td>
    </tr>
    {/section}
  {/if}
  </table>
  <br />
{/section}
