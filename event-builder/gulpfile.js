var gulp        = require('gulp');
var mocha       = require('gulp-mocha');
var jshint      = require('gulp-jshint');
var taskListing = require('gulp-task-listing');
var fs          = require('fs');
var path        = require('path');

var jshConfig = {
    reporter: 'jshint-stylish',
    options: {
        eqnull   : true, // for intended single equal comparisons to null (!= null). see: http://www.jshint.com/docs/options/#eqnull
        laxcomma : true, // for comma-first style warnings. see: http://www.jshint.com/docs/options/#laxcomma
        laxbreak : true  // for line break warnings (usually with strings). see: http://www.jshint.com/docs/options/#laxbreak
    }
}

gulp.task('help', taskListing);

gulp.task('lint', function() {
    return gulp.src([
        './lib/*.js' // allow tests to lint themselves
        , './test/*.js'
    ])
    .pipe(jshint(jshConfig.options))
    .pipe(jshint.reporter(jshConfig.reporter, {verbose: true}));
});

gulp.task('test', ['lint'], function() {
    return gulp.src([
        './test/*.js'
    ])
    .pipe(jshint(jshConfig.options))
    .pipe(jshint.reporter(jshConfig.reporter))
    .pipe(mocha({reporter: 'spec'}));
});

(function(undefined) {
    // batched receipt tests
    var testDir = 'test/';
    var testFiles = fs.readdirSync(testDir);
    var receiptFiles = [];
    testFiles.forEach(function(fileName, key, arr) {
        var filePath = path.parse(fileName);
        if (filePath.name.slice(0, 7) === 'receipt')
            receiptFiles.push(testDir + filePath.base);
    });
    gulp.task('test-receipts', function() {
        return gulp.src(receiptFiles)
            .pipe(jshint(jshConfig.options))
            .pipe(jshint.reporter(jshConfig.reporter))
            .pipe(mocha({ reporter: 'spec' }));
    });
}());

(function(undefined) {
    // single tests
    var rootDir = 'test/';
    var files = fs.readdirSync(rootDir);
    fs.readdirSync('test/').forEach(function(fileName, key, arr) {
        var taskName = fileName.slice(0, -3); // remove the file extension
        taskName = 'test-' + taskName.replace('.spec', '');
        gulp.task(taskName, function() {
            return gulp.src(rootDir + fileName)
                .pipe(jshint(jshConfig.options))
                .pipe(jshint.reporter(jshConfig.reporter))
                .pipe(mocha({ reporter: 'spec' }));
        });
    });
}());

gulp.task('default', [
    'test'
]);