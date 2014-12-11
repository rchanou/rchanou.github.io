var gulp = require('gulp');
var react = require('gulp-react');
var browserify = require('browserify');
var traceur = require('gulp-traceur');
var es = require('event-stream');
var concat = require('gulp-concat');
var transform = require('vinyl-transform');
var uglify = require('gulp-uglify');

gulp.task('default', function(){
  var start = new Date();
  console.log('Build started at ' + start);

  var browserified = transform(function(filename){
    var b = browserify(filename);
    b.transform('reactify', { es6: true });
    return  b.bundle();
  });

  var mainFilePipeTasks = [
    browserified,
    react({ harmony: true }),
    traceur()
  ];

  var mainFilePipe = gulp.src('src/mobileApp/menuItems/main.js');

  mainFilePipeTasks.forEach(function(task){
    task.on('error', function(e){
      console.log(e);
    });

    mainFilePipe = mainFilePipe.pipe(task);
  });

  var build = es.concat(
    gulp.src('node_modules/traceur/bin/traceur-runtime.js'),
    mainFilePipe
  )
  .pipe(concat('main.min.js'))
  //.pipe(sourcemaps.init())
  .pipe(uglify())
  //.pipe(sourcemaps.write('./'))
  .pipe(gulp.dest('./build/mobileApp/menuItems'));

  build.on('end', function(){
    var end = new Date();
    console.log('Build completed at ' + end);
    console.log('Build took ' + ((end.getTime() - start.getTime()) / 1000) + ' seconds.');
  });

  // following line deploys to main release branch. comment out to not do this
  build.pipe(gulp.dest('./../../../../clubspeedapps/admin/www/js/react/build/mobileApp/menuItems'));
});

gulp.task('watch', ['default'], function(){
  gulp.watch('src/**/*.js', ['default']);
});
