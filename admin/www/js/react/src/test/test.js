var assert = require("assert");
//var React = require('react/addons');
var BookingAdmin = require('../../src/booking/manage/bookingadmin.js');

describe('Array', function(){
  describe('#indexOf()', function(){
    it('returns -1 when the value is not present', function(){ 
      assert.equal(-1, [1,2,3].indexOf(5));
      assert.equal(-1, [1,2,3].indexOf(0));
    });
  });
});

describe('BookingAdmin', function(){
	it('does stuff', function(){
		assert.equal(1, 1);
	});
});