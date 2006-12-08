<h1><a class="pagetitle" href="tiki-config_pdf.php">{tr}Create PDF{/tr}</a></H1>
<div class="cbox">
<div class="cbox-title">
{tr}PDF Settings{/tr}
</div>
<div class="cbox-data">
<form method="post" action="tiki-config_pdf.php{if $page_ref_id ge '-1'}?page_ref_id={$page_ref_id}{/if}">
<input type="hidden" name="convertpages" value="{$form_convertpages|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="50%">
		<table >
			<tr><td class="form">{tr}Format{/tr}:</td>
			<td class="form">
			<select name="media" id="medi" style="width:111px">
			<!--Can use php here to obtain predefined media types OR leave as is-->
			<option value="Letter" {if $media eq 'Letter'}selected{/if}>Letter</option>
			<option value="Legal" {if $media eq 'Legal'}selected{/if}>Legal</option>
			<option value="Executive" {if $media eq 'Executive'}selected{/if}>Executive</option>
			<option value="A0Oversize" {if $media eq 'A0Oversize'}selected{/if}>A0Oversize</option>
			<option value="A0" {if $media eq 'A0'}selected{/if}>A0</option>
			<option value="A1" {if $media eq 'A1'}selected{/if}>A1</option>
			<option value="A2" {if $media eq 'A2'}selected{/if}>A2</option>
			<option value="A3" {if $media eq 'A3'}selected{/if}>A3</option>
			<option value="A4" {if $media eq 'A4'}selected{/if}>A4</option>
			<option value="A5" {if $media eq 'A5'}selected{/if}>A5</option>
			<option value="B5" {if $media eq 'B5'}selected{/if}>B5</option>
			<option value="Folio" {if $media eq 'Folio'}selected{/if}>Folio</option>
			<option value="A6" {if $media eq 'A6'}selected{/if}>A6</option>
			<option value="A7" {if $media eq 'A7'}selected{/if}>A7</option>
			<option value="A8" {if $media eq 'A8'}selected{/if}>A8</option>
			<option value="A9" {if $media eq 'A9'}selected{/if}>A9</option>
			<option value="A10" {if $media eq 'A10'}selected{/if}>A10</option>
			<!--end php predefined media options if used-->
			</select>
			</td></tr>
			<tr><td class="form">{tr}Keep screen pixel/point ratio{/tr}:</td><td class="form"><input class="nulinp" type="checkbox" name="scalepoints" value="1" {if $scalepoints eq '1'}checked="checked"{/if} id="scalepoint"/></td></tr>
			<tr><td class="form">{tr}Render images{/tr}:</td><td class="form"><input class="nulinp" type="checkbox" name="renderimages" value="1" {if $renderimages eq '1'}checked="checked"{/if} id="renderi"/></td></tr>
			<tr><td class="form">{tr}Render hyperlinks{/tr}:</td><td class="form"><input class="nulinp" type="checkbox" name="renderlinks" value="1" {if $renderlinks eq '1'}checked="checked"{/if} id="renderi"/></td></tr>
			<tr><td class="form">{tr}Left Margin (mm){/tr}:</td><td class="form"><input id="lm" type="text" size="3" name="leftmargin" value="{$leftmargin|escape}"/></td></tr>
			<tr><td class="form">{tr}Right Margin (mm){/tr}:</td><td class="form"><input id="rm" type="text" size="3" name="rightmargin" value="{$rightmargin|escape}"/></td></tr>
			<tr><td class="form">{tr}Top Margin (mm){/tr}:</td><td class="form"><input id="tm" type="text" size="3" name="topmargin" value="{$topmargin|escape}"/></td></tr>
			<tr><td class="form">{tr}Bottom Margin (mm){/tr}:</td><td class="form"><input id="bm" type="text" size="3" name="bottommargin" value="{$bottommargin|escape}"/></td></tr>

			
			<tr><td align="center" colspan="2" class="form"><input type="submit" name="send" value="{tr}send{/tr}" /></td></tr>
		</table>
		</td>
		<td>
			<table>
				<tr><td class="form">{tr}Landscape{/tr}:</td><td class="form"><input id="landsc" class="nulinp" type="checkbox" name="landscape" value="1" {if $landscape eq '1'}checked="checked"{/if}/></td></tr>
				<tr><td class="form">{tr}Show page border{/tr}:</td><td class="form"><input id="landsc" class="nulinp" type="checkbox" name="pageborder" value="1" {if $pageborder eq '1'}checked="checked"{/if}/></td></tr>
				<tr><td class="form">{tr}Encoding{/tr}:</td><td class="form">
					<select id="encod" name="encoding">
						<option value="" selected="selected">Autodetect</option>
						<option value="utf-8" {if $encoding eq 'utf-8'}selected{/if}>utf-8</option>
						<option value="iso-8859-1" {if $encoding eq 'iso-8859-1'}selected{/if}>iso-8859-1</option>
						<option value="iso-8859-2" {if $encoding eq 'iso-8859-2'}selected{/if}>iso-8859-2</option>
						<option value="iso-8859-3" {if $encoding eq 'iso-8859-3'}selected{/if}>iso-8859-3</option>
						<option value="iso-8859-4" {if $encoding eq 'iso-8859-4'}selected{/if}>iso-8859-4</option>
						<option value="iso-8859-5" {if $encoding eq 'iso-8859-5'}selected{/if}>iso-8859-5</option>
						<option value="iso-8859-7" {if $encoding eq 'iso-8859-7'}selected{/if}>iso-8859-7</option>
						<option value="iso-8859-9" {if $encoding eq 'iso-8859-9'}selected{/if}>iso-8859-9</option>
						<option value="iso-8859-10" {if $encoding eq 'iso-8859-10'}selected{/if}>iso-8859-10</option>
						<option value="iso-8859-11" {if $encoding eq 'iso-8859-11'}selected{/if}>iso-8859-11</option>
						<option value="iso-8859-13" {if $encoding eq 'iso-8859-13'}selected{/if}>iso-8859-13</option>
						<option value="iso-8859-14" {if $encoding eq 'iso-8859-14'}selected{/if}>iso-8859-14</option>
						<option value="iso-8859-15" {if $encoding eq 'iso-8859-15'}selected{/if}>iso-8859-15</option>
						<option value="windows-1250" {if $encoding eq 'windows-1250'}selected{/if}>windows-1250</option>
						<option value="windows-1251" {if $encoding eq 'windows-1251'}selected{/if}>windows-1251</option>
						<option value="windows-1252" {if $encoding eq 'windows-1252'}selected{/if}>windows-1252</option>
						<option value="koi8-r" {if $encoding eq 'koi8-r'}selected{/if}>koi8-r</option>
					</select>
				</td></tr>
				<tr><td class="form">{tr}Output{/tr}:</td><td class="form">
					<br/><input class="nulinp" type="radio" id="ps" name="method" value="fastps" {if $method eq 'fastps'}checked{/if}/>PostScript<br /><input class="nulinp" type="radio" id="pdf" name="method" value="pdflib" {if $method eq 'pdflib'}checked{/if}/>PDF (PDFLIB)<br /><input class="nulinp" type="radio" id="pdf" name="method" value="fpdf" {if $method eq 'fpdf'}checked{/if}/>PDF (FPDF)
				</td></tr>
				<tr><td class="form">{tr}PDF compatilbility level{/tr}</td><td class="form">
					<select name="pdfversion">
						<option value="1.2" {if $pdfversion eq '1.2'}selected{/if}>PDF 1.2 {tr}(NOT supported by PDFLIB!){/tr}</b></option>
						<option value="1.3" {if $pdfversion eq '1.3'}selected{/if}>PDF 1.3 (Acrobat Reader 4)</option>
						<option value="1.4" {if $pdfversion eq '1.4'}selected{/if}>PDF 1.4 (Acrobat Reader 5)</option>
						<option value="1.5" {if $pdfversion eq '1.5'}selected{/if}>PDF 1.5 (Acrobat Reader 6)</option>
					</select>
				</td></tr>
			</table>
		</td>
	</tr>
