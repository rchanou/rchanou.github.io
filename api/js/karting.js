// function sectionCollapseSetup() {
//     $('.call-header').on('click', function(e) {
//         var callSection = $(this).siblings('.call-section');
//         callSection.toggle(); // slide toggle messes with bootstrap background spacing
//         var icons = $(this).find('.glyphicon-chevron-right, .glyphicon-chevron-down');
//         if (callSection.is(':visible')) {
//             icons.removeClass('glyphicon-chevron-right').addClass('glyphicon-chevron-down');
//         }
//         else {
//             icons.removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-right');
//         }
//     });
// }

function sideNavSetup() {
    $('#sidebar')
    .affix({
        offset: {
            top: $('#main-header').outerHeight()
        }
    });
}

function scrollSpySetup() {
    $('body').scrollspy({ 
        target: '.navbar-example' 
    });
}

function scrollActiveSetup() {
    // var topHeight = $("#main-header").outerHeight(); // not necessary, unless making sticky header
    var sidebar = $('#sidebar');
    var callGroups = sidebar.find('.nav-group');
    var callItems = callGroups.find('.nav-group-item');
    var anchorList = sidebar.find('a');
    var scrollList = anchorList.map(function() {
        var item = $($(this).attr("href")); // ensure the anchor we are looking at has an href with a length
        if (item.length) return item;
    });

    // declare here for scoping of variables above
    function doOnScroll() {
        // there's probably a more efficient way to do this, but this works for now
        anchorList.removeClass('active');
        var fromTop = $(this).scrollTop() + 1; // note the 1 -- the scrollTop on its own isnt quite right..
        var possibleItems = scrollList.map(function() {
            if ($(this).offset().top < fromTop)
                return this;
        });

        var scrollCurrent = possibleItems[possibleItems.length-1]; // last element found
        var scrollCurrentId = scrollCurrent && scrollCurrent.length ? scrollCurrent[0].id : "";
        var activeAnchor = anchorList.filter('[href=#' + scrollCurrentId + ']').first();
        activeAnchor.addClass('active');

        var activeAnchorGroup = $(activeAnchor).parents('.nav-group').first();
        var activeAnchors = activeAnchorGroup.find('.nav-group-item').find('a');
        activeAnchors.show();

        var inactiveAnchorGroup = callGroups.not(activeAnchorGroup);
        var inactiveAnchors = inactiveAnchorGroup.find('.nav-group-item').find('a');
        inactiveAnchors.hide();

        if (!activeAnchor.hasClass('nav-group-header')) {
            // scrolled to a specific call -- make the anchor's header is active as well
            var activeAnchorGroupHeader = $(activeAnchorGroup).children('.nav-group-header').first();
            activeAnchorGroupHeader.addClass('active');
        }
    };

    $(window).scroll(doOnScroll.bind(this));
    doOnScroll.apply($(window)); // call this once to ensure the logic is executed before the user scrolls
    sidebar.removeClass('visibility-hidden'); // hackish way to prevent the sidebar from flashing -- note that we have to use visibility: hidden; since display: none; breaks bootstrap's sidebar setup
}

function init() {
    // sectionCollapseSetup();
    scrollSpySetup();
    sideNavSetup();
    scrollActiveSetup();
}

$(function() {
    init();
});