var React = require('react/addons');
var d3 = require('d3');
var _ = require('lodash');

var ReactTransition = React.createClass({
  getDefaultProps: function () {
    return {
      ease: 'cubic-out',
      duration: 400,
      onFinish: function(){}
    };
  },

  startAnimation: function () {
    var start = new Date().getTime(),
        end = start + parseInt(this.props.duration, 10),
        finished = false,
        that = this;

    function animate () {
      if (finished || that.stopAnimation) {
        that.props.onFinish();
        return;
      }

      var now = t();

      if (now > 1) { now = 1; finished = true; }

      that.animate(now);
      window.requestAnimationFrame(animate);
    }

    function t () {
      var now = new Date().getTime();

      return (now - start) / (end - start) || 0;
    }

    animate();
  },

  componentWillUnmount: function () {
    // stops the animation in progress
    this.stopAnimation = true;
  },

  getInitialState: function () {
    return this.props;
  },

  componentWillReceiveProps: function (newProps) {
    var that = this;

    var interpolators = {};

    _(newProps).each(function (value, propName) {
      if (propName === 'element' || propName === 'component' || propName === 'children' || propName === 'ease' || propName === 'duration' || propName.match(/^on(.+)/)) { return; }
      interpolators[propName] = d3.interpolate(that.state[propName], newProps[propName]);
    });

    this.interpolators = interpolators;

    this.startAnimation();
  },

  animate: function (t) {
    var newState = {},
        ease = d3.ease(this.props.ease);

    _(this.interpolators).each(function (interpolator, propName) {
      newState[propName] = interpolator(ease(t));
    });

    this.setState(newState);
  },

  render: function () {
    return React.createElement(this.props.element || this.props.component || 'div', this.state, this.props.children);
  }
});

module.exports = ReactTransition;
