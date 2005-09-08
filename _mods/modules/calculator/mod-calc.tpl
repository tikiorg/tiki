{* $Header: /cvsroot/tikiwiki/_mods/modules/calculator/mod-calc.tpl,v 1.4 2005-09-08 10:45:39 damosoft Exp $ 

   TikiWiki calculator in a module

*}
{tikimodule title="{tr}Calculator{/tr}" name="calculator" flip=$module_params.flip decorations=$module_params.decorations}
<div align="center">
<form name="calc">
<table><tr><td><input type="text" name="input" Size="16" />
<br />
</td></tr>
<tr><td><input type="button" name="one" value="1" OnClick="calc.input.value += '1'" />
<input type="button" name="two"   value="2" OnCLick="calc.input.value += '2'" />
<input type="button" name="three" value="3" OnClick="calc.input.value += '3'" />
<input type="button" name="plus"  value="+" OnClick="calc.input.value += ' + '" />
<br />
<input type="button" name="four"  value="4" OnClick="calc.input.value += '4'" />
<input type="button" name="five"  value="5" OnCLick="calc.input.value += '5'" />
<input type="button" name="six"   value="6" OnClick="calc.input.value += '6'" />
<input type="button" name="minus" value="-" OnClick="calc.input.value += ' - '" />
<br />
<input type="button" name="seven" value="7" OnClick="calc.input.value += '7'" />
<input type="button" name="eight" value="8" OnCLick="calc.input.value += '8'" />
<input type="button" name="nine"  value="9" OnClick="calc.input.value += '9'" />
<input type="button" name="times" value="x" OnClick="calc.input.value += ' * '" />
<br />
<input type="button" name="clear" value="c" OnClick="calc.input.value = ''" />
<input type="button" name="zero"  value="0" OnClick="calc.input.value += '0'" />
<input type="button" name="DoIt"  value="=" OnClick="calc.input.value = eval(calc.input.value)" />
<input type="button" name="div"   value="/" OnClick="calc.Input.value += ' / '" />
<br /></td></tr></table></form></div>
{/tikimodule}