</table>
</form>
</div>
</div>

<div class="cbox">
<div class="cbox-title">
{tr}Select Wiki Pages{/tr}
</div>
<div class="cbox-data">
<form action="tiki-config_pdf.php{if $page_ref_id ge '-1'}?page_ref_id={$page_ref_id}{/if}" method="post">
<input type="hidden" name="convertpages" value="{$form_convertpages|escape}" />
<input type="hidden" name="media" value="{$media|escape}" />
<input type="hidden" name="scalepoints" value="{$scalepoints|escape}" />
<input type="hidden" name="renderimages" value="{$renderimages|escape}" />
<input type="hidden" name="renderlinks" value="{$renderlinks|escape}" />
<input type="hidden" name="leftmargin" value="{$leftmargin|escape}" />
<input type="hidden" name="rightmargin" value="{$rightmargin|escape}" />
<input type="hidden" name="topmargin" value="{$topmargin|escape}" />
<input type="hidden" name="bottommargin" value="{$bottommargin|escape}" />
<input type="hidden" name="landscape" value="{$landscape|escape}" />
<input type="hidden" name="pageborder" value="{$pageborder|escape}" />
<input type="hidden" name="encoding" value="{$encoding|escape}" />
<input type="hidden" name="method" value="{$method|escape}" />
<input type="hidden" name="pdfversion" value="{$pdfversion|escape}" />
<input type="text" name="find" value="{$find|escape}" /><input type="submit" name="filter" value="{tr}filter{/tr}" /><br />
</form>
<br />
<form action="tiki-config_pdf.php{if $page_ref_id ge '-1'}?page_ref_id={$page_ref_id}{/if}" method="post">
<input type="hidden" name="convertpages" value="{$form_convertpages|escape}" />
<input type="hidden" name="media" value="{$media|escape}" />
<input type="hidden" name="scalepoints" value="{$scalepoints|escape}" />
<input type="hidden" name="renderimages" value="{$renderimages|escape}" />
<input type="hidden" name="renderlinks" value="{$renderlinks|escape}" />
<input type="hidden" name="leftmargin" value="{$leftmargin|escape}" />
<input type="hidden" name="rightmargin" value="{$rightmargin|escape}" />
<input type="hidden" name="topmargin" value="{$topmargin|escape}" />
<input type="hidden" name="bottommargin" value="{$bottommargin|escape}" />
<input type="hidden" name="landscape" value="{$landscape|escape}" />
<input type="hidden" name="pageborder" value="{$pageborder|escape}" />
<input type="hidden" name="encoding" value="{$encoding|escape}" />
<input type="hidden" name="method" value="{$method|escape}" />
<input type="hidden" name="pdfversion" value="{$pdfversion|escape}" />
<table class="normal">
<tr><td class="normal" align="center">
<select name="addpageName[]" size="10" multiple="multiple" style="min-width:150px;_width:150x;">
{section name=ix loop=$pages}
<option value="{$pages[ix].pageName|escape}">{$pages[ix].pageName|truncate:60:"..."}</option>
{/section}
</select>
</td><td class="normal" align="center">
<input type="submit" name="addpage" value="{tr}add page{/tr} ---&gt;" /><br />
<input type="submit" name="rempage" value="&lt;--- {tr}remove page{/tr}" /><br />
<input type="submit" name="clearpages" value="{tr}reset{/tr}" />
</td><td class="normal" align="center">
<select name="rempageName[]" size="10" multiple="multiple" style="min-width:150px;_width:150x;">
{foreach from=$convertpages item=ix}
<option value="{$ix|escape}">{$ix}</option>
{/foreach}
</select>
</td></tr>
</table>
</form>
</div>
</div>

