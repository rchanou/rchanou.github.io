
# CSPrinter

  This package handles taking jobs from Main Engine (CSPrinter2.exe), rendering them to PDF (phantomjs.exe) and printing them (sumatrapdf.exe).

## Installation

  Update the ControlPanel MainEngine:PrinterFolder to be the full path to (and including) CSPrinter2.exe. Ex: `C:\ClubSpeedApps\cs-printer\CSPrinter2.exe`

  Kill off any older `csprinter.exe` processes and restart MainEngine to take effect.

## Configuration (Optional)

  If you'd like to customize the paper size or retention time of PDFs/Logs, copy `config.orig.js` to `config.js` and edit the configuration file.

## Troubleshooting

  Run `node app.js <url> <printername>` from the command line to see any console-based errors.

Example: `node app.js http://www.google.com resultprinter1`

  In the `output` folder there will be a PDF created and a matching log file. You can open these to see what was rendered and also what errors and output were logged.