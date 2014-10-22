

    var page = require('webpage').create();

    page.viewportSize = { width: 200, height: 100 };

    

    page.open('http://vm-122.clubspeedtiming.com/index', function () {
				setTimeout(function() {
								page.render('vm-122.clubspeedtiming.com-1569897631_200_100.png');
								phantom.exit();
				}, 2000);
    });


    