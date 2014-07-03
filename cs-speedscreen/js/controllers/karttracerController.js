/** karttracerController
*/
speedScreenDemoApp.controller('karttracerController', function($scope, $routeParams, speedScreenServices,
                                                                 SocketIOService, $timeout, $interval) {

    var socket = io.connect('http://l66breda.clubspeedtiming.com:8081');
    socket.on('rtd', function (realTimeData) {
        console.log(realTimeData);
        $(createDataPoint(realTimeData, { maxX: 63, maxY: 43})).appendTo("#map").fadeOut(fadeOut, function() { $(this).remove(); });
    });

    var fadeOut = 1000;
    $(document).ready(function() {
        setInterval(function(){
            var realTimeData = {
                kartId: 0,
                x: Math.floor((Math.random()*60)+1),
                y: Math.floor((Math.random()*40)+1),
                timestamp: ''
            }
    $(createDataPoint(realTimeData, { maxX: 63, maxY: 43})).appendTo("#map").fadeOut(fadeOut, function() { $(this).remove(); });
    },500);
    });
    function createDataPoint(rtd, maximumLengths) {
        var y = (rtd.y / maximumLengths.maxY) * 100;
        var x = (rtd.x / maximumLengths.maxX) * 100;
        console.log(rtd);
        console.log(x + ', ' + y);
        var html = '<span class="dot" style="top: ' + y + '%; left: ' + x + '%">&bull;</span>';
        return html;
        }
});
