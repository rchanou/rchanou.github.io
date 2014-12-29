/*
 USAGE: in the command line, in this file's folder, enter "node webpack"
        to deploy, enter "node webpack deploy"
        to build React component demos, enter "node webpack demo"
 */

var webpack = require('webpack');
var _ = require('lodash');
var fs = require('fs-extra');


var MAIN_PATHS = ['mobileApp/menuItems', 'booking/manage'];

var DEFAULT_MODULE = {
  loaders: [
    {
      test: /^(?!.*(bower_components|node_modules))+.+\.js$/,
      loader: 'traceur?runtime'
    },
    {
      test: /^(?!.*(bower_components|node_modules))+.+\.js$/,
      loader: 'jsx-loader?harmony'
    }
  ]
};


//buildType is third command line "word" (first two are "node webpack")
var buildType = process.argv.slice(2)[0];


if (buildType === 'demo'){
  fs.readdir('demo', function(err, files){
    if (err){
      console.log('FOLDER READ ERROR', err);
      return;
    }

    files.forEach(function(folder){
      var demoConfig = {
        entry: './demo/' + folder + '/main.js',
        output: {
          filename: './demo/' + folder + '/main-bundle.js'
        },
        module: DEFAULT_MODULE,
        resolve: {
          extensions: ['', '.js', '.jsx']
        }
      };

      var demoCompiler = webpack(demoConfig);
      demoCompiler.watch(200, function(err, stats){
        if (stats.hasErrors()){
          console.log('*ERROR*', stats.toJson().errors);
        } else {
          console.log('Demo build "' + folder + '" done!');
        }
      });
    });
  });

  return;
}


MAIN_PATHS.forEach(function(path){
  var sharedConfig = {
    entry: './src/' + path + '/main.js',
    output: {
      filename: './build/' + path + '/main.min.js'
    },
    module: DEFAULT_MODULE,
    resolve: {
      extensions: ['', '.js', '.jsx']
    }
  };

  var minifiedConfig = _.extend(
    _.cloneDeep(sharedConfig),
    {
      devtool: 'source-map',
      plugins: [
        new webpack.optimize.UglifyJsPlugin()
      ]
    }
  );

  var releaseConfig = _.extend(
    _.cloneDeep(sharedConfig),
    {
      output: {
        filename: './../../../../clubspeedapps/admin/www/js/react/build/' + path + '/main.min.js'
      },
      plugins: [
        new webpack.optimize.UglifyJsPlugin()
      ]
    }
  );

  // initialize webpack instances

  var devCompiler = webpack(sharedConfig);
  var minifiedCompiler = webpack(minifiedConfig);
  var releaseCompiler = webpack(releaseConfig);


  // get first command line arg after node and filename, and start corresponding watch process

  switch(buildType){
    case 'deploy':
      devCompiler.watch(200, function(err, stats){
        var seconds = (stats.endTime - stats.startTime) / 1000;
        console.log('Dev build done: ' + seconds + 's, at ' + new Date() + ' for ' + path);
        console.log('Started minified builds...');

        if (stats.hasErrors()){
          console.log('ERROR!', err, stats.toJson().errors);
        } else {
          minifiedCompiler.run(function(err, stats){
            if (stats.hasErrors()){
              console.log('DEV MINIFIER ERROR', stats.toJson().errors);
              return;
            }

            var seconds = (stats.endTime - stats.startTime) / 1000;
            console.log('Min dev build done: ' + seconds + 's, at ' + new Date() + ' for ' + path);

            fs.copy(
              'build/' + path + '/main.min.js',
              '../../../../clubspeedapps/admin/www/js/react/build/' + path + '/main.min.js',
              function(err){
                if (err) return console.err('*ERROR DEPLOYING*', err);
                console.log(path + ' deployed to clubspeedapps!');
              }
            );

            fs.copy(
              'build/' + path + '/main.min.js.map',
              '../../../../clubspeedapps/admin/www/js/react/build/' + path + '/main.min.js.map',
              function(err){
                if (err) return console.err('Error deploying source map: ', err);
              }
            );
          });

          /*releaseCompiler.run(function(err, stats){
            if (stats.hasErrors()){
              console.log('RELEASE BUILD ERROR', stats.toJson().errors);
              return;
            }

            var seconds = (stats.endTime - stats.startTime) / 1000;
            console.log('Release build done: ' + seconds + 's, at ' + new Date() + ' for ' + path);
          });*/
        }
      });
      break;

    default:
      devCompiler.watch(200, function(err, stats){
        if (!stats){
          console.log('Dev build completed.');
          return;
        }

        if (stats.hasErrors()){
          console.log('I AM ERROR!', stats.toJson().errors);
        }

        var seconds = (stats.endTime - stats.startTime) / 1000;
        console.log('Dev build done: ' + seconds + 's, at ' + new Date() + ' for ' + path);
      });
  }
});
