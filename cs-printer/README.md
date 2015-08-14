
# CSPrinter

  This package handles taking jobs from Main Engine (csprinter.exe), rendering them to PDF (phantomjs.exe) and printing them (sumatrapdf.exe).

## Installation

  Update the ControlPanel MainEngine:PrinterFolder to be the full path to (and including) csprinter.exe. Ex: `C:\ClubSpeedApps\cs-printer\csprinter.exe`

  Kill off any `csprinter.exe` processes and restart MainEngine to take effect.

## Troubleshooting

  Run `node app.js <url> <printername>` from the command line to see any console-based errors.

Example: `node app.js http://www.google.com resultprinter1`

  In the `output` folder there will be a PDF created and a matching log file. You can open these to see what was rendered and also what errors and output were logged.