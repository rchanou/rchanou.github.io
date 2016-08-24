"use strict";
/* eslint no-trailing-spaces:0 */
var gulp        = require('gulp');
var mocha       = require('gulp-mocha');
var fs          = require('fs');
var cached      = require('gulp-cached');
var eslint      = require('gulp-eslint');
var seq         = require('run-sequence');

var testDir = './test/';
var testGlob = testDir + '*.js';

var srcDir  = './lib/';
var srcGlob = [srcDir + '*.js', './gulpfile.js'];

var mochaOptions = {
    reporter  : 'spec'
};

var log = console.log.bind(console); // eslint-disable-line no-unused-vars

gulp.task('lint', function() {
    return gulp.src(srcGlob)
        .pipe(cached('src'))
        .pipe(eslint()) // eslint options in .eslintrc
        .pipe(eslint.format());
        //.pipe(eslint.failOnError()); // fail on src lints
});

gulp.task('test', function() {
    return gulp.src(testGlob)
        .pipe(cached('test'))
        .pipe(eslint())
        .pipe(eslint.format())
        .pipe(mocha(mochaOptions));
});

(function() {
    // single tests
    var rootDir = 'test/';
    fs.readdirSync(rootDir).forEach(function(fileName) {
        var taskName = fileName.slice(0, -3); // remove the file extension
        taskName = 'test-' + taskName.replace('.spec', '');
        gulp.task(taskName, function() {
            return gulp.src(rootDir + fileName)
                .pipe(cached('test'))
                .pipe(eslint())
                .pipe(eslint.format())
                .pipe(mocha(mochaOptions));
        });
    });
}());

gulp.task('watch', function() {
    gulp.watch(srcGlob).on('change', function(event) {
        /*
            we want to run unit tests whenever a file is saved,
            regardless of whether or not the code itself changed
            due to integration streamlining (code may have changed on the api)

            as such, if watch triggers for a file, remove it from the test cache
            even if no change actually occurred
        */
        if (cached.caches && cached.caches.src)
            delete cached.caches.src[event.path];
        return seq('lint');
    });

    gulp.watch(testGlob).on('change', function(event) {
        /*
            we want to run unit tests whenever a file is saved,
            regardless of whether or not the code itself changed
            due to integration streamlining (code may have changed on the api)

            as such, if watch triggers for a file, remove it from the test cache
            even if no change actually occurred
        */
        if (cached.caches && cached.caches.test)
            delete cached.caches.test[event.path];
        return seq('test');
    });
});

gulp.task('default', function(callback) {
    seq(
        'lint',
        'test',
        'watch',
        callback
    );
});
