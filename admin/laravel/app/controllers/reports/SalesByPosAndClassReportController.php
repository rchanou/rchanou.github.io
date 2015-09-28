<?php

require_once(app_path().'/includes/includes.php');
require_once(app_path().'/tools/Exports.php');

class SalesByPosAndClassReportController extends BaseController
{

		public function __construct()
		{
				$this->beforeFilter('validatePermission:View Reports Module');
				$this->formatters = array(
					'Total Pre-tax'    => \NumberFormatter::PATTERN_DECIMAL,
					'Total Post-tax'    => \NumberFormatter::PATTERN_DECIMAL,
					);
		}
		
		protected function localizeData($report, $formatters)
		{				
			if(!extension_loaded('intl')) return $report;
			
			$settings = CS_API::getSettingsFor('MainEngine');
			$currentCulture = isset($settings->{'settings'}->{'CurrentCulture'}) ? $settings->{'settings'}->{'CurrentCulture'}->{'SettingValue'} : 'en-US';
			//$currentCulture = 'nl-NL';
			
			if($currentCulture === 'en-US' || empty($currentCulture)) return $report; // No need to format
			
			$formatter = new \NumberFormatter($currentCulture, \NumberFormatter::DECIMAL); 

			foreach($report as $key => $line) {
				foreach($line as $i => $cell) {
					if(array_key_exists($i, $formatters) && is_numeric($cell)) {
						$report[$key]->$i = $formatter->format($cell,  $formatters[$i]);

						/*$formatter = new \NumberFormatter("nl_NL", \NumberFormatter::CURRENCY); 
						$symbol = $formatter->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL);
						//echo $frontEndFormatter->formatCurrency($line->{'Unit Price'},  $symbol) . "<br>"; 
						$report[$key]->{'Unit Price'} = $formatter->formatCurrency($line->{'Unit Price'},  $symbol);*/

					}
				}
			}
			
			return $report;
		}

    public function index()
    {
        $start = Input::get('start');
        $end   = Input::get('end');

        $report = CS_API::getReport_SalesByPOSAndClass($start, $end);

        Session::put('mostRecentReport_SalesByPosAndClassReportController', $report);

        return View::make('/screens/reports/sales-by-pos-and-class',
            array('controller' => 'ReportsController',
                  'report'     => $this->localizeData($report, $this->formatters),
                  'start'      => $start,
                  'end'        => $end
            ));
    }

    public function exportToCSV()
    {
        if (!Session::has('mostRecentReport_SalesByPosAndClassReportController'))
        {
            return Redirect::to('/reports/sales-by-pos-and-class');
        }

        $report = Session::get('mostRecentReport_SalesByPosAndClassReportController');

        Exports::toCSV($this->localizeData($report, $this->formatters), 'Sales By POS and Class Report');

    }

}