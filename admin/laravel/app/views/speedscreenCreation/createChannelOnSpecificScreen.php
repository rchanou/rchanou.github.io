<?php

$screen_index = $targetMonitor - 1;

$indexHTML = <<<EOT
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Loading Club Speed...</title>
    <style type="text/css">
		body {
			margin: 0;
			padding: 0;
		}
		iframe {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
		}
		#container {
			display: block;
			position: absolute;
			top: 10px;
			right: 0;
			padding: 1em;
			width: 100%;
			height: 3em;
			font-family: Helvetica, Arial, Sans-Serif;
			font-weight: bold;
			z-index: 9999999;
		}
		#container:hover #controls {
			display: block;
		}
		#controls {
			display: none;
			background-color: #ffffff;
			float: right;
		}
		</style>
  </head>
  <body>
    <div id="container">
    	<div id="controls">
      <span id="close">[CLOSE SCREEN]</span> <span id="full-screen">[TOGGLE FULL SCREEN]</span>
      </div>
     </div>
    <iframe src="$channelUrl" style="cursor: none;" frameborder="0" nwdisable nwfaketop></iframe>

  </body>
  <script language="javascript">
	// Load native UI library
	var gui = require('nw.gui');
    gui.App.clearCache();

	function ScreenToString(screen) {
		var string = "";
		string += "screen " + screen.id + " ";
		var rect = screen.bounds;
		string += "bound{" + rect.x + ", " + rect.y + ", " + rect.width + ", " + rect.height + "} ";
		rect = screen.work_area;
		string += "work_area{" + rect.x + ", " + rect.y + ", " + rect.width + ", " + rect.height + "} ";
		string += " scaleFactor: " + screen.scaleFactor;
		string += " isBuiltIn: " + screen.isBuiltIn;
		string += "<br>";
		return string;
	}

	//init must be called once during startup, before any function to gui.Screen can be called
	gui.Screen.Init();
	var string = "";
	var screens = gui.Screen.screens;
	// store all the screen information into string
	for(var i=0; i<screens.length; i++) {
		string += ScreenToString(screens[i]);
	}
	console.log(screens);
	console.log(string);


	//document.write(string);

	var win = gui.Window.get();

	var screen_index = $screen_index < screens.length ? $screen_index : 1;

	win.moveTo(screens[screen_index].bounds.x, screens[screen_index].bounds.y);
	win.enterFullscreen();

	//win.resizeTo(screens[screen_index].bounds.width, screens[screen_index].bounds.height);

	document.getElementById('close').onclick = function() { win.close(); };
	document.getElementById('full-screen').onclick = function() { win.toggleKioskMode(); };

	var screenCB = {
		onDisplayBoundsChanged : function(screen) {
			var out = "OnDisplayBoundsChanged " + ScreenToString(screen);
			//document.write(out);
		},

		onDisplayAdded : function(screen) {
			var out = "OnDisplayAdded " + ScreenToString(screen);
			//document.write(out);
		},

		onDisplayRemoved : function(screen) {
			var out = "OnDisplayRemoved " + ScreenToString(screen);
			//document.write(out);
		}
	};

    var option = {
		key : "Ctrl+Shift+D",
		active : function() {
			console.log("Global desktop keyboard shortcut: " + this.key + " active.");
			win.showDevTools();
		},
		failed : function(msg) {
			// :(, fail to register the |key| or couldn't parse the |key|.
			console.log(msg);
		}
	};

	// Create a shortcut with |option|.
	var shortcut = new gui.Shortcut(option);

	// Register global desktop shortcut, which can work without focus.
	gui.App.registerGlobalHotKey(shortcut);

	// listen to screen events
	gui.Screen.on('displayBoundsChanged', screenCB.onDisplayBoundsChanged);
	gui.Screen.on('displayAdded', screenCB.onDisplayAdded);
	gui.Screen.on('displayRemoved', screenCB.onDisplayRemoved);
	</script>
</html>
EOT;


file_put_contents('./speedscreenCreation/index.html',$indexHTML);
chdir("speedscreenCreation");
exec("lib\\7z.exe a -tzip package.zip index.html package.json");
exec("rename package.zip package.nw");
exec("copy /b /Y nw.exe+package.nw package.exe");
exec("enigmavbconsole.exe package.evb");
exec("mkdir generatedChannels");
exec("move /y app.exe generatedChannels/channel$channelNumber" . "screen" . $targetMonitor . ".exe");
exec("del package.nw");
exec("del package.zip");
exec("del package.exe");
exec("del app.exe");

chdir("generatedChannels");
header('Content-Type: application/x-msdownload');
header('Content-Length: ' . filesize("./channel$channelNumber" . "screen" . $targetMonitor . ".exe"));
header("Content-Disposition: attachment; filename=\"channel$channelNumber" . "screen" . $targetMonitor . ".exe\"");
header("Content-Transfer-Encoding: binary");
header("Cache-Control: no-cache, must-revalidate, post-check=0, pre-check=0"); // HTTP/1.1
header("Cache-Control: private",false);
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header('Pragma: public');

readfile("./channel$channelNumber" . "screen" . $targetMonitor . ".exe");
