var page = require('webpage').create(),
    system = require('system'),
    address, output, size;
var debug = false;

if(debug) {
	function printArgs() {
			var i, ilen;
			for (i = 0, ilen = arguments.length; i < ilen; ++i) {
					console.log("    arguments[" + i + "] = " + JSON.stringify(arguments[i]));
			}
			console.log("");
	}
	
	page.onConsoleMessage = function(msg) {
			console.log(msg);
	};
	page.onLoadStarted = function() {
			console.log("page.onLoadStarted");
			printArgs.apply(this, arguments);
	};
	page.onUrlChanged = function() {
			console.log("page.onUrlChanged");
			printArgs.apply(this, arguments);
	};
	page.onNavigationRequested = function() {
			console.log("page.onNavigationRequested");
			printArgs.apply(this, arguments);
	};
}

console.log('rasterize.js received these arguments:', system.args);

function waitFor(testFx, onReady, timeOutMillis) {
    var maxtimeOutMillis = timeOutMillis ? timeOutMillis : 2000, //< Default Max Timout is 3s
        start = new Date().getTime(),
        condition = false,
        interval = setInterval(function() {
            if ( (new Date().getTime() - start < maxtimeOutMillis) && !condition ) {
                // If not time-out yet and condition not yet fulfilled
                condition = (typeof(testFx) === "string" ? eval(testFx) : testFx()); //< defensive code
            } else {
                if(!condition) {
                    // If condition still not fulfilled (timeout but condition is 'false')
                    console.log("'waitFor()' timeout" + (new Date().getTime() - start) + "ms.");
                    typeof(onReady) === "string" ? eval(onReady) : onReady(); //< Do what it's supposed to do once the condition is fulfilled
										clearInterval(interval);
										//phantom.exit(1);
                } else {
                    // Condition fulfilled (timeout and/or condition is 'true')
                    console.log("'waitFor()' finished in " + (new Date().getTime() - start) + "ms.");
                    typeof(onReady) === "string" ? eval(onReady) : onReady(); //< Do what it's supposed to do once the condition is fulfilled
                    clearInterval(interval); //< Stop this interval
                }
            }
        }, 250); //< repeat check every 250ms
};

if (system.args.length < 3 || system.args.length > 6) {
    console.log('Usage: rasterize.js URL filename [paperwidth*paperheight|paperformat] [zoom] [timeoutInMs]');
    console.log('  paper (pdf output) examples: "5in*7.5in", "10cm*20cm", "A4", "Letter"');
    console.log('  image (png/jpg output) examples: "1920px" entire page, window width 1920px');
    console.log('                                   "800px*600px" window, clipped to 800x600');
    phantom.exit(1);
} else {
    address = system.args[1];
    output = system.args[2];
    page.viewportSize = { width: 600, height: 600 };
    if (system.args.length > 3 && system.args[2].substr(-4) === ".pdf") {
        size = system.args[3].split('*');
        page.paperSize = size.length === 2 ? { width: size[0], height: size[1], margin: '0px' }
                                           : { format: system.args[3], orientation: 'portrait', margin: '1cm' };
    } else if (system.args.length > 3 && system.args[3].substr(-2) === "px") {
        size = system.args[3].split('*');
        if (size.length === 2) {
            pageWidth = parseInt(size[0], 10);
            pageHeight = parseInt(size[1], 10);
            page.viewportSize = { width: pageWidth, height: pageHeight };
            page.clipRect = { top: 0, left: 0, width: pageWidth, height: pageHeight };
        } else {
            console.log("size:", system.args[3]);
            pageWidth = parseInt(system.args[3], 10);
            pageHeight = parseInt(pageWidth * 3/4, 10); // it's as good an assumption as any
            console.log ("pageHeight:",pageHeight);
            page.viewportSize = { width: pageWidth, height: pageHeight };
        }
    }
    if (system.args.length > 4) {
        page.zoomFactor = system.args[4];
    }
		
		// Output errors to console
		page.onError = function (msg, trace) {
				console.log(msg);
				trace.forEach(function(item) {
						console.log('  ', item.file, ':', item.line);
				});
		};
    
		/**
		 * Perform login if sp_admin in the URL
		 */
		if(~address.toLowerCase().indexOf('/sp_admin')) {
			var loginUrl = address.substring(0, address.toLowerCase().indexOf('sp_admin')) + "sp_admin/loginfrompos.aspx";
			var reportUrl = address; // Example: "https://ftikcincinnati.clubspeedtiming.com/SP_Admin/EventReservationDetail.aspx?ID=100"; k1irvine (no pass needed?)
			var username = 'printer';
			var password = 'pr1nt3r';
			var loginSubmitted = false;
			
			if(debug) console.log('Accessing admin panel URL', reportUrl);
			
			var fillLoginInfo = function(username, password, debug){
					if(debug) console.log('Logging in with:', username, password, 'at', loginUrl);
					var frm = document.getElementById("form1");
					frm.elements["txtUsername"].value = username;
					frm.elements["txtPassword"].value = password;
					frm.elements["btnLogin"].click();
			}
			page.onLoadFinished = function(){
				if(debug) {
					console.log("page.onLoadFinished");
					printArgs.apply(this, arguments);
				}
				
				// Note: Older versions of Club Speed (without "loginfrompos.aspx") seem to allow direct URL access.
				// Newer versions require a login first. The logic below handles both cases by either logging in or
				// going straight to the report URL directly.
				
				if(loginSubmitted === false && ~page.url.indexOf('loginfrompos.aspx') && !~page.url.toLowerCase().indexOf('pagenotfound.aspx')){
					page.evaluate(fillLoginInfo, username, password, debug);
					loginSubmitted = true;
					return;
				} else if(~page.url.toLowerCase().indexOf('pagenotfound.aspx') || (loginSubmitted === true && ~page.url.indexOf('loginfrompos.aspx'))) {
					loginSubmitted = true;
					page.open(reportUrl);
					return;
				}
			
				// Render
				page.evaluate(function() { document.body.bgColor = 'white'; });
				page.render(output);
				if(debug) console.log("Completed", page.url);
				phantom.exit();
			}
			page.open(loginUrl);
		}
		
		
		/**
		 * All other URLs not needing a login to take place
		 */
		else {
			page.open(address, function (status) {
					if (status !== 'success') {
							console.log('Unable to load the address!');
							phantom.exit(1);
					} else {
							waitFor(function() {
									return false; // Bypassing for now, defaulting to timeout
									
									// Check in the page if a specific element is now visible
									return page.evaluate(function() {
											return $("#ready-to-print").is(":visible");
									});
							}, function() {
								 page.evaluate(function() { document.body.bgColor = 'white'; });
								 page.render(output);
								 phantom.exit();
							}, system.args[4]); 
					}
			});
		}
}