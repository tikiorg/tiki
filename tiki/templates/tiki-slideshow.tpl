<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "DTD/xhtml1-transitional.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
  <link rel="stylesheet" href="styles/slideshows/{$slide_style}">
  <title>{$slide_title}</title>
  </head>
  <body bgcolor="#7d554b">
    <!-- this image map makes the cord plug a link to edit the page -->
    <map name="map">
      <area shape="RECT" coords="0,351,36,396" href="tiki-editpage.php?page={$page}">
    </map>
    <p /><p />
    <div align="center">
    <table bgcolor="#fffdeb" background="img/background.png" height="500" width="700" cellpadding="0" cellspacing="0" border="0">
      <tr>
        <td colspan="2" bgcolor="#67767d" height="50">
          <!-- The slide title -->
          <h1>&nbsp;{$slide_title}</h1>
        </td>
	<td height="50" valign="top" style="border-left:solid 2px #67767d">
	  &nbsp;
	</td>
      </tr>
      <tr>
        <td width="50" height="350" valign="left">
          <!-- The cord -->
          <img src="img/cord.png" USEMAP="#map" border="0" />
        </td>
	<td width="400" class="Main">
	  <p />
          {$slide_data}
          <p /><p />
        </td>
        <td width="170" style="border-left:solid 2px #67767d">
        <h2></h2>
        <font class="links"></font></td></tr>
        <tr><td colspan="3" height="50" bgcolor="#7d554b">
        <table width="100%" bgcolor="#7d554b"><tr><td width="100">
        <font class="buttons">
        {if $structure eq 'y'}
        <a class="buttons" href="tiki-slideshow2.php?page={$struct_prev}">{$struct_prev}</a></font></td>
        <td align="center"><font class="buttons"><a class="buttons" href="tiki-slideshow2.php?page={$struct_struct}">{$struct_struct}</a></font></td>
        <td width="100" align="right">
        <a class="buttons" href="tiki-slideshow2.php?page={$struct_next}">{$struct_next}</a>
        {else}
          {if $slide_prev_title}
            <a class="buttons" href="tiki-slideshow.php?page={$page}&amp;slide={$prev_slide}">{$slide_prev_title}</a></font>
          {/if}
          </td>
          <td align="center"><font class="buttons">{$current_slide} of {$total_slides}</font></td>
          <td width="100" align="right">
          {if $slide_next_title}
            <a class="buttons" href="tiki-slideshow.php?page={$page}&amp;slide={$next_slide}">{$slide_next_title}</a>
          {/if}
        {/if}
        </td></tr>
        </table>
        </td></tr>
        </table>
        </div>
  </body>
</html>



