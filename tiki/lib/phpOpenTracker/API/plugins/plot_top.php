<?php
//
// +---------------------------------------------------------------------+
// | phpOpenTracker - The Website Traffic and Visitor Analysis Solution  |
// +---------------------------------------------------------------------+
// | Copyright (c) 2000-2003 Sebastian Bergmann. All rights reserved.    |
// +---------------------------------------------------------------------+
// | This source file is subject to the phpOpenTracker Software License, |
// | Version 1.0, that is bundled with this package in the file LICENSE. |
// | If you did not receive a copy of this file, you may either read the |
// | license online at http://phpOpenTracker.de/license/1_0.txt, or send |
// | a note to license@phpOpenTracker.de, so we can mail you a copy.     |
// +---------------------------------------------------------------------+
// | Author: Sebastian Bergmann <sebastian@phpOpenTracker.de>            |
// +---------------------------------------------------------------------+
//
// $Id: plot_top.php,v 1.2 2003-05-12 16:35:01 lechuckdapirate Exp $
//

require_once POT_INCLUDE_PATH . 'API/Plugin.php';

/**
* phpOpenTracker API - Plot Top
*
* @author   Sebastian Bergmann <sebastian@phpOpenTracker.de>
* @version  $Revision: 1.2 $
* @since    phpOpenTracker 1.0.0
*/
class phpOpenTracker_API_plot_top extends phpOpenTracker_API_Plugin {
  /**
  * API Calls
  *
  * @var array $apiCalls
  */
  var $apiCalls = array('top');

  /**
  * API Type
  *
  * @var string $apiType
  */
  var $apiType = 'plot';

  /**
  * Runs the phpOpenTracker API call.
  *
  * @param  array $parameters
  * @return mixed
  * @access public
  */
  function run($parameters) {
    $parameters['api_call']      = 'top';
    $parameters['result_format'] = 'separate_result_arrays';

    list($names, $values, $percent, $total) = phpOpenTracker::get(
      $parameters
    );

    $title = 'Top ' . $parameters['limit'] . ' ';

    switch ($parameters['what']) {
      case 'document': {
        $title .= 'Pages';
      }
      break;

      case 'entry_document': {
        $title .= 'Entry Pages';
      }
      break;

      case 'exit_document': {
        $title .= 'Exit Pages';
      }
      break;

      case 'exit_target': {
        $title .= 'Exit Targets';
      }
      break;

      case 'host': {
        $title .= 'Hosts';
      }
      break;

      case 'operating_system': {
        $title .= 'Operating Systems';
      }
      break;

      case 'referer': {
        $title .= 'Referers';
      }
      break;

      case 'user_agent': {
        $title .= 'User Agents';
      }
      break;
    }

    $title .= " (Total: $total)";

    for ($i = 0, $numValues = sizeof($values); $i < $numValues; $i++) {
      $legend[$i] = sprintf(
        '%s (%s, %s%%%%)',

        $names[$i],
        $values[$i],
        $percent[$i]
      );
    }

    $graph = new PieGraph($parameters['width'], $parameters['height'], 'auto');
    $graph->SetShadow();

    $graph->title->Set($title);
    $graph->title->SetFont($parameters['font'], $parameters['font_style'], $parameters['font_size']);
    $graph->title->SetColor('black');
    $graph->legend->Pos(0.1, 0.2);

    $plot = new PiePlot3d($values);
    $plot->SetTheme('sand');
    $plot->SetCenter(0.4);
    $plot->SetAngle(30);
    $plot->value->SetFont($parameters['font'], $parameters['font_style'], $parameters['font_size'] - 2);
    $plot->SetLegends($legend);

    $graph->Add($plot);
    $graph->Stroke();
  }
}

//
// "phpOpenTracker essenya, gul meletya;
//  Sebastian carneron PHP."
//
?>
