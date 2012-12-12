<div class="dirfooter">
  <table>
    <tr>
      <td>{tr}Total directory categories:{/tr} {$stats.categs}</td>
      <td>{tr}Total links:{/tr} {$stats.valid}</td>
      <td>{tr}Links to validate:{/tr} {$stats.invalid}</td>
    </tr>
    <tr>
      <td>{tr}Searches performed:{/tr} {$stats.searches|default:'0'}</td>
      <td>{tr}Total links visited:{/tr} {$stats.visits|default:'0'}</td>
      <td>&nbsp;</td>
    </tr>
  </table>
</div>
