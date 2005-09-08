{* $Header: /cvsroot/tikiwiki/_mods/modules/extcalculator/mod-extcalc.tpl,v 1.1 2005-09-08 10:46:26 damosoft Exp $ 

   TikiWiki calculator in a module

   Ported to TikiWiki by mdavey
*}
{tikimodule title="{tr}Calculator{/tr}" name="calculator" flip=$module_params.flip decorations=$module_params.decorations}
<script language="JavaScript" type="text/javascript" src="lib/calc.js"></script>
<style>
{literal}
/* Calculator */
.calcTable {
	padding: 0;
	border: 0;
	margin: 0;
	width: 100%;
}
.calcBg {
	background-color: #EEEEEE;/*B3BDC6*/
	border: 1px solid #CCCCCC;
}
.calcResult {
	width: 100%;
	height: 25;
	background-color: #CCFFCC;
	border-top: 1px solid #999;
	border-left: 0px;
	border-right: 1px solid #FFF;
	border-bottom: 1px solid #FFF;
	font-size: 12;
	text-align: right;
}
.calcMem {
	width: 100%;
	height: 25;
	border-top: 1px solid #999;
	border-left: 1px solid #999;
	border-right: 0px;
	border-bottom: 1px solid #FFF;
	background-color: #CCFFCC;
	font-family: Arial;
	font-size: 10;
	color: #BBB;
	text-align: left;
}
.calcBlackBtn, .calcGreyBtn, .calcBigBtn, .calcCancBtn, .calcMemBtn, .calcBackBtn {
	border: none;
	width: 25;
	height: 20;
	font-size: 10px;
	color: #FFF;
}
{/literal}
</style>
<table class="calcTable" cellspacing="0" align="center">
  <tr>
    <td class="calcBg">
        <table border="0" cellpadding="2" cellspacing="2">
                <form name="calculator">
          <tr style="height:5">
            <td></td>
          </tr>
          <tr>
            <td colspan=6><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td width="10%"><input type="text" class="calcMem" name="mem" value="M" readonly></td>
                        <td width="90%"><input type="text" class="calcResult" name="answer" maxlength="30" onChange="CheckNumber(this.value)" readonly></td>
                      </tr>
                    </table></td>
                </tr>
              </table></td>
          </tr>
          <tr>
            <td colspan="3"></td>
            <td width="25">
 <input type="button" name="CE" class="calcCancBtn" value="CE" onClick="CECalc(); return false;">
            </td>
            <td width="25">
 <input type="reset" name="C" class="calcCancBtn" value="C" onClick="ClearCalc(); return false;">
            </td>
          </tr>
          <tr>
            <td width="25">
 <input type="button" name="backspace" class="calcBackBtn" value="" onClick="Backspace(document.calculator.answer.value); return false;"></td>
            <td width="25">
 <input type="button" name="recip" class="calcBlackBtn" value="1/x" onClick="RecipButton(); return false;"></td>
            <td width="25">
 <input type="button" name="sqrt" class="calcBlackBtn" value="sqrt" onClick="SqrtButton(); return false;"></td>
            <td width="25">
 <input type="button" name="negate" class="calcBlackBtn" value="+/-" onClick="NegateButton(); return false;"></td>
            <td width="25">
 <input type="button" name="percent" class="calcBlackBtn" value="%" onClick="PercentButton(); return false;"></td>
          </tr>
          <tr>
            <td width="25">
 <input type="button" name="MC" class="calcMemBtn" value="MC" onClick="MemoryClear(); return false;"></td>
            <td width="25">
 <input type="button" name="calc7" class="calcGreyBtn" value="7" onClick="CheckNumber('7'); return false;"></td>
            <td width="25">
 <input type="button" name="calc8" class="calcGreyBtn" value="8" onClick="CheckNumber('8'); return false;"></td>
            <td width="25">
 <input type="button" name="calc9" class="calcGreyBtn" value="9" onClick="CheckNumber('9'); return false;"></td>
            <td width="25">
 <input type="button" name="divide" class="calcBlackBtn" value="/" onClick="DivButton(1); return false;"></td>
          </tr>
          <tr>
            <td width="25">
 <input type="button" name="MR" class="calcMemBtn" value="MR" onClick="MemoryRecall(Memory); return false;"></td>
            <td width="25">
 <input type="button" name="calc4" class="calcGreyBtn" value="4" onClick="CheckNumber('4'); return false;"></td>
            <td width="25">
 <input type="button" name="calc5" class="calcGreyBtn" value="5" onClick="CheckNumber('5'); return false;"></td>
            <td width="25">
 <input type="button" name="calc6" class="calcGreyBtn" value="6" onClick="CheckNumber('6'); return false;"></td>
            <td width="25">
 <input type="button" name="multiply" class="calcBlackBtn" value="x" onClick="MultButton(1); return false;"></td>
          </tr>
          <tr>
            <td width="25">
 <input type="button" name="MS" class="calcMemBtn" value="M-" onClick="MemorySubtract(document.calculator.answer.value); return false;"></td>
            <td width="25">
 <input type="button" name="calc1" class="calcGreyBtn" value="1" onClick="CheckNumber('1'); return false;"></td>
            <td width="25">
 <input type="button" name="calc2" class="calcGreyBtn" value="2" onClick="CheckNumber('2'); return false;"></td>
            <td width="25">
 <input type="button" name="calc3" class="calcGreyBtn" value="3" onClick="CheckNumber('3'); return false;"></td>
            <td width="25">
 <input type="button" name="minus" class="calcBlackBtn" value="-" onClick="SubButton(1); return false;"></td>
          </tr>
          <tr>
            <td width="25">
 <input type="button" name="Mplus" class="calcMemBtn" value="M+" onClick="MemoryAdd(document.calculator.answer.value); return false;"></td>
            <td width="25">
 <input type="button" name="calc0" class="calcGreyBtn" value="0" onClick="CheckNumber('0'); return false;"></td>
            <td width="25">
 <input type="button" name="dot" class="calcGreyBtn" value="." onClick="CheckNumber('.'); return false;"></td>
            <td width="25">
 <input type="button" name="equal" class="calcBlackBtn" value="=" onClick="EqualButton(0); return false;"></td>
            <td width="25">
 <input type="button" name="plus" class="calcBlackBtn" value="+" onClick="AddButton(1); return false;"></td>
          </tr>
                   </form>
        </table>
     </td>
  </tr>
  </table>
{/tikimodule}
