// CURRENTLY UNUSED AND WIP

var React = require('react');
var react = require('broccoli-react');
var traceur = require('broccoli-traceur');
var uglify = require('broccoli-uglify-js');

var tree = react('broctest', { extensions: ['js']});
tree = traceur(tree);

module.exports = tree;
