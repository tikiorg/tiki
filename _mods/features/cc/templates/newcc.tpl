Create a new cc

<FORM method='post'>
<input type='hidden' name='page' value='createcc'>
<TABLE>

<TR><TD bgcolor='#CCCCCC'>cc id</TD>
    <TD><input type='text' name='id'></TD>
</TR>


<TR><TD bgcolor='#CCCCCC'>cc name</TD>
    <TD><input type='text' name='cc_name'></TD>
</TR>

<TR><TD bgcolor='#CCCCCC'>cc description</TD>
    <TD><textarea cols='40' rows='10' name='cc_description'></textarea>
    </TD>
</TR>

<TR><TD>Requires approval</TD>
    <TD><select name='requires_approval'>
        <option value='Y'>yes</option>
        <option value='N'>no</option>
        </select>
    </TD>
</TR>

<TR>
<TD colspan='2'><input type='submit' value='create cc'></TD>
</TR>
</TABLE>
</FORM>
