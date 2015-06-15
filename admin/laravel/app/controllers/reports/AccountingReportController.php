<?php

require_once(app_path().'/includes/includes.php');
require_once(app_path().'/tools/Exports.php');

class AccountingReportController extends BaseController
{

		public function __construct()
		{
				$this->beforeFilter('validatePermission:View Reports Module');
		}

    public function index()
    {
        $start = Input::get('start');
				$end   = Input::get('end');
				
				// Handle updating settings (if we are POST'ing)
				if (Request::isMethod('post')) {
					$newSettings = array('fieldMappings' => Input::get('fieldMappings'));
					$accountingExportSettingsIds = Session::get('accountingExportSettingsIds',array());
					$result = CS_API::updateSettingsInNewTableFor('AccountingExport', $newSettings, $accountingExportSettingsIds);
	
					if ($result === false) {
							return Redirect::to('reports/accounting')->with( array('error' => 'One or more settings could not be updated. Please try again.'));
					} else if ($result === null) {
							return Redirect::to('/disconnected');
					}
				}
				
				// Retrieve settings from DB
				$accountingExportSettings     = CS_API::getSettingsFromNewTableFor('AccountingExport');
        $accountingExportSettingsData = array();
				if ($accountingExportSettings === null) {
            return Redirect::to('/disconnected');
        }
				foreach($accountingExportSettings->settings as $setting) {
            $accountingExportSettingsData[$setting->name] = $setting->value;
						$accountingExportSettingsIds[$setting->name]  = $setting->settingsId;
        }
				Session::put('accountingExportSettings', $accountingExportSettingsData);
        Session::put('accountingExportSettingsIds', $accountingExportSettingsIds);
				
				// Get form-based settings
				$start = empty($start) ? date('Y-m-d') : $start;
        $end   = empty($end)   ? date('Y-m-d') : $end;
				$data  = CS_API::getReport_Accounting($start, $end);
				$fieldMappings = $accountingExportSettingsData['fieldMappings'];

        $report = array('options' => array('start' => $start, 'end' => $end), 'data' => $data);

				// Format for view
				$total_debits = $total_credits = 0;
				foreach($data as $line) {
					//print_r($line);die();
					$total_debits  += $line->Debit;
					$total_credits += $line->Credit;
				}
				
				// Convert to Array (easier to work with than object)
				foreach($report['data'] as $key => $value) {
					$report['data'][$key] = (array) $value;
				}
				
				// Prepare replacements
				/*$replacement_mapping = <<<EOD
##CASH_PAYMENT##=555 Cash|test|this=hi
##ITEM_DISCOUNT##=11000 Item Discount|ClassName	
EOD;*/

				$replacements = array();
				foreach(preg_split('/$\R?^/m', $fieldMappings) as $key => $val) {
					$line = explode('=', $val, 2);
					if(count($line) === 2) {
						$replacements[trim($line[0])] = explode('|', trim($line[1]));
					}
				}
				
				// Perform replacement
				foreach($report['data'] as $key => $val) {
					if(array_key_exists($val['AccountNumber'], $replacements)) {
						$report['data'][$key]['AccountNumber'] = $replacements[$val['AccountNumber']][0];
						for($i = 1; $i < count($replacements[$val['AccountNumber']]); $i++) {
							if(strpos($replacements[$val['AccountNumber']][$i], '=') !== false) { // Contains an equals sign, specifying a key name
								$line = explode('=', $replacements[$val['AccountNumber']][$i], 2);
								$report['data'][$key][$line[0]] = $line[1];
							} else { // Give a default key name
								$report['data'][$key]['Option_' . $i] = $replacements[$val['AccountNumber']][$i];
							}
						}
					}
				}
				
				Session::put('mostRecentReport_Accounting', $report);

        return View::make('/screens/reports/accounting',
            array('controller'    => 'ReportsController',
                  'report'        => $report['data'],
                  'start'         => $start,
                  'end'           => $end,
                  'total_debits'  => $total_debits,
									'total_credits' => $total_credits,
									'fieldMappings' => $fieldMappings
            ));
    }

    public function exportToCSV()
    {
        if (!Session::has('mostRecentReport_Accounting'))
        {
            return Redirect::to('/reports/accounting');
        }

        $dataToExport = Session::get('mostRecentReport_Accounting');

        Exports::toCSV($dataToExport['data'], 'Accounting Export');

    }
		
		public function exportToIIF()
    {
        if (!Session::has('mostRecentReport_Accounting'))
        {
            return Redirect::to('/reports/accounting');
        }

        $dataToExport = Session::get('mostRecentReport_Accounting');

        Exports::toIIF($dataToExport, 'Accounting Export');

    }
		
		public function exportToSAGE()
    {
        if (!Session::has('mostRecentReport_Accounting'))
        {
            return Redirect::to('/reports/accounting');
        }

        $dataToExport = Session::get('mostRecentReport_Accounting');

        Exports::toSAGE($dataToExport, 'Accounting Export');

    }

}