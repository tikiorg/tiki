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
// $Id: plot_access_statistics.php,v 1.2 2003-05-12 16:34:55 lechuckdapirate Exp $
//

require_once POT_INCLUDE_PATH . 'API/Plugin.php';

/**
* phpOpenTracker API - Plot Access Statistics
*
* @author   Sebastian Bergmann <sebastian@phpOpenTracker.de>
* @version  $Revision: 1.2 $
* @since    phpOpenTracker 1.0.0
*/
class phpOpenTracker_API_plot_access_statistics extends phpOpenTracker_API_Plugin {
  /**
  * API Calls
  *
  * @var array $apiCalls
  */
  var $apiCalls = array('access_statistics');

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
    $parameters['interval']    = isset($parameters['interval'])    ? $parameters['interval']    : false;
    $parameters['month_names'] = isset($parameters['month_names']) ? $parameters['month_names'] : false;

    if (!$parameters['month_names']) {
      $parameters['month_names'] = array(
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December'
      );
    }

    $timestamp = time();

    $steps = array(
      'hour'  =>     3600,
      'day'   =>    86400,
      'month' =>  2592000,
      'year'  => 31536000
    );

    $starttitle = '';
    $endtitle   = '';

    switch ($parameters['interval']) {
      case 'hour': {
        $starthour   = $hour = date('H', $parameters['start']);
        $endhour     =         date('H', $parameters['end']);
        $starttitle .= $starthour . ':00 ';
        $endtitle   .= $endhour   . ':00 ';
      }

      case 'day': {
        $startday    = $day = date('d', $parameters['start']);
        $endday      =        date('d', $parameters['end']);
        $starttitle .= $startday . '. ';
        $endtitle   .= $endday   . '. ';
      }

      case 'month': {
        $startmonth  = $month = date('m', $parameters['start']);
        $endmonth    =          date('m', $parameters['end']);
        $starttitle .= $parameters['month_names'][$startmonth-1] . ' ';
        $endtitle   .= $parameters['month_names'][$endmonth-1]   . ' ';
      }

      case 'year': {
        $startyear   = $year = date('Y', $parameters['start']);
        $endyear     =         date('Y', $parameters['end']);
        $starttitle .= $startyear;
        $endtitle   .= $endyear;
      }
    }

    $title = $starttitle . ' - ' . $endtitle;

    for ($start = $parameters['start']; $start < $parameters['end']; $start += $steps[$parameters['interval']]) {
      if ($parameters['interval'] == 'month') {
        $steps['month'] = $steps['day'] * date('t', $_start);
      }

      $end = $start + $steps[$parameters['interval']] - 1;

      if ($start <= $timestamp) {
        $apiCallParameters = array(
          'client_id'   => $parameters['client_id'],
          'start'       => $start,
          'end'         => $end,
          'constraints' => $parameters['constraints']
        );

        $y_pi[] = phpOpenTracker::get(
          array_merge(
            array(
              'api_call' => 'page_impressions'
            ),
            $apiCallParameters
          )
        );

        $y_visitors[] = phpOpenTracker::get(
          array_merge(
            array(
              'api_call' => 'visitors'
            ),
            $apiCallParameters
          )
        );
      } else {
        $y_pi[]       = 0;
        $y_visitors[] = 0;
      }

      switch ($parameters['interval']) {
        case 'hour': {
          $x_label[] = date('H', mktime($hour, 0, 0, $startmonth, $startday, $startyear)) . ':00';
          $hour++;
        }
        break;

        case 'day': {
          $x_label[] = date('d', mktime(0, 0, 0, $startmonth, $day, $startyear));
          $day++;
        }
        break;

        case 'month': {
          $x_label[] = date('m', mktime(0, 0, 0, $month, 1, $startyear));
          $month++;
        }
        break;

        case 'year': {
          $x_label[] = date('Y', mktime(0, 0, 0, 1, 1, $year));
          $year++;
        }
        break;
      }
    }

    if ($parameters['interval'] == 'hour') {
      $angle         = 50;
      $xasisFontSize = $parameters['font_size'] - 2;
    } else {
      $angle         = 0;
      $xasisFontSize = $parameters['font_size'];
    }

    $graph = new Graph($parameters['width'], $parameters['height'], 'auto');

    $graph->img->SetMargin(40, 40, 20, 40);
    $graph->SetScale('textlin');
    $graph->SetY2Scale('lin');
    $graph->SetShadow();

    $pi_plot = new LinePlot($y_pi);
    $pi_plot->SetColor($parameters['color1']);
    $pi_plot->SetLegend('Page Impressions');
    $pi_plot->SetWeight(2);

    $visitors_plot = new LinePlot($y_visitors);
    $visitors_plot->SetColor($parameters['color2']);
    $visitors_plot->SetLegend('Visitors');
    $visitors_plot->SetWeight(2);

    $graph->Add($pi_plot);
    $graph->AddY2($visitors_plot);

    $graph->xaxis->SetTickLabels($x_label);
    $graph->xaxis->SetLabelAngle($angle);
    $graph->xaxis->SetFont($parameters['font'], $parameters['font_style'], $xasisFontSize);
    $graph->xaxis->title->SetFont($parameters['font'], $parameters['font_style'], $parameters['font_size']);

    $graph->yaxis->SetColor('black');
    $graph->yaxis->SetFont($parameters['font'], $parameters['font_style'], $parameters['font_size']);
    $graph->yaxis->title->SetFont($parameters['font'], $parameters['font_style'], $parameters['font_size']);

    $graph->y2axis->SetColor('black');
    $graph->y2axis->SetFont($parameters['font'], $parameters['font_style'], $parameters['font_size']);
    $graph->y2axis->title->SetFont($parameters['font'], $parameters['font_style'], $parameters['font_size']);

    $graph->title->Set($title);
    $graph->title->SetFont($parameters['font'], $parameters['font_style'], $parameters['font_size']);

    $graph->Stroke();
  }
}

//
// "phpOpenTracker essenya, gul meletya;
//  Sebastian carneron PHP."
//
?>
