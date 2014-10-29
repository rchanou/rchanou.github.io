/**
    ClubSpeed ApiService class definition.

    Library Requirements:
        1. zutil
            a. assert
            b. base
            c. check
            d. convert
            e. log
        2. jQuery
*/
(function(w, z, $, undefined) {
    var clubspeed = w.clubspeed = (w.clubspeed || {}); // implement or collect pointer for the clubspeed namespace
    clubspeed.helpers = clubspeed.helpers || {}; // implement or collect pointer for the clubspeed.helpers namespace
    // var c = clubspeed.helpers.css = clubspeed.helpers.css || {}; // implement or collect pointer for clubspeed.helpers.css

    var log = z.log;
    var check = z.check;
    var arrays = z.arrays;
    var equals = z.equals;

    /**
        Container for all CSS helper methods.
        
        @class Contains all CSS helper methods.
    */
    var CSSHelper = function() {};

    // maintain a set of constants
    var KEYS = {
        CSS: {
            NO_TRANSITION: '.noTransition',
            TRANSITION_END: 'transitionend MSTransitionEnd webkitTransitionEnd oTransitionEnd',
            ANIMATION_END: 'animationend webkitAnimationEnd oanimationend MSAnimationEnd'
        }
    };

    /**
        Checks for the existence of an image based on a provided uri.

        @param {string} imageUri The uri to check for existence.
        @returns {boolean} True if found, false if not found.
    */
    var imageExists = function(imageUri) {
        var image = new Image();
        image.src = imageUri;
        var doesImageExist = (image.width > 0);
        image = null; // make sure GC runs, so we dont have Image objects floating around
        return doesImageExist;
    }
    
    /**
        Looks for an existing class on any appended stylesheets,

    */
    var addToStyleSheet = function(cssClassName, cssClassDefinition) {
        var hasStyle = false,
            cssStyleSheet,
            cssRulesList,
            cssRule;

        if ($(cssClassName).length > 0) {
            // check if any elements exist which have the class
            // if they do, break early instead of looping through document
            return;
        }

        try {
            // note that document.styleSheets is array-like, not an array
            if (document && document.styleSheets) {
                for (var i = 0; i < document.styleSheets.length && hasStyle === false; i++) {
                    cssStyleSheet = document.styleSheets[i];
                    cssRulesList = cssStyleSheet.cssRules;
                    for (var k = 0; k < cssRulesList.length && hasStyle === false; k++) {
                        cssRule = cssRulesList[k];
                        if (z.check.exists(cssRule.selectorText)) {
                            hasStyle = (cssRule.selectorText.trim() === cssClassName.trim());
                        }
                    }
                }
            }
            if (!hasStyle) {
                log.debug("Style " + cssClassName + " was missing! Appending to stylesheet");
                if (z.check.exists(cssStyleSheet)) {
                    cssStyleSheet.insertRule(cssClassDefinition, cssRulesList.length);
                }
            }
        }
        catch(e) {
            log.error("Error while looking for style " + cssClassName + " : " + e);
        }
    };

    /**
        Applies a pre-determined set of animations 
        to a set of DOM elements,
        based on a provided Sizzle selector.

        @param {object} options TBD
        @returns {void}
    */
    var applyAnimations = function(options) {
        var elems,
            classes;

        if (z.convert(parameters.disableAnimations, z.types.boolean) === true) {

            // ensure that the noTransition helper style exists(!!!)
            addToStyleSheet(KEYS.CSS.NO_TRANSITION, KEYS.CSS.NO_TRANSITION + " { transitions: none; }");

            // override the animations - just show the hidden elements
            z.forEach(options.elems, function(x, key, obj) {
                elems = $(x.selector);
                classes = makeClassString([KEYS.CSS.NO_TRANSITION], x.classes.defaults); // disable transitions by adding 'transition: none' css class
                elems.addClass(classes);
            });
        }
        else {
            var d,
                delay,
                maxDelay,
                numElems,
                slideTimeout,
                wait,
                complete;

            // ensure that the delay is never longer than 2/3 of the slideTimeout, but dont go over options.maxDelay
            maxDelay = options.maxDelay || 350; // default to 350ms
            slideTimeout = z.convert.toNumber(parameters.slideTimeout) || 15000; // default to 15000ms to match sp_admin default time selection, whenever parameters.slideTimeout is not available
            z.forEach(options.elems, function(x, key, obj) {
                elems = $(x.selector);
                if (x.items)
                    elems = x.items;
                numElems = elems.length;
                classes = makeClassString(x.classes.defaults, x.classes.animations);
                delay = (d = (slideTimeout) / (numElems * 1.5)) > maxDelay ? maxDelay : d;
                wait = x.wait > 0 ? delay * x.wait : 0;
                if (check.isFunction(x.onAnimationEnd)) {
                    elems.one(KEYS.CSS.ANIMATION_END + ' ' + KEYS.CSS.TRANSITION_END, function(e) {
                        if (e.type === 'transitionend')
                            x.onAnimationEnd.apply(this, e); // super hacky, why is chrome firing off TransitEvent AND WebKitAnimationEvent here?
                    });
                }
                if (check.isFunction(x.onAllAnimationsEnd)) {
                    $(arrays.last(elems)).one(KEYS.CSS.ANIMATION_END + ' ' + KEYS.CSS.TRANSITION_END, function(e) {
                        if (e.type === 'transitionend')
                            x.onAllAnimationsEnd.apply(elems);
                    });
                }
                (function(elems, classes, delay, wait) {
                    setTimeout(function() {
                        applyHtmlClass(elems, classes, delay);
                    }, wait);
                })(elems, classes, delay, wait); // use iife + parameters to maintain correct references
            });
        }
    };

    /**
        Applies css classes to a set of elements with an optional delay between each application.

        @param {object} elems The jQuery objects to which to apply the animations.
        @param {string|array<string>} cssClasses A comma separated string or array of strings containing the classes to be applied to the elements.
        @param {number} [delay] The number of milliseconds to delay between each html class application. If no delay is provided, then 0 is used.
        @returns {void}
    */
    var applyHtmlClass = function(elems, cssClasses, delay) {
        delay = delay || 0;
        
        z.assert.isString(cssClasses);
        if (elems) {
            if (elems.length) {
                if (delay > 0) {
                    for (var i = 0; i < elems.length; i++) {
                        (function(index) {
                            setTimeout(function() {
                                var elem = $(elems[index]);
                                elem.visible(); // hard coded -- bad idea(?) may need a refactor if we ever don't want to make items visible
                                elem.addClass(cssClasses);
                            }, delay*index);
                        })(i); // use an iife to maintain the correct index for setTimeout
                    }
                }
                else {
                    elem.addClass(cssClasses);
                }
            }
        }
    };

    /**
        Mutates a set of css class strings
        into a single space-separated string
        for use with jQuery selectors.

        @param {array<string>|string} cssClasses The list of css classes. 
                                                 May be an array of strings, 
                                                 a comma-separated set of strings, 
                                                 or a space-separated set of strings.

    */
    var makeClassString = function() {
        var args = Array.prototype.slice.call(arguments);
        var r = [];
        z.forEach(args, function(x, key, arr) {
            if (z.check.isArray(x)) {
                r = r.concat(x.map(function(x) { return x.toString().trim(); }));
            }
            else if (z.check.isString(x)) {
                var splitChar = x.indexOf(",") > -1 ? "," : x.indexOf(" ") > -1 ? " " : "";
                r = r.concat(x.split(splitChar).map(function(x) { return x.trim(); }));
            }
        });
        return r.distinct().join(" ");
    };

    $.fn.visible = function() {
        return this.css('visibility', 'visible');
    };

    $.fn.invisible = function() {
        return this.css('visibility', 'hidden');
    };

    // set clubspeed.helpers.css to the new CSSHelper(), also return it
    return clubspeed.helpers.css = (function(cssHelper) {
        z.defineProperty(cssHelper, "addToStyleSheet", { get: function() { return addToStyleSheet }, writeable: false });
        z.defineProperty(cssHelper, "applyAnimations", { get: function() { return applyAnimations }, writeable: false });
        z.defineProperty(cssHelper, "imageExists", { get: function() { return imageExists }, writeable: false });
        return cssHelper;
    })(new CSSHelper());

})(window || this, z /* zUtil */, $ /* jquery */);
