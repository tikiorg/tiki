<h1>Trolls Webcams</h1>
<span class="button2"><a href="multicam.php" class="linkbut">Refresh</a></span><br /><br />
<script><!-- 
{literal}
function reloadimage() {
{/literal}
{foreach key=who item=img from=$cams}
  iframe{$who} = document.getElementById('{$who}');
  if (!iframe{$who}) return false;
  iframe{$who}.src = iframe{$who}.src;
{/foreach}
  setTimeout("reloadimage()",30000);
{literal}
}
{/literal}
--></script>
<style><!--
{literal}
.webcam { border : 1px solid #000; margin : 0px; }
{/literal}
--></style>

<div class="simplebox">To add your webcam here, add your login and the url of your
webcam image (320x240, 1 image / 30s) to the wiki page <a href='/WebCams'>WebCams</a>.</div>

<div align="center">
<table width="{if not $cams[1]}320{else}640{/if}"><tr>
{cycle values=",</tr><tr>" advance=false print=false}
{foreach key=who item=img from=$cams}
<td>
<div style="color:#fff;background-color:#000;padding:0 3px;"><b>{$who}</b> 
<span style="color:#666;font-size:10px;">at {$cam_loc.$who}</span><br />
<span style="color:#999;font-size:10px;">{$cam_info.$who}</span></div>
<iframe class="webcam" src="{$img}" id="{$who}" name="{$who}" width="322" height="242" frameborder="0" marginwidth="1" marginheight="1" scrolling="no"></iframe>
</td>
{cycle}
{/foreach}
</tr></table>
</div>
<script><!-- 
reloadimage(); 
--></script>

