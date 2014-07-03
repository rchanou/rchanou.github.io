//This function dynamically creates a slide show from image files named "slideX.png", where X is a
//number starting at 1 and ending with the number of slides.
function homeCarouselController($scope) {
    $scope.myInterval = 3000; //Time between slides, in seconds
    var numberOfSlides = 3; //Number of slides

    var slides = $scope.slides = [];
    $scope.addSlide = function(slideNumber) {
        slides.push({
            image: 'images/slide' + slideNumber + '.png',
            text: ''
        });
    };
    for (var i=1; i<=numberOfSlides; i++) {
        $scope.addSlide(i);
    }
}
