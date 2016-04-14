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
			overflow: hidden;
			background-color: #000000;
		}
		iframe {
			position: absolute;
			top: 32px;
			left: 0;
			width: 100%;
			height: 768px;
		}
		#headerBar {
		    background-color: #535353;
		    width: 100%;
		    max-width: 1024px;
		    min-width: 800px;
		    height: 32px;
		    margin: 0px auto;
		    padding: 0px;
		    z-index: 100;
		}
		</style>
  </head>
  <body>
     <div id="headerBar">
        <img src="home_ffffff_32.png" style="cursor: pointer;" onclick="resetInnerFrame()">
     </div>
    <iframe id="innerFrame" src="$targetUrl" style="cursor: none;" frameborder="0" onload="disableFacebookAutoComplete()" nwdisable nwfaketop></iframe>
  </body>
  <script language="javascript">

	console.log("Current locale: " + window.navigator.language);

	function disableFacebookAutoComplete()
	{
		try { document.getElementById('innerFrame').contentDocument.getElementById('email').setAttribute('autocomplete', 'off'); }
		catch(ex){  }
		window.scrollTo(0,0);
	}
    function resetInnerFrame()
    {
        document.getElementById('innerFrame').src = "$targetUrl";
    }
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

    console.log("NodeWebKit version: ", process.version);


	//document.write(string);

	var win = gui.Window.get();

	var screen_index = $screen_index < screens.length ? $screen_index : 1;

	win.moveTo(screens[screen_index].bounds.x, screens[screen_index].bounds.y);
	win.enterFullscreen();

	//win.resizeTo(screens[screen_index].bounds.width, screens[screen_index].bounds.height);

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
exec("lib\\7z.exe a -tzip package.zip index.html package.json home_ffffff_32.png");
exec("rename package.zip package.nw");
exec("copy /b /Y nw.exe+package.nw package.exe");
exec("enigmavbconsole.exe package.evb");
exec("mkdir generatedGenericExes");
exec("move /y app.exe generatedGenericExes/$appName.exe");
exec("del package.nw");
exec("del package.zip");
exec("del package.exe");
exec("del app.exe");

chdir("generatedGenericExes");
header('Content-Type: application/x-msdownload');
header('Content-Length: ' . filesize("./$appName.exe"));
header("Content-Disposition: attachment; filename=\"$appName.exe\"");
header("Content-Transfer-Encoding: binary");
header("Cache-Control: no-cache, must-revalidate, post-check=0, pre-check=0"); // HTTP/1.1
header("Cache-Control: private",false);
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header('Pragma: public');

readfile("./$appName.exe");
