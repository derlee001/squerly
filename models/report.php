<?php
/**
  *
  * Squerly - Report main class
  * 
  * Report is the main 'report' class in Squerly that all report sub-classes are instantiated from through the 'deletgate' factory method
  * 
  * If you are interfacing with reports in Squerly, this class is the one you should be using. All 'reports' share the same data structure
  * but each 'type' of report has it's on PHP class based on it's default data source i.e. SQL database, XML file, CSV file, etc.
  * 
  * @author Eric Perez <ericperez@squerly.net>
  * @copyright (c)2012 Squerly contributors (Eric Perez, et. al.)
  * @license GNU General Public License, version 3 or later
  * @license http://opensource.org/licenses/gpl-3.0.html
  * @link http://www.squerly.net
  *
  */
class Report extends Report_Base {
  public $sub_class;

  //TODO: set the 'created at' and 'edited at' report fields
  public function beforeSave() {

  }

 /**
  *
  * Sets the report 'sub-class' property for use by the report factory/'delegate' method
  * 
  * This method is run automatically by Axon after a report instance is loaded from the database
  *
  * @todo Clean this up
  *
  */
  public function afterLoad() {
    $type = isset($this->type) && !empty($this->type) ? $this->type : 'sql'; //Defaults to SQL-based report
    $sub_class = String::machine('report_' . $type, true);
    //$sub_class_file = String::machine($sub_class);
    if(@class_exists($sub_class) && @is_subclass_of($sub_class, 'Report_Base')) {
      $this->sub_class = $sub_class;
    } else {
      F3::error('', "Report class {$sub_class} not found.");
    }
  }


 /**
  *
  * Report Factory
  * 
  * This method uses the 'sub-class' property of the report to determine 
  *   which class to use and returns an instance of it
  *
  * @return object Instance of report sub-class
  *
  */
  public static function delegate($id) {
    $report = new self();
    $report->load("id = {$id}");
    if($report->dry()) { 
      Notify::error("Report {$id} does not exist.");
      F3::reroute(F3::get('URL_BASE_PATH') . 'report'); 
      exit; 
    }
    $report_sub_class = new $report->sub_class();
    return $report_sub_class->load("id = {$id}");
  }



 /**
  *
  * Returns an array of 'actions' available for the report model (to be rendered on the Index page)
  *
  * @param int $id ID of report (to be plugged into the HTML returned)
  * @return array 'Title' => 'HTML markup'
  *
  */
  public static function getIndexActions($id) {
    return array(
      'Preview Results' => "
        <div title='This will not work for reports with required inputs'>
        <form action='/report/render/{$id}'>
        <input type='hidden' name='preview' value='1' />
        <select name='context'>
          <option value='table'>HTML Table</option>
          <option value='json'>JSON</option>
          <option value='highcharts'>Line Graph</option>
        </select>
        <input type='submit' value='Preview' />
        </form>
        </div>",
    );
  }

}
