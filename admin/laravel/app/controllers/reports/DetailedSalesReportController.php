<?php

require_once(app_path().'/includes/includes.php');
require_once(app_path().'/tools/Exports.php');

class DetailedSalesReportController extends BaseController
{
		public function __construct()
		{
				$this->beforeFilter('validatePermission:View Reports Module');
				$this->formatters = array(
					'Unit Price'     => \NumberFormatter::PATTERN_DECIMAL,
					'Quantity'       => \NumberFormatter::PATTERN_DECIMAL,
					'Total Discount' => \NumberFormatter::PATTERN_DECIMAL,
					'Tax Percent'    => \NumberFormatter::PATTERN_DECIMAL,
					'Check Total'    => \NumberFormatter::PATTERN_DECIMAL,
					);
		}

		protected function localizeData($report, $formatters)
		{				
			if(!extension_loaded('intl')) return $report;
			
			$settings = CS_API::getSettingsFor('MainEngine');
			$currentCulture = isset($settings->{'settings'}->{'CurrentCulture'}) ? $settings->{'settings'}->{'CurrentCulture'}->{'SettingValue'} : 'en-US';
			
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
        $end = Input::get('end');
        $show_by_opened_date = Input::get('show_by_opened_date');

        $report = CS_API::getReport_DetailedSales($start,$end,$show_by_opened_date);

        Session::put('mostRecentReport_DetailedSales',$report);

        return View::make('/screens/reports/detailed-sales',
            array('controller' => 'ReportsController',
                  'report' => $this->localizeData($report, $this->formatters),
                  'start' => $start,
                  'end' => $end,
                  'show_by_opened_date' => $show_by_opened_date
            ));
    }

    public function exportToCSV()
    {
        if (!Session::has('mostRecentReport_DetailedSales'))
        {
            return Redirect::to('/reports/detailed-sales');
        }

        $dataToExport = Session::get('mostRecentReport_DetailedSales');

        Exports::toCSV(localizeData($dataToExport, $this->formatters), 'Detailed Sales Report');

    }

}