"use strict";

function isCJK(charCode) {
    if (charCode === 0xA5)
        return true; // Yen symbol (epson prints yen as double width, for some reason)
    if (charCode >= 0xFF01 && charCode <= 0xFF60)
        return true;
    if (charCode >= 0x4E00 && charCode <= 0x9FFF)
        return true; // CJK Unified Ideographs (Common)
    if (charCode >= 0x3400 && charCode <= 0x4DFF)
        return true; // CJK Unified Ideographs Extension A (Rare)
    if (charCode >= 0x20000 && charCode <= 0x2A6DF)
        return true; // CJK Unified Ideographs Extension B (Rare, Historic)
    if (charCode >= 0xF900 && charCode <= 0xFAFF)
        return true; // CJK Compatibility Ideographs (Duplicates, unifiable variants, corporate characters)
    if (charCode >= 0x2F800 && charCode <= 0x2FA1F)
        return true; // CJK Compatibility Ideographs Supplement (Unifiable variants)
    return false;
}

function length(str) {
    var len = 0;
    if (!str)
        return len;
    str = str.toString();
    len = str.length;
    for (var i = 0; i < str.length; i++) {
        var charCode = str.charCodeAt(i);
        if (isCJK(charCode))
            len += 1;
    }
    return len;
}

function alignLeft() {
    return '\x1b\x61\x00';
}

function alignCenter() {
    return '\x1b\x61\x01';
}

function alignRight() {
    return '\x1b\x61\x02';
}

function padding(str, num, padChar) {
    num = num || 41;
    padChar = padChar || ' ';
    var actualLength = length(str); // use function for CJK lengths, not str.length
    var targetLength = num - actualLength;
    var actualPadding = '';
    while (actualPadding.length < targetLength)
        actualPadding += padChar;
    return actualPadding;
}

function lpad(str, num, padChar) {
    // this won't really work if we intend to truncate CJK chars instead of padding them. fair warning.
    return (padding(str, num, padChar) + (str ? str.toString() : '')).substr(-num);
}

function rpad(str, num, padChar) {
    // this won't really work if we intend to truncate CJK chars instead of padding them. fair warning.
    return ((str ? str.toString() : '') + padding(str, num, padChar)).substr(0, num);
}

function cpad(str, totalLength, padChar) {
    var tempPadding = padding(str, totalLength, padChar);
    tempPadding = tempPadding.substr(0, length(tempPadding) / 2);
    return (tempPadding + (str ? str.toString() : '')).substr(0, totalLength);
}

function buildFullLine(str, width, charToPadWith, lineEnding) {
    str = str.toString();
    width = width || 41;
    charToPadWith = charToPadWith || ' ';
    lineEnding = lineEnding || '\n';
    return rpad(str, width, charToPadWith) + lineEnding;
}

var LINE_DEFAULTS = {
    KEY_WIDTH: 10,
    VALUE_WIDTH: 29,
    SEPARATOR: ': ',
    LINE_ENDING: '\n'
};

function buildLine(key, value, separator, keyWidth, valueWidth, lineEnding) {
    separator  = separator  || LINE_DEFAULTS.SEPARATOR;
    keyWidth   = keyWidth   || LINE_DEFAULTS.KEY_WIDTH;
    valueWidth = valueWidth || LINE_DEFAULTS.VALUE_WIDTH;
    lineEnding = lineEnding || LINE_DEFAULTS.LINE_ENDING;
    return lpad(key, keyWidth) + separator + rpad(value, valueWidth) + lineEnding;
}

var utils = {};
utils.receipts = {
    alignLeft     : alignLeft,
    alignCenter   : alignCenter,
    alignRight    : alignRight,
    isCJK         : isCJK,
    length        : length, // dangerous name?
    padding       : padding,
    rpad          : rpad,
    lpad          : lpad,
    cpad          : cpad,
    buildFullLine : buildFullLine,
    buildLine     : buildLine,
    defaults      : LINE_DEFAULTS
};

var slice = Array.prototype.slice;
var log = function() {
  var args = slice.call(arguments);
  console.log.apply(console, args);
};
log.debug = function() {
  if (log.debug.on)
    log.apply(null, slice.call(arguments));
};
utils.logging = {
    log: log
};

module.exports = utils;
