<?php

require_once(app_path().'/includes/includes.php');
require_once(app_path().'/tools/Exports.php');

class GiftCardsController extends BaseController
{

		public function __construct()
		{
				$this->beforeFilter('validatePermission:View Reports Module');
		}

    public function index()
    {
        return View::make('/screens/giftcards/manage',array(
            'controller' => 'GiftCardsController'
        ));
    }

    public function updateBalance()
    {
        $input = Input::all();

        //Begin data validation
        $rules = array(
            'giftCardsToUpdate' => 'required|no_enters',
            'newPointsBalance' => 'integer|min:0',
            'newCashBalance' => 'numeric|min:0',
            'notes' => 'max:400'
        );
        $messages = array(
            'giftCardsToUpdate.required' => 'Please enter a list of gift card numbers to update.',
            'giftCardsToUpdate.no_enters' => 'Please do not include newlines / enters in the list of gift card numbers.',
            'newPointsBalance.integer' => 'Please enter a valid whole number for the new points balance.',
            'newPointsBalance.min' => 'Please enter a valid positive number for the new points balance.',
            'newCashBalance.numeric' => 'Please enter a valid number for the new cash balance.',
            'newCashBalance.min' => 'Please enter a valid positive number for the new cash balance.',
            'notes.max' => 'The notes field has a maximum length of 400 characters.'
        );

        Validator::extend('no_enters', function($attribute, $value, $parameters)
        {
            // Banned characters
            $banned_characters = array("\r", "\n", "\r\n");
            foreach ($banned_characters as $character)
            {
                if (stripos($value, $character) !== false) return false;
            }
            return true;
        });

        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            return Redirect::to('/giftcards/manage')->withErrors($validator)->withInput();
        } //End data validation

        $giftCardsList = Input::get('giftCardsToUpdate');
        $giftCardsList = trim($giftCardsList);
        $newPointsBalance = Input::get('newPointsBalance');
        $newCashBalance = Input::get('newCashBalance');
        $notes = Input::get('notes');

        if ($newPointsBalance == '' && $newCashBalance == '')
        {
            return Redirect::to('/giftcards/manage')->with( array('error' => 'You must enter at least a new Points balance or a new Cash balance. You may also enter both.'))->withInput();
        }

        $params = array();
        $params['cards'] = $giftCardsList;
        if ($newPointsBalance !== null && $newPointsBalance !== '') { $params['points'] = $newPointsBalance; }
        if ($newCashBalance !== null && $newCashBalance !== '') { $params['money'] = $newCashBalance; }
        if ($notes !== null && $notes !== '') { $params['notes'] = $notes; }

        $result = CS_API::updateGiftCardBalances($params);

        if ($result === false)
        {
            return Redirect::to('/giftcards/manage')->with( array('error' => 'One or more gift card balances could not be updated. Please try again later. It may help to reduce the number of gift cards being processed simultaneously.'))->withInput();
        }
        else if ($result === null)
        {
            return Redirect::to('/disconnected');
        }

