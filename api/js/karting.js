function init() {
    // collapseSections();
    // sectionCollapseSetup();
    scrollSpySetup();
    sideNavSetup();
    scrollActiveSetup();
}

function collapseSections() {
    $('.call-section').hide();
}

function sectionCollapseSetup() {
    $('.call-header').on('click', function(e) {
        var callSection = $(this).siblings('.call-section');
        callSection.toggle(); // slide toggle messes with bootstrap background spacing
        var icons = $(this).find('.glyphicon-chevron-right, .glyphicon-chevron-down');
        if (callSection.is(':visible')) {
            icons.removeClass('glyphicon-chevron-right').addClass('glyphicon-chevron-down');
        }
        else {
            icons.removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-right');
        }
    });
}

function sideNavSetup() {
    $('#sidebar').affix({
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

    var sidebar = $('#sidebar');
    var topHeight = $("#main-header").outerHeight(); // probably not necessary, unless making sticky

    var callGroups = sidebar.find('.nav-group');
    var callItems = callGroups.find('.nav-group-item');
    var anchorList = sidebar.find('a');
    var scrollList = anchorList.map(function() {
        var item = $($(this).attr("href")); // ensure the anchor we are looking at has an href with a length
        if (item.length) return item;
    });

    $(window).scroll(function() {
        anchorList.removeClass('active');

        var fromTop = $(this).scrollTop() + 1; // note the 1 -- the scrollTop on its own isnt quite right..
        var possibleItems = scrollList.map(function() {
            if ($(this).offset().top < fromTop) // could build this and store it instead of recalculating? might cause problems with window resizes
                return this;
        });
        var scrollCurrent = possibleItems[possibleItems.length-1]; // last element found
        var scrollCurrentId = scrollCurrent && scrollCurrent.length ? scrollCurrent[0].id : "";


        // TODO: change the page layout to have a space for header by default
        // make the container start lower -- make the container push down by a specific amount?
        
        // var scrollCurrentHeader = scrollCurrent.parents('.call-group').find('.call-header');
        // scrollCurrentHeader.removeClass('active-header'); // remove first(?)
        // scrollCurrentHeader.addClass('active-header');

        // var scrollInactiveHeaders = $('.call-header').not(scrollCurrentHeader);
        // scrollInactiveHeaders.removeClass('active-header');

        // END TODO


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
    });
}

$(function() {
    init();
});