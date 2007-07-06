<div class="rbox" name="tip">
	<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
	<div class="rbox-data" name="tip">
	

	</div>
</div>
<br />

<div class="cbox">
  <div class="cbox-title">
    {tr}{$crumbs[$crumb]->description}{/tr}
    {help crumb=$crumbs[$crumb]}
  </div>

{include file=multiplayer.tpl url="" w=200 h=100 video='n'}
	
      <form action="tiki-admin.php?page=multimedia" method="post">
        <table class="admin">

        
        <tr>
        <td class="form">{tr}ProgressBarPlay Color{/tr}:</td><td class="form"><input type="text" name="ProgressBarPlay" value="{$ProgressBarPlay|escape}" size=7/><span style="height:8px;width:30px;background-color:{$ProgressBarPlay};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	<td class="form">{tr}ProgressBarLoad Color{/tr}:</td><td class="form"><input type="text" name="ProgressBarLoad" value="{$ProgressBarLoad|escape}" size=7/><span style="height:8px;width:30px;background-color:      {$ProgressBarLoad};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	</tr>
   	<tr>
        <td class="form">{tr}ProgressBarButton Color{/tr}:</td><td class="form"><input type="text" name="ProgressBarButton" value="{$ProgressBarButton|escape}" size=7/><span style="height:8px;width:30px;background-color:{$ProgressBarButton};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	<td class="form">{tr}ProgressBar Color{/tr}:</td><td class="form"><input type="text" name="ProgressBar" value="{$ProgressBar|escape}" size=7/><span  style="height:8px;width:30px;background-color:{$ProgressBar};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	</tr>
   	<tr>
        <td class="form">{tr}Volume On Color{/tr}:</td><td class="form"><input type="text" name="VolumeOn" value="{$VolumeOn|escape}" size=7/><span style="height:8px;width:30px;background-color:{$VolumeOn};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	<td class="form">{tr}Volume Off Color{/tr}:</td><td class="form"><input type="text" name="VolumeOff" value="{$VolumeOff|escape}" size=7/><span style="height:8px;width:30px;background-color:{$VolumeOff};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	</tr>
   	<tr>
	<td class="form">{tr}Volume Button Color{/tr}:</td><td class="form"><input type="text" name="VolumeButton" value="{$VolumeButton|escape}" size=7/><span style="height:8px;width:30px;background-color:{$VolumeButton};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	<td class="form">{tr}Button Color{/tr}:</td><td class="form"><input type="text" name="Button" value="{$Button|escape}" size=7/><span style="height:8px;width:30px;background-color:{$Button};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	</tr>
   	<tr>
   	<td class="form">{tr}Button Pressed Color{/tr}:</td><td class="form"><input type="text" name="ButtonPressed" value="{$ButtonPressed|escape}" size=7/><span style="height:8px;width:30px;background-color:{$ButtonPressed};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	<td class="form">{tr}Button Over Color{/tr}:</td><td class="form"><input type="text" name="ButtonOver" value="{$ButtonOver|escape}" size=7/><span style="height:8px;width:30px;background-color:{$ButtonOver};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	</tr>
   	<tr>
   	<td class="form">{tr}Button Info Color{/tr}:</td><td class="form"><input type="text" name="ButtonInfo" value="{$ButtonInfo|escape}" size=7/><span style="height:8px;width:30px;background-color:{$ButtonInfo};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	<td class="form">{tr}Button Info Pressed Color{/tr}:</td><td class="form"><input type="text" name="ButtonInfoPressed" value="{$ButtonInfoPressed|escape}" size=7/><span style="height:8px;width:30px;background-color:{$ButtonInfoPressed};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	</tr>
   	<tr>
   	<td class="form">{tr}Button Info Over Color{/tr}:</td><td class="form"><input type="text" name="ButtonInfoOver" value="{$ButtonInfoOver|escape}" size=7/><span style="height:8px;width:30px;background-color:{$ButtonInfoOver};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	<td class="form">{tr}Button Info Text Color{/tr}:</td><td class="form"><input type="text" name="ButtonInfoText" value="{$ButtonInfoText|escape}" size=7/><span  style="height:8px;width:30px;background-color:{$ButtonInfoText};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	</tr>
   	<tr>
   	<td class="form">{tr}ID3 Tag Color{/tr}:</td><td class="form"><input type="text" name="ID3" value="{$ID3|escape}" size=7/><span style="height:8px;width:30px;background-color:{$ID3};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	<td class="form">{tr}Play Time Color{/tr}:</td><td class="form"><input type="text" name="PlayTime" value="{$PlayTime|escape}" size=7/><span style="height:8px;width:30px;background-color:{$PlayTime};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	</tr>
	<tr>
   	<td class="form">{tr}Total Time Color{/tr}:</td><td class="form"><input type="text" name="TotalTime" value="{$TotalTime|escape}" size=7/><span style="height:8px;width:30px;background-color:{$TotalTime};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	<td class="form">{tr}Panel Display Color{/tr}:</td><td class="form"><input type="text" name="PanelDisplay" value="{$PanelDisplay|escape}" size=7/><span style="height:8px;width:30px;background-color:{$PanelDisplay};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	</tr>
	<tr>
   	<td class="form">{tr}Alert Message Color{/tr}:</td><td class="form"><input type="text" name="AlertMesg" value="{$AlertMesg|escape}" size=7/><span style="height:8px;width:30px;background-color:{$AlertMesg};">&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	</tr>
	

	<tr>
   	<td class="form">{tr}Video Lenght{/tr}:</td><td class="form"><input type="text" name="VideoLength" value="{$VideoLength|escape}" size=7/></td>
	<td class="form">{tr}Video Heigth{/tr}:</td><td class="form"><input type="text" name="VideoHeight" value="{$VideoHeight|escape}" size=7/></td>
	</tr>
	<tr>
   	<td class="form">{tr}Preload Delay{/tr}:</td><td class="form"><input type="text" name="PreloadDelay" value="{$PreloadDelay|escape}" size=7/></td>
	<td class="form">{tr}Max Play time{/tr}:</td><td class="form"><input type="text" name="MaxPlay" value="{$MaxPlay|escape}" size=7/></td>
 	</tr>
	<tr>
	<td  class="form">{tr}URL Append{/tr}:</td><td class="form"><input type="text" name="URLAppend" value="{$URLAppend|escape}" size="25" /></td>
	</tr>
   	<tr>
	<td  class="form">{tr}Message after limited time{/tr}:</td><td class="form"><input type="text" name="LimitedMsg" value="{$LimitedMsg|escape}" size="25"/></td>
	</tr>
	<td  class="form">{tr}ID of System File Galleries to upload multimedia files{/tr}:</td><td class="form"><input type="text" name="MultimediaGalerie" value="{$MultimediaGalerie|escape}" size="4"/></td>
	</tr>
        
        <td colspan="4" class="button"><input type="submit" name="multimediasetup" value="{tr}Save{/tr}" /></td>
	 	  
        </tr>
        </table>
      </form>

</div>

