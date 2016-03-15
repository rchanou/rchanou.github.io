<?php

require_once(app_path().'/includes/includes.php');

class EMVReportController extends BaseController
{
		public function __construct()
		{
				$this->beforeFilter('validatePermission:View Reports Module');
		}

    public function configuration($terminal)
    {
				$settings = $this->getEMVSettings($terminal);
				$report = null;
				$missingSettings = true;
				$missingReport = true;
			  if (isset($settings['host']) && isset($settings['port'])) {
					$missingSettings = false;

					$report = $this->getConfigurationReport($settings['host'], $settings['port']);
					$report = htmlspecialchars($report);
				}
				if (isset($report) && !empty($report)) {
					$missingReport = false;
				}

        return View::make('/screens/reports/emv-configuration',
            array(
							'controller' => 'ReportsController',
							'missingSettings' => $missingSettings,
							'missingReport' => $missingReport,
							'terminal' => strtoupper($terminal),
							'report' => $report
            )
				);
    }

		public function chip($terminal)
		{
			$settings = $this->getEMVSettings($terminal);
			$report = null;
			$missingSettings = true;
			$missingReport = true;
			if (isset($settings['host']) && isset($settings['port'])) {
				$missingSettings = false;

				$report = $this->getChipReport($settings['host'], $settings['port']);
				$report = htmlspecialchars($report);
			}
			if (isset($report) && !empty($report)) {
				$missingReport = false;
			}

			return View::make('/screens/reports/emv-chip',
				array(
					'controller' => 'ReportsController',
					'missingSettings' => $missingSettings,
					'missingReport' => $missingReport,
					'terminal' => strtoupper($terminal),
					'report' => $report
				)
			);
		}

		private function getEMVSettings($terminal) {
			$settings = array();
			$host = CS_API::getJSON('settings/get',
				array('group' => $terminal,
					    'setting' => 'ExternalPaymentTerminalHost' ));
			if (isset($host->settings->ExternalPaymentTerminalHost)) {
				if (!empty($host->settings->ExternalPaymentTerminalHost->SettingValue)) {
					$settings['host'] = $host->settings->ExternalPaymentTerminalHost->SettingValue;
				}
				else {
					if (!empty($host->settings->ExternalPaymentTerminalHost->DefaultSetting)) {
						$settings['host'] = $host->settings->ExternalPaymentTerminalHost->DefaultSetting;
					}
					else {
						$settings['host'] = null;
					}
				}
			}

			$port = CS_API::getJSON('settings/get',
				array('group' => $terminal,
					'setting' => 'ExternalPaymentTerminalPort' ));
			if (isset($port->settings->ExternalPaymentTerminalPort)) {
				if (!empty($port->settings->ExternalPaymentTerminalPort->SettingValue)) {
					$settings['port'] = $port->settings->ExternalPaymentTerminalPort->SettingValue;
				}
				else {
					if (!empty($port->settings->ExternalPaymentTerminalPort->DefaultSetting)) {
						$settings['port'] = $port->settings->ExternalPaymentTerminalPort->DefaultSetting;
					}
					else {
						$settings['port'] = null;
					}
				}
			}

			return $settings;
		}

		private function getConfigurationReport($host, $port) {
			$url = $host . ':' . $port;
			$body = '<TRANSACTION>
							<TRANSACTIONTYPE>INTERACTIVEGETEMVCONFIG</TRANSACTIONTYPE>
							<SERVICENAME>CARD-3</SERVICENAME>
							</TRANSACTION>';
			$report = $this->postXml($url,$body);
			return $report;
		}

		private function getChipReport($host, $port) {
			$url = $host . ':' . $port;
			$body = '<TRANSACTION>
							<TRANSACTIONTYPE>CCCHIPREPORT</TRANSACTIONTYPE>
							<CCSYSTEMCODE>340048</CCSYSTEMCODE>
							</TRANSACTION>';
			$report = $this->postXml($url,$body);
			return $report;
		}

		private function postXml($url, $body) {
			set_time_limit(65); // To prevent Laravel error
			try {
				$response = \Httpful\Request::post($url)
					->body($body)
					->sendsXml()
					->timeoutIn(60)
					->send();

				if (isset($response->body) && $response->code == 200) {
					return $response->body;
				}
				else
				{
					return null;
				}
			}
			catch (Exception $e)
			{
				return null;
			}
		}
}
