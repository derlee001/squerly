<?php
/**
  *
  * Squerly - Saved Report Configuration Controller
  * 
  * This file contains all the additional routes and supporting code that is specific to saved report configurations
  * 
  * @author Eric Perez <ericperez@squerly.net>
  * @copyright (c)2012-2013 Squerly contributors (Eric Perez, et al.)
  * @license GNU General Public License, version 3 or later
  * @license http://opensource.org/licenses/gpl-3.0.html
  * @link http://www.squerly.net
  * 
  */


class Saved_Report_Controller extends Crud_Controller {

 /**
  *
  * AJAX action/method to output serialized form values for a given saved report configuration
  * 
  * @param int $id Saved Report Configuration ID to load
  * @return string Serailized representation of a report input form
  *
  */
  public static function getValues($id = null) {
    $id = $id ?: (int) F3::get('PARAMS.id') ?: null;
    if($id === null) { return; }
    $report_config = new Saved_Report('saved_report');
    $report_config_model = $report_config->load("id={$id}");
    if($report_config_model === false) { return; }
    echo $report_config_model->input_values ?: '';
    return;
  }

}

F3::route('GET ' . F3::get('URL_BASE_PATH') . 'saved_report/getvalues/@id', 'Saved_Report_Controller::getValues', 10);