

    var page = require('webpage').create();

    page.viewportSize = { width: 200, height: 100 };

    

    page.open('http://vm-122.clubspeedtiming.com', function () {
				setTimeout(function() {
								page.render('vm-122.clubspeedtiming.com-2026619412_200_100.png');
								phantom.exit();
				}, 500);
    });


    