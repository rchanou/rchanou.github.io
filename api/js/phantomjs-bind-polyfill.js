    
// polyfill for phantomJS Function.prototype.bind
// see https://github.com/ariya/phantomjs/issues/10522 for more info
// original code in comments from eschwartz
(function() {
    var bind,
        slice = [].slice,
        proto = Function.prototype,
        has = function(obj, key) {
            return isFunction(obj.key);
        },
        isFunction = function(o) {
            return typeof o == 'function';
        };

    // check for missing features
    if (!has(proto, 'bind')) {
        // adapted from Mozilla Developer Network example at
        // https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/Function/bind
        bind = function bind(obj) {
            var args = Array.prototype.slice.call(arguments),
                self = this,
                nop = function() {},
                bound = function() {
                    return self.apply(this instanceof nop ? this : (obj || {}), args.concat(slice.call(arguments)));
                };
            nop.prototype = this.prototype || {}; // Firefox cries sometimes if prototype is undefined
            bound.prototype = new nop();
            return bound;
        };
        proto.bind = bind;
    }
})();