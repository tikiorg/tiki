<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
  <link rel="stylesheet" href="styles/slideshows/{$slide_style}" />
  <title>{$page_info.pageName}</title>
  </head>
  <body bgcolor="#7d554b">
    <!-- this image map makes the cord plug a link to edit the page -->
    <map name="map">
      <area shape="RECT" coords="0,351,36,396" href="tiki-editpage.php?page={$page_info.pageName}">
    </map>
    <p /><p />
    <div align="center">
    <table bgcolor="#fffdeb" background="img/background.png" height="500" width="700" cellpadding="0" cellspacing="0" border="0">
      <tr>
        <td colspan="2" bgcolor="#67767d" height="50">
          <!-- The slide title -->
          <h1>&nbsp;{$page_info.pageName}</h1>
        </td>
	<td height="50" valign="top" style="border-left:solid 2px #67767d">
	  &nbsp;
	</td>
      </tr>
      <tr>
        <td width="50" height="350" valign="left">
          <!-- The cord -->
          <img src="img/cord.png" USEMAP="#map" border="0" 
		alt='{tr}The cord{/tr}'/>
        </td>
	<td width="400" class="Main">
	  <p />
          {$slide_data}
          <p /><p />
        </td>
        <td width="170" style="border-left:solid 2px #67767d">
        <h2></h2>
        <font class="links"></font></td></tr>
        <tr bgcolor="#7d554b">
        {if $structure eq 'y'}
	<td><font class="buttons">
        <a class="buttons" href="tiki-slideshow2.php?page_ref_id={$prev_info.page_ref_id}">{$prev_info.pageName}</a>
	</font></td>
        <td align="center"><font class="buttons">
        <a class="buttons" href="tiki-slideshow2.php?page_ref_id={$home_info.page_ref_id}">{$home_info.pageName}</a>
	</font></td>
        <td align="right">
        <a class="buttons" href="tiki-slideshow2.php?page_ref_id={$next_info.page_ref_id}">{$next_info.pageName}</a>
        {else}
          <td width="100"><font class="buttons">
          {if $slide_prev_title}
            <a href="tiki-slideshow.php?page={$page}&amp;slide={$prev_slide}">{$slide_prev_title}</a>
          {/if}
          </font></td>
          <td align="center"><font class="buttons">{$current_slide} of {$total_slides}</font></td>
          <td width="100" align="right"><font class="buttons">
          {if $slide_next_title}
            <a class="buttons" href="tiki-slideshow.php?page={$page}&amp;slide={$next_slide}">{$slide_next_title}</a>
          {/if}
          </font></td>
        </td>
        {/if}
	</tr>
        </table>
        </div>
  </body>
</html>