        return Redirect::to('giftcards/manage')->with( array('message' => 'Gift card balances updated successfully!'));
    }

    public function reports()
    {
        return View::make('/screens/giftcards/reports/index',array(
            'controller' => 'GiftCardsController'
        ));
    }

    public function balanceReport()
    {
        return View::make('/screens/giftcards/reports/balance',array(
            'controller' => 'GiftCardsController',
            'report' => array()
        ));
    }

    public function getBalanceReport()
    {

        $input = Input::all();

        //Begin data validation
        $rules = array(
            'listOfGiftCards' => 'required|no_enters'
        );
        $messages = array(
            'listOfGiftCards.required' => 'Please enter the desired list of gift card numbers below.',
            'listOfGiftCards.no_enters' => 'Please do not include newlines / enters in the list of gift card numbers.'
        );

        Validator::extend('no_enters', function($attribute, $value, $parameters)
        {
            // Banned characters
            $banned_characters = array("\r", "\n", "\r\n");
            foreach ($banned_characters as $character)
            {
                if (stripos($value, $character) !== false) return false;
            }
            return true;
        });

        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            Session::forget('mostRecentReport_GiftCard_Balance');
            return Redirect::to('/giftcards/reports/balance')->withErrors($validator)->withInput();
        } //End data validation

        $listOfGiftCards = Input::get('listOfGiftCards');
        $listOfGiftCards = preg_replace('/\s+/', '', $listOfGiftCards);
        $result = CS_API::getGiftCardBalances($listOfGiftCards);
        if (is_string($result) && strpos($result,'Allowed memory size') !== false)
        {
            return Redirect::to('/giftcards/reports/balance')->with( array('error' => 'There was a problem generating the report due to the number of gift cards requested. Please try again with a smaller range.'))->withInput();
        }
        if (!is_array($result))
        {
            $result = false;
        }
        if (isset($result) && is_array($result) && count($result) > 0)
        {
            $result = $this->formatBalanceReport($result);

            Session::put('mostRecentReport_GiftCard_Balance',$result);
        }
        else
        {
            Session::forget('mostRecentReport_GiftCard_Balance');
        }

        if ($result === false)
        {
            return Redirect::to('/giftcards/reports/balance')->with( array('error' => 'There was a problem generating the report. Please try again. It may help to reduce the number of gift cards being processed.'))->withInput();
        }
        else if ($result === null)
        {
            return Redirect::to('/disconnected');
        }

        $message = null;
        if (count($result) > 0)
        {
            $message = 'Report generation complete!';
        }
        else {
            $message = 'Report generation complete, but there was no data for the card numbers entered.';
        }

        Input::flash();

        return View::make('/screens/giftcards/reports/balance',array(
            'controller' => 'GiftCardsController',
            'report' => $result,
            'message' => $message
        ));
    }

    private function formatBalanceReport($listOfGiftCards)
    {
        $report = array();
        $headerRow = array('Card ID', 'Money Balance', 'Points Balance', 'Assigned To Member?');
        foreach($listOfGiftCards as $currentGiftCard)
        {
            $report[] = array(
                $headerRow[0] => $currentGiftCard->cardId,
                $headerRow[1] => number_format($currentGiftCard->money,2,'.',''),
                $headerRow[2] => $currentGiftCard->points,
                $headerRow[3] => $currentGiftCard->isGiftCard ? 'false' : 'true'
            );
        }

        usort($report,array('GiftCardsController','sortByCardID'));

        return $report;
    }

    private function sortByCardID($a,$b)
    {
        if ($a['Card ID'] == $b['Card ID']) {
            return 0;
        }
        return ($a['Card ID'] < $b['Card ID']) ? -1 : 1;
    }

    public function getBalanceReportCSV()
    {
        return $this->exportToCSV('mostRecentReport_GiftCard_Balance','/giftcards/reports/balance','Gift Card Balance Report');
    }

    public function transactionReport()
    {
        return View::make('/screens/giftcards/reports/transactions',array(
            'controller' => 'GiftCardsController',
            'report' => array()
        ));
    }

    public function getTransactionReport()
    {

        $input = Input::all();

        //Begin data validation
        $rules = array(
            'listOfGiftCards' => 'required|no_enters'
        );
        $messages = array(
            'listOfGiftCards.required' => 'Please enter the desired list of gift card numbers below.',
            'listOfGiftCards.no_enters' => 'Please do not include newlines / enters in the list of gift card numbers.'
        );

        Validator::extend('no_enters', function($attribute, $value, $parameters)
        {
            // Banned characters
            $banned_characters = array("\r", "\n", "\r\n");
            foreach ($banned_characters as $character)
            {
                if (stripos($value, $character) !== false) return false;
            }
            return true;
        });

        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            Session::forget('mostRecentReport_GiftCard_Transaction');
            return Redirect::to('/giftcards/reports/transactions')->withErrors($validator)->withInput();
        } //End data validation

        $listOfGiftCards = Input::get('listOfGiftCards');
        $listOfGiftCards = preg_replace('/\s+/', '', $listOfGiftCards);
        $result = CS_API::getGiftCardTransactions($listOfGiftCards);

        if (is_string($result) && strpos($result,'Allowed memory size') !== false)
        {
            return Redirect::to('/giftcards/reports/transactions')->with( array('error' => 'There was a problem generating the report due to the number of gift cards requested. Please try again with a smaller range.'))->withInput();
        }
        if (!is_array($result))
        {
            $result = false;
        }

        if (isset($result) && is_array($result) && count($result) > 0)
        {
            $result = $this->formatTransactionReport($result);

            Session::put('mostRecentReport_GiftCard_Transaction',$result);
        }
        else
        {
            Session::forget('mostRecentReport_GiftCard_Transaction');
        }

        if ($result === false)
        {
            return Redirect::to('/giftcards/reports/transactions')->with( array('error' => 'There was a problem generating the report. Please try again. It may help to reduce the number of gift cards being processed.'))->withInput();
        }
        else if ($result === null)
        {
            return Redirect::to('/disconnected');
        }

        $message = null;
        if (count($result) > 0)
        {
            $message = 'Report generation complete!';
        }
        else
        {
            $message = 'Report generation complete, but there was no data for the card numbers entered.';
        }

        Input::flash();

        return View::make('/screens/giftcards/reports/transactions',array(
            'controller' => 'GiftCardsController',
            'report' => $result,
            'message' => $message
        ));
    }

    private function formatTransactionReport($listOfGiftCards)
    {
        $report = array();
        $headerRow = array('Date', 'Card ID', 'Money Change', 'Points Change', 'Notes');
        foreach($listOfGiftCards as $currentGiftCard)
        {
            $report[] = array(
                $headerRow[0] => str_replace('T',' ',$currentGiftCard->date),
                $headerRow[1] => $currentGiftCard->cardId,
                $headerRow[2] => number_format($currentGiftCard->money,2,'.',''),
                $headerRow[3] => $currentGiftCard->points,
                $headerRow[4] => $currentGiftCard->notes,
            );
        }

        usort($report,array('GiftCardsController','sortByDateAndCardId'));

        return $report;
    }

    private function sortByDateAndCardId($a,$b)
    {
        if ($a['Date'] == $b['Date']) {
            return $this->sortByCardId($a,$b);
        }
        return ($a['Date'] < $b['Date']) ? -1 : 1;
    }

    public function getTransactionReportCSV()
    {
        return $this->exportToCSV('mostRecentReport_GiftCard_Transaction','/giftcards/reports/transactions','Transaction History Report');
    }

    public function exportToCSV($sessionDataName,$redirectToIfFailure,$filename)
    {
        if (!Session::has($sessionDataName))
        {
            return Redirect::to($redirectToIfFailure);
        }
        else
        {
            $dataToExport = Session::get($sessionDataName);
            Exports::toCSV($dataToExport,$filename);
        }
    }

}
