{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}It works with the File Gallery, wiki pages (via a plugin), and a new multimedia tracker item{/tr}{/remarksbox}


<div class="cbox">
  <div class="cbox-title">
    {tr}{$crumbs[$crumb]->description}{/tr}
    {help crumb=$crumbs[$crumb]}
  </div>

{include file=multiplayer.tpl url="" w=$prefs.MultimediaDefaultLength h=$prefs.MultimediaDefaultHeight video='n'}
	
      <form action="tiki-admin.php?page=multimedia" method="post">
        <table class="admin">

        
        <tr>
        <td class="form">{tr}ProgressBarPlay Color{/tr}:</td><td class="form"><input type="text" name="ProgressBarPlay" value="{$prefs.ProgressBarPlay|escape}" size=7/><span style="height:8px;width:30px;background-color:{$prefs.ProgressBarPlay};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	<td class="form">{tr}ProgressBarLoad Color{/tr}:</td><td class="form"><input type="text" name="ProgressBarLoad" value="{$prefs.ProgressBarLoad|escape}" size=7/><span style="height:8px;width:30px;background-color:      {$prefs.ProgressBarLoad};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	</tr>
   	<tr>
        <td class="form">{tr}ProgressBarButton Color{/tr}:</td><td class="form"><input type="text" name="ProgressBarButton" value="{$prefs.ProgressBarButton|escape}" size=7/><span style="height:8px;width:30px;background-color:{$prefs.ProgressBarButton};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	<td class="form">{tr}ProgressBar Color{/tr}:</td><td class="form"><input type="text" name="ProgressBar" value="{$prefs.ProgressBar|escape}" size=7/><span  style="height:8px;width:30px;background-color:{$prefs.ProgressBar};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	</tr>
   	<tr>
        <td class="form">{tr}Volume On Color{/tr}:</td><td class="form"><input type="text" name="VolumeOn" value="{$prefs.VolumeOn|escape}" size=7/><span style="height:8px;width:30px;background-color:{$prefs.VolumeOn};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	<td class="form">{tr}Volume Off Color{/tr}:</td><td class="form"><input type="text" name="VolumeOff" value="{$prefs.VolumeOff|escape}" size=7/><span style="height:8px;width:30px;background-color:{$prefs.VolumeOff};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	</tr>
   	<tr>
	<td class="form">{tr}Volume Button Color{/tr}:</td><td class="form"><input type="text" name="VolumeButton" value="{$prefs.VolumeButton|escape}" size=7/><span style="height:8px;width:30px;background-color:{$prefs.VolumeButton};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	<td class="form">{tr}Button Color{/tr}:</td><td class="form"><input type="text" name="Button" value="{$prefs.Button|escape}" size=7/><span style="height:8px;width:30px;background-color:{$prefs.Button};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	</tr>
   	<tr>
   	<td class="form">{tr}Button Pressed Color{/tr}:</td><td class="form"><input type="text" name="ButtonPressed" value="{$prefs.ButtonPressed|escape}" size=7/><span style="height:8px;width:30px;background-color:{$prefs.ButtonPressed};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	<td class="form">{tr}Button Over Color{/tr}:</td><td class="form"><input type="text" name="ButtonOver" value="{$prefs.ButtonOver|escape}" size=7/><span style="height:8px;width:30px;background-color:{$prefs.ButtonOver};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	</tr>
   	<tr>
   	<td class="form">{tr}Button Info Color{/tr}:</td><td class="form"><input type="text" name="ButtonInfo" value="{$prefs.ButtonInfo|escape}" size=7/><span style="height:8px;width:30px;background-color:{$prefs.ButtonInfo};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	<td class="form">{tr}Button Info Pressed Color{/tr}:</td><td class="form"><input type="text" name="ButtonInfoPressed" value="{$prefs.ButtonInfoPressed|escape}" size=7/><span style="height:8px;width:30px;background-color:{$prefs.ButtonInfoPressed};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	</tr>
   	<tr>
   	<td class="form">{tr}Button Info Over Color{/tr}:</td><td class="form"><input type="text" name="ButtonInfoOver" value="{$prefs.ButtonInfoOver|escape}" size=7/><span style="height:8px;width:30px;background-color:{$prefs.ButtonInfoOver};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	<td class="form">{tr}Button Info Text Color{/tr}:</td><td class="form"><input type="text" name="ButtonInfoText" value="{$prefs.ButtonInfoText|escape}" size=7/><span  style="height:8px;width:30px;background-color:{$prefs.ButtonInfoText};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	</tr>
   	<tr>
   	<td class="form">{tr}ID3 Tag Color{/tr}:</td><td class="form"><input type="text" name="ID3" value="{$prefs.ID3|escape}" size=7/><span style="height:8px;width:30px;background-color:{$prefs.ID3};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	<td class="form">{tr}Play Time Color{/tr}:</td><td class="form"><input type="text" name="PlayTime" value="{$prefs.PlayTime|escape}" size=7/><span style="height:8px;width:30px;background-color:{$prefs.PlayTime};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	</tr>
	<tr>
   	<td class="form">{tr}Total Time Color{/tr}:</td><td class="form"><input type="text" name="TotalTime" value="{$prefs.TotalTime|escape}" size=7/><span style="height:8px;width:30px;background-color:{$prefs.TotalTime};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	<td class="form">{tr}Panel Display Color{/tr}:</td><td class="form"><input type="text" name="PanelDisplay" value="{$prefs.PanelDisplay|escape}" size=7/><span style="height:8px;width:30px;background-color:{$prefs.PanelDisplay};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	</tr>
	<tr>
   	<td class="form">{tr}Alert Message Color{/tr}:</td><td class="form"><input type="text" name="AlertMesg" value="{$prefs.AlertMesg|escape}" size=7/><span style="height:8px;width:30px;background-color:{$prefs.AlertMesg};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	</tr>
	
	<tr>
   	<td class="form">{tr}Flash Lenght{/tr}:</td><td class="form"><input type="text" name="MultimediaDefaultLength" value="{$prefs.MultimediaDefaultLength|escape}" size=7/></td>
	<td class="form">{tr}Flash Heigth{/tr}:</td><td class="form"><input type="text" name="MultimediaDefaultHeight" value="{$prefs.MultimediaDefaultHeight|escape}" size=7/></td>
	</tr>
	<tr>

	<tr>
   	<td class="form">{tr}Video Lenght{/tr}:</td><td class="form"><input type="text" name="VideoLength" value="{$prefs.VideoLength|escape}" size=7/></td>
	<td class="form">{tr}Video Heigth{/tr}:</td><td class="form"><input type="text" name="VideoHeight" value="{$prefs.VideoHeight|escape}" size=7/></td>
	</tr>
	<tr>
   	<td class="form">{tr}Preload Delay{/tr}:</td><td class="form"><input type="text" name="PreloadDelay" value="{$prefs.PreloadDelay|escape}" size=7/></td>
	<td class="form">{tr}Max Play time{/tr}:</td><td class="form"><input type="text" name="MaxPlay" value="{$prefs.MaxPlay|escape}" size=7/></td>
 	</tr>
	<tr>
	<td  class="form">{tr}URL Append{/tr}:</td><td class="form"><input type="text" name="URLAppend" value="{$prefs.URLAppend|escape}" size="25" /></td>
	</tr>
   	<tr>
	<td  class="form">{tr}Message after limited time{/tr}:</td><td class="form"><input type="text" name="LimitedMsg" value="{$prefs.LimitedMsg|escape}" size="25"/></td>
	</tr>
   	<tr>
	<td  class="form">{tr}ID of System File Galleries to upload multimedia files{/tr}:</td><td class="form"><input type="text" name="MultimediaGalerie" value="{$prefs.MultimediaGalerie|escape}" size="4"/></td>
	</tr>
   	<tr>
	<td colspan="4" class="button"><input type="submit" name="multimediasetup" value="{tr}Save{/tr}" /></td>
	</tr>
        </table>
      </form>

</div>

