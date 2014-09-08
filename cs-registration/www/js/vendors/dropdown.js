$(document).ready(function() {
    $(".dropdown dt a").click(function() {
    $(".dropdown dd ul").toggle();
    });

$(".dropdown dd ul li a").click(function() {
    var text = $(this).html();
    $(".dropdown dt a span").html(text);
    $(".dropdown dd ul").hide();
    $('#loadingModal').modal();

    if ( (window.location.href).indexOf('/step') != -1)
    {
        window.location.href = (window.location.href).replace("#","").replace("/step","/changeLanguage/" + getSelectedValue("languageDropdown") + "/step"); //replace /step with "/changeLanguage/" + text + "/step"
    }
    else if ( (window.location.href).indexOf('/www') != -1)
    {
        console.log(window.location.href);
        window.location.href = (window.location.href).replace("#","").replace("/cs-registration/www/","/cs-registration/www/changeLanguage/" + getSelectedValue("languageDropdown") + "/step1");
        console.log(window.location.href);

    }
    else
    {
        window.location.href = (window.location.href).replace("#","").replace("/cs-registration/","/cs-registration/changeLanguage/" + getSelectedValue("languageDropdown") + "/step1");
    }

    });

function getSelectedValue(id) {
    return $("#" + id).find("dt a span.value").html();
    }

$(document).bind('click', function(e) {
    var $clicked = $(e.target);
    if (! $clicked.parents().hasClass("dropdown"))
    $(".dropdown dd ul").hide();
    });

});