<div class="cbox">
<div class="cbox-data" align="center">
<form action="tiki-export_pdf.php{if $page_ref_id ge '-1'}?page_ref_id={$page_ref_id}{/if}" method="post">
<input type="hidden" name="convertpages" value="{$form_convertpages|escape}" />
<input type="hidden" name="media" value="{$media|escape}" />
<input type="hidden" name="scalepoints" value="{$scalepoints|escape}" />
<input type="hidden" name="renderimages" value="{$renderimages|escape}" />
<input type="hidden" name="renderlinks" value="{$renderlinks|escape}" />
<input type="hidden" name="leftmargin" value="{$leftmargin|escape}" />
<input type="hidden" name="rightmargin" value="{$rightmargin|escape}" />
<input type="hidden" name="topmargin" value="{$topmargin|escape}" />
<input type="hidden" name="bottommargin" value="{$bottommargin|escape}" />
<input type="hidden" name="landscape" value="{$landscape|escape}" />
<input type="hidden" name="pageborder" value="{$pageborder|escape}" />
<input type="hidden" name="encoding" value="{$encoding|escape}" />
<input type="hidden" name="method" value="{$method|escape}" />
<input type="hidden" name="pdfversion" value="{$pdfversion|escape}" />

<input type="submit" name="create" value="{tr}Create PDF{/tr}" />
</form>
</div>
</div>

<br />
