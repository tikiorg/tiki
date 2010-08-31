{title}{tr}Stats for survey:{/tr} {$survey_info.name|escape}{/title}

<div class="navbar">
	{self_link print='y'}{icon _id='printer' align='right' hspace='1' alt="{tr}Print{/tr}"}{/self_link}
	{button href="tiki-list_surveys.php" _text="{tr}List Surveys{/tr}"}
	{button href="tiki-survey_stats.php" _text="{tr}Survey Stats{/tr}"}
	{if $tiki_p_admin_surveys eq 'y'}
		{button href="tiki-admin_surveys.php?surveyId=`$surveyId`" _text="{tr}Edit this Survey{/tr}"}
		{button href="tiki-survey_stats_survey.php?surveyId=`$surveyId`&amp;clear=`$surveyId`" _text="{tr}Clear Stats{/tr}"}
		{button href="tiki-admin_surveys.php" _text="{tr}Admin Surveys{/tr}"}
	{/if}
</div>
<br />

{section name=ix loop=$channels}
  <table class="normal">
  <tr>
    <th colspan="4">{$channels[ix].question|escape|nl2br}</th>
  </tr>
  {if $channels[ix].type eq 'r'}
    <tr>
      <td class="odd">{tr}Votes:{/tr}</td>
      <td class="odd">{$channels[ix].votes}</td>
    </tr>
    <tr>
      <td class="odd">{tr}Average:{/tr}</td>
      <td class="odd">{$channels[ix].average|string_format:"%.2f"}</td>
    </tr>
  {elseif $channels[ix].type eq 's'}
    <tr>
      <td class="odd">{tr}Votes:{/tr}</td>
      <td class="odd">{$channels[ix].votes}</td>
    </tr>
    <tr>
      <td class="odd">{tr}Average:{/tr}</td>
      <td class="odd">{$channels[ix].average|string_format:"%.2f"}/10</td>
    </tr>
  {else}
    {section name=jx loop=$channels[ix].qoptions}
    <tr>
      <td class="odd">
        {if $channels[ix].type eq 'g'}
          <div style="float:left">
          {thumb _id=$channels[ix].qoptions[jx].qoption _max=40 name='thumb' style='margin:3px;'}
          </div>
          <div>
            {fileinfo _id=$channels[ix].qoptions[jx].qoption _field='name' _link='thumb'}
            <br />{fileinfo _id=$channels[ix].qoptions[jx].qoption _field='description'}
          </div>
        {else}
          {$channels[ix].qoptions[jx].qoption|escape}
        {/if}
      </td>
      <td class="odd">{$channels[ix].qoptions[jx].votes}</td>
      <td class="odd">{$channels[ix].qoptions[jx].average|string_format:"%.2f"}%</td>
      <td class="odd">{quotabar length=$channels[ix].qoptions[jx].width}</td>
    </tr>
    {/section}
  {/if}
  </table>
  <br />
{/section}
