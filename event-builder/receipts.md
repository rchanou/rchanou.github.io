# Receipt Templating Documentation
---
## Required Applications
1. ClubSpeed v15.5.5+
2. GriddingService (clubspeedapps\event-builder)

## Setup
The receipt templating system is included with the existing GriddingService, and no additional setup is required, provided that the GriddingService has already been installed and is started in Firedaemon. ClubSpeed internals will use the receipt templating service by default, starting at version `15.5.5`.

Receipt templating is also dependent on the `dbo.ControlPanel` setting for `MainEngine.EventGriddingServiceIpPort`, and this setting needs to be correctly pointed to the GriddingService's IP and Port.

## Basic Application Flow

1. ClubSpeed needs a receipt to be printed
2. ClubSpeed collects all relevant information for that receipt, including database records, control panel settings, and translations/resources
3. ClubSpeed sends a `POST` with that information to the receipt templating service
6. Receipt templating service builds a template for the receipt, following the necessary logic based on provided data and control panel settings
7. Receipt templating responds to the original `POST`, sending the template string back to ClubSpeed
8. ClubSpeed makes final template changes (inserting items such as pictures)
9. ClubSpeed prints the fully built template string

## Receipt Types
1. [Credit Card](#credit-card)
2. [Entitle Item](#entitle-item)
3. [Expense](#expense)
4. [Food Order](#food-order)
5. [Race Ticket](#race-ticket)
6. [Till Report](#till-report)
7. [Transaction Receipt](#transaction-receipt)

## Options and Resources
Receipt templates can be customized using control panel settings and translatable resources. All control panel settings can be found at `https://{track_name}.clubspeedtiming.com/sp_admin` under their corresponding section. All resources can be updated at `https://{track_name}.clubspeedtiming.com/resourceentry`. Each resource will have a default English string on which to fall back whenever the resource is not available. 

Note that whenever a resource contains a string template variable (such as `{0}`), the variable will be replaced by the corresponding value and can be used to control the placement of variables inside the resources.

### Credit Card
* **Resource Names**
	* SP_Intake.ReceiptHelperResources
		* `strAuthNo`: "Auth No. #"
		* `strCC`: "Credit Card"
		* `strCrdIssuer`: "to the card issuer agreement."
		* `strCustomer`: "Customer"
		* `strGratuity2`: "Gratuity"
		* `strIAgree`: "I agree to pay the above amount according"
		* `strNA`: "N/A"
		* `strPayment`: "Payment"
		* `strReceiptNo`: "Receipt Number"
		* `strRefNo`: "Ref No. #"
		* `strRefund`: "Refund"
		* `strTerminal`: "Terminal:"
		* `strTotal2`: "Total"
		* `strTroutD`: "TroutD. #"
		* `strVoided`: "VOIDED"
* **Control Panel Settings and Defaults**
	* Terminal Specific (pos1, off2, etc)
		* `UseESign`
			* Default -> `false`
			* If `true`, use ESignature pad and print the signature image
			* If `false`, print extra space for the customer to sign physically

### Entitle Item
* **Resource Names**
	* SP_Intake.ReceiptHelperResources
        * `strCustomer2`: "Customer:"
        * `strId`: "ID:"
        * `strLaps`: "Laps:"
        * `strMinutes2`: "Minutes:"
        * `strTime2`: "Time:"
        * `strType`: "Type:"

### Expense
* **Resource Names**
	* SP_Intake.ReceiptHelperResources
        * `strExpense2`: "EXPENSE"
        * `strShift`: "Shift:"
        * `strTerminal`: "Terminal:"

### Food Order
* **Resource Names**
	* SP_Intake.ReceiptHelperResources
        * `strCheckNo`: "Check #"
        * `strCustomer`: "Customer"
        * `strNA`: "N/A"
        * `strTerminal`: "Terminal"
        * `strUser`: "User"

### Race Ticket
* **Resource Names**
	* SP_Intake.ReceiptHelperResources
        * `strAge`: "Age"
        * `strBestLap`: "Best Lap"
        * `strCustomer`: "Customer"
        * `strDuration`: "Duration"
        * `strEventName`: "Event Name"
        * `strExperience`: "Experience"
        * `strGrid`: "Grid"
        * `strHeatNumber`: "Heat No."
        * `strLaps`: "Laps"
        * `strMinutes`: "Minutes"
        * `strNA`: "N/A"
        * `strNew`: "New"
        * `strPosition`: "Position"
        * `strRoundNo`: "Round No."
        * `strSessions`: "sessions"
        * `strTime`: "Time"
        * `strVenue`: "Venue"
        * `strWinBy`: "Win By"
* **Control Panel Settings and Defaults**
	* MainEngine
		* `RaceTicketLine1`
			* Default -> `""`
			* If non empty, then printed as the first line of the footer
		* `RaceTicketLine2`
			* Default -> `""`
			* If non empty, then printed as the second line of the footer
		* `RaceTicketLine3`
			* Default -> `""`
			* If non empty, then printed as the third line of the footer
		* `RaceTicketLine4`
			* Default -> `""`
			* If non empty, then printed as the fourth line of the footer
		* `NotShowScheduledTime`
			* Default -> `false`
			* If false, the print the scheduled time for the heat
		* `PrintGridOnRaceTicket`
			* Default -> `true`
			* If true, then print the customer's lineup position
		* `PrintAgeOnRaceTicket`
			* Default -> `true`
			* If true, then print the customer's age
		* `ShowHeatNo`
			* Default -> `true`
			* If true, then print the actual heat number
			* If false, then print the heat sequence number

### Till Report
* **Resource Names**
	* SP_Intake.ReceiptHelperResources
        * `strAmount`: "Amount"
        * `strCashInDrawer`: "Cash in Drawer"
        * `strShiftPayment`: "Shift  Payment Type"
        * `strTerminal`: "Terminal"
        * `strTillReport`: "TILL REPORT"

### Transaction Receipt
* **Resource Names**
	* Interfaces.Common
		* `strTaxes`: "Tax"
	* SP_Intake.ReceiptHelperResources
        * `strAccBalance`: "{0:C} Account Balance"
        * `strAccessCode`: "Your Access code is:"
        * `strBalance`: "Balance"
        * `strBalanceRemaining`: "{0:C} Balance remaining."
        * `strCash`: "Cash"
        * `strChange`: "Change"
        * `strCheck`: "Check"
        * `strComplimentary`: "Complimentary"
        * `strCreatedBy`: "Created by {0}"
        * `strCustomer`: "Customer"
        * `strDebit`: "Debit"
        * `strDuration`: "Duration"
        * `strEventInfo`: "Event Information"
        * `strExempt`: "(Exempt)"
        * `strExternal`: "External"
        * `strGC`: "Gift Card"
        * `strGratuity`: "Gratuity:"
        * `strGST`: "GST"
        * `strNA`: "N/A"
        * `strName`: "Name:"
        * `strPayment`: "Payment"
        * `strPST`: "PST"
        * `strReceiptNo`: "Receipt Number"
        * `strRefund`: "Refund"
        * `strSignature`: "Signature:"
        * `strSubtotal`: "Subtotal"
        * `strTender`: "Tendered"
        * `strTerminal`: "Terminal:"
        * `strTime`: "Time"
        * `strTotal`: "Total:"
        * `strTotal2`: "Total"
        * `strType`: "Type:"
        * `strUser`: "User:"
        * `strVoided`: "VOIDED"
        * `strVoidedPayment`: "*Voided Payments*"
        * `strVoucher`: "Voucher"
* **Control Panel Settings and Defaults**
	* MainEngine
		* `ReceiptFooterText1`
			* Default -> `""`
			* If non empty, then printed as the first line of the footer
	    * `ReceiptFooterText2`
		    * Default -> `""`
		    * If non empty, then printed as the second line of the footer
	    * `ReceiptFooterText3`
		    * Default -> `""`
		    * If non empty, then printed as the third line of the footer
	    * `ReceiptFooterText4`
		    * Default -> "Powered By www.ClubSpeed.com"
		    * If non empty, then printed as the fourth line of the footer
	    * `ReceiptFooterSurveyText1`
		    * Default -> `""`
		    * If non empty, then printed as the first line of the survey footer
	    * `ReceiptFooterSurveyText2`
		    * Default -> `""`
		    * If non empty, then printed as the second line of the survey footer
	    * `ReceiptHeaderText1`
		    * Default -> `""`
		    * If non empty, then printed as the first line of the header
	    * `ReceiptHeaderText2`
		    * Default -> `""`
		    * If non empty, then printed as the second line of the header
	    * `ReceiptHeaderText3`
		    * Default -> `""`
		    * If non empty, then printed as the third line of the header
	    * `ReceiptHeaderText4`
		    * Default -> `""`
		    * If non empty, then printed as the fourth line of the header
		* `OrganizationNumber`
			* Default -> `""`
			* If non empty, then prints the organization number for the fiscal printer
		* `PrintSurveyUrlOnReceipt`
			* Default -> `false`
			* If true, then print the value found in `URLSurvey` inside the survey footer
		* `PrintVoidedPayments`
			* Default -> `false`
			* If true, then included voided payments on the receipt
		* `URLSurvey`
			* Default -> `""`
			* If the receipt survey footer is being printed, and this is non empty, then printed as the URL for the survey
		* `PrintGratuityLine`
			* Default -> `"none"`
			* If `"none`" then never print the gratuity line, if `"eventonly"` then only print the gratuity lines for event checks where the gratuity is still `0`, if `"all"` then print the gratuity lines for all checks where the gratuity is still `0`
			* Options
				* `"none"`
				* `"eventonly"`
				* `"all"`
		* `Has2Taxes`
			* Default -> `false`
			* If true, then print out taxes as a split between PST and GST
			* If false, then print out taxes without a split
		* `ReceiptHeaderAlign`
			* Default -> `"center"`
			* Determines the alignment of the transaction receipt header
			* Options
				* `"center"`
				* `"left"`
				* `"right"`
		* `CompanyLogoPath`
			* Default -> `""`
			* If non empty, then print a placeholder template variable at the top for the company's logo. VB code will replace the template variable with the picture at the given path
* **Additional Logic**
	*  `printDetail`
		*  Default -> `true`
		*  If check type of `Event`, and the receipt print interface has `Print Detail` **unchecked**, then print product descriptions and check detail subtotals
			*  I know this seems backwards. Matches the old VB logic, we can change as necessary. 
	*  `accessCode`
		*  Default -> `""`
		*  If the receipt survey footer is being printed, and access code is non empty, then printed as the access code for the survey
		* Note: this setting is dynamically generated by VB when relevant, and is not a control panel setting

## Receipt Overrides

If the customization provided by control panel settings and translated resources are not enough, all receipts can be completely overwritten with custom logic by placing scripts at `c:\clubspeedapps\assets\receipts\{receipt-name}Template.js`. The exact names are as follows:

    c:\clubspeedapps\assets\receipts\creditCardTemplate.js
    c:\clubspeedapps\assets\receipts\entitleItemTemplate.js
    c:\clubspeedapps\assets\receipts\expenseTemplate.js
    c:\clubspeedapps\assets\receipts\foodOrderTemplate.js
    c:\clubspeedapps\assets\receipts\raceTicketTemplate.js
    c:\clubspeedapps\assets\receipts\tillReportTemplate.js
    c:\clubspeedapps\assets\receipts\transactionReceiptTemplate.js

These modules are expected to export a function with a signature of `create(body: Object): string`. Below is a full example of a replacement for the food order template which would cause those receipts to only print `"My awesome template override!"`.

    // file: c:/clubspeedapps/assets/receipts/foodOrderTemplate.js

    function CustomFoodOrderTemplater() {}
    CustomFoodOrderTemplater.prototype.create = function(body) {
        var receipt = "My awesome template override!";
        receipt += '\n\n\n\n\n\n';   // feed paper
        receipt += ('\x1d\x56\x01'); // cut paper
        return receipt;
    }

    module.exports = new CustomFoodOrderTemplater();