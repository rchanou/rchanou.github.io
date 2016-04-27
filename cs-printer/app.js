// CS PRINTER REPLACEMENT
// Usage: node app.js [urlToPrint] [Printer Name:default] [Render Timeout:10000] [Paper Size:8.5in*11in]
// node app.js http://www.reddit.com

// This will save both the PDF printed and any output into ./output folder
// Older files are cleaned up after a period of time. See setting below, oldFileLifetimeInMs 

// DONE: Can implement "waiting for DOM" in rasterize.js...
// - https://github.com/ariya/phantomjs/blob/master/examples/waitfor.js
// !!! When ready to print, set #ready-to-print to :visible in HTML/Handlebars (or hitting the default "timeout" below will cause it to print)
// !!! Can also make this logic variable and look for content in a DIV

// ??? Club Speed needs us to login to admin portal to print?
// See CSPrinter's FrmMain.vb: If strURL.Contains(URLAdmin) Then loginToAdminPortal() Could likely look for url.indexOf('/sp_admin/') > -1
// If so: http://casperjs.org/ (get the printer user/pass via API -- key in index.php), login at: loginfrompos.aspx (grab URL used with http://.../sp_admin/ prefixed)
// Dim txtUsername As System.Windows.Forms.HtmlElement = WebBrowser1.Document.GetElementById("txtUsername")
// Dim txtPassword As System.Windows.Forms.HtmlElement = WebBrowser1.Document.GetElementById("txtPassword")
// Dim btnLogin As System.Windows.Forms.HtmlElement = WebBrowser1.Document.GetElementById("btnLogin")

var path = require('path')
  , childProcess = require('child_process')
  , phantomjs = require('phantomjs')
  , fs = require('fs');

// Load variables from file (if it exists)
var config = {};
if (fs.existsSync(path.join(__dirname, 'config.js'))) {
	config = require(path.join(__dirname, 'config.js'));
} else {
	config.debug = false;
  config.paperSize = '8.5in*11in';
	config.retentionTimeInMinutes = 10;
}

var url       = process.argv[2];
var printer   = process.argv[3] && process.argv[3] !== 'default' ? '-print-to ' + process.argv[3] : '-print-to-default'
var timeout   = process.argv[4] || 10000;
var paperSize = process.argv[5] || config.paperSize || '9in*12in';
var oldFileLifetimeInMs = (config.retentionTimeInMinutes || 10)*60*1000;
var printJobDirectory = path.join(__dirname, 'print-jobs');

// Overrides for testing specific sheets (also see paperSize variable)
//var url = 'http://k1sandiego.clubspeedtiming.com/PrivateWWW/SpeedSheetK1.aspx?HeatNo=53780&CustID=17096126'
//var url = 'http://pprlongisland.clubspeedtiming.com/assets/speed-sheets/pole-position.html?HeatNo=40990&CustID=1057621'
//var url = 'http://sipcalgary.clubspeedtiming.com/assets/speed-sheet/halfsheet.html?HeatNo=63155&CustID=2098969'

// Filename prefix, needs to be more random? :-)
var d = new Date();
var n = d.getTime() + '-' + Math.floor((Math.random() * 10000) + 1);

// Create print job directory (if it doesn't exist)
if (!fs.existsSync(printJobDirectory)){
    fs.mkdirSync(printJobDirectory);
}

var filename    = path.join(printJobDirectory, n + '-page.pdf');
var logFilename = path.join(printJobDirectory, n + '-log.txt');

var phantomjsBinPath = phantomjs.path
var phantomjsArgs = [
  path.join(__dirname, 'rasterize.js'),
  url,
  filename,
  paperSize,
  timeout
];

var sumatraBinPath = path.join(__dirname, 'SumatraPDF.exe');
var sumatraArgs = [
	'-silent',
  '-exit-on-print',
	printer,
	filename
]

// Run PhantomJS to create PDF
childProcess.execFile(phantomjsBinPath, phantomjsArgs, function(phantomErr, phantomStdout, phantomStderr) {
  console.log('Created', filename);

  // Print PDF -- "SumatraPDF.exe -print-to-default output.pdf"
  // To get notified if an error occurred (with -silent) START /WAIT SumatraPDF.exe -silent -print-to "..." "..." & IF ERRORLEVEL 1 ECHO failure
  // Omitting -silent will give a popup
  childProcess.exec(sumatraBinPath + ' ' + sumatraArgs.join(' '), function(sumatraErr, sumatraStdout, sumatraStderr) {
    
		// Write logfile -- could make output prettier, object?
		var logStr = '/// RESULTS OF RENDERING & PRINTING ' + filename + ' ///\n\n';
		logStr += '/// Program Inputs: (Overall Program)\n' + JSON.stringify(process.argv, null, 4) + '\n\nPhantom (URL->PDF Rendering)\n' + JSON.stringify(phantomjsArgs, null, 4) + '\n\nSumatraPDF (PDF Printing)\n' + JSON.stringify(sumatraArgs, null, 4) + '\n\n';
		logStr += '/// Errors:\nPhantom (URL->PDF Rendering)\n' + JSON.stringify(phantomErr, null, 4) + '\n' + JSON.stringify(phantomStderr, null, 4) + '\n\nSumatraPDF (PDF Printing)\n' + JSON.stringify(sumatraErr, null, 4) + '\n' + JSON.stringify(sumatraStderr, null, 4) + '\n\n';
		logStr += '/// Normal Output:\nPhantom (URL->PDF Rendering)\n' + phantomStdout + '\n\nSumatraPDF (PDF Printing)\n' + sumatraStdout;
		fs.writeFile(logFilename, logStr, function(err) {
			if(err) return console.log('Error writing logfile', err);
			
			console.log(logStr);
		}); 
    
    console.log('Printed', filename);
  });
})

try {
  // Remove older files
  var findRemoveSync = require('find-remove');
  var deletedFiles = findRemoveSync(printJobDirectory, {age: {seconds: (oldFileLifetimeInMs / 1000) }, extensions: ['.txt', '.pdf']});
  console.log('Cleaned up older files:', deletedFiles);
} catch(e) {
  console.log('ERROR CLEARING CACHE', e);

  if(config.debug) {
    fs.writeFile('error-cache-' + n + '.err', e.toString(), function(err) {
      if(err) console.log('Error writing error logfile', err);
      // No need to exit, this is a "soft" error
    });
  }
}

process.on('uncaughtException', function(e) {
  console.log('ERROR: uncaughtException', e);
  if(config.debug) {
    fs.writeFile('error-uncaughtException-' + n + '.err', e.toString(), function(err) {
      if(err) console.log('Error writing uncaughtException log', err);
      process.exit(1);
    });
  }
});

process.on('unhandledRejection', function(e) {
  console.log('ERROR: unhandledRejection', e);
  if(config.debug) {
    fs.writeFile('error-unhandledRejection-' + n + '.err', e.toString(), function(err) {
      if(err) console.log('Error writing unhandledRejection log', err);
      process.exit(1);
    });
  }
});
