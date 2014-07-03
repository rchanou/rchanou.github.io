$(document).ready(function() {
    $(".dropdown dt a").click(function() {
    $(".dropdown dd ul").toggle();
    });

$(".dropdown dd ul li a").click(function() {
    var text = $(this).html();
    $(".dropdown dt a span").html(text);
    $(".dropdown dd ul").hide();
    $('#loadingModal').modal();
    console.log(window.location.href);
    window.location.href = (window.location.href).replace("/step","/changeLanguage/" + getSelectedValue("languageDropdown") + "/step").replace("#",""); //replace /step with "/changeLanguage/" + text + "/step"
    console.log(window.location.href);

    //replace # with ""
    //$("#result").html("Selected value is: " + getSelectedValue("sample"));
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