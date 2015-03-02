/*
 USAGE: in the command line, in this file's folder, enter "node webpack"
        to deploy, enter "node webpack deploy"
        to build the standalone React component test demos, enter "node webpack demo"
        to build a specific project, enter it's position in the MAIN_PATHS array as the fourth argument
          e.g. "node webpack _ 2" to do a dev build of 'booking/manage'
          or   "node webpack deploy 1" to do a build and deploy of 'mobileApp/menuItems'
 */

var webpack = require('webpack');
var _ = require('lodash');


var MAIN_PATHS = [
  'speedScreen/manage',
  'mobileApp/menuItems',
  'booking/manage',
  'speedText/logTable',
  'facebook/logTable'
];

var DEFAULT_MODULE = {
  loaders: [
    {
      test: /^(?!.*(bower_components|node_modules))+.+\.js$/,
      loader: 'traceur?runtime'
    },
    {
      test: /^.*js-csp.*csp.*.js$/,
      loader: 'traceur?runtime'
    },
    {
      test: /^(?!.*(bower_components|node_modules))+.+\.js$/,
      loader: 'jsx-loader?harmony'
    }/*,
    {
      test: /^.*react-magic-move.*.js$/,
      loader: 'jsx-loader?harmony'
    }*/
  ]
};

//buildType determined from third command line "word" (first two are "node webpack")
var buildType = process.argv[2];

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


/*
  Description of the build process for each project in MAIN_PATHS:
    First, an unminified dev version is built. This should take less than 2 seconds, allowing for a fast feedback loop for local testing.
    Next, a minified dev version and, if you entered "node webpack deploy", a minified release version, are built and deployed to their respective output paths.
    Let the minification step complete before committing any "main.min.js" files, because it's not actually minified before then.
*/


MAIN_PATHS.forEach(function(path, i){
  if (typeof process.argv[3] !== 'undefined' && ~~process.argv[3] !== i){
    return;
  }
  console.log('Building ' + path + '...');

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

  var releaseModule = _.cloneDeep(DEFAULT_MODULE);
  releaseModule.loaders.push({
    test: /(.*)\.js/,
    loader: 'strip-loader?strip[]=assert,strip[]=console.assert,strip[]=console.debug,strip[]=console.dir,strip[]=console.log,strip[]=console.table,strip[]=console.warn'
  });
  var releaseConfig = _.extend(
    _.cloneDeep(sharedConfig),
    {
      output: {
        filename: './../../../../clubspeedapps/admin/www/js/react/build/' + path + '/main.min.js'
      },
      module: releaseModule,
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
        if (stats){
          var seconds = (stats.endTime - stats.startTime) / 1000;
          console.log('Dev build done: ' + seconds + 's, at ' + new Date() + ' for ' + path);
          console.log('Started minified builds...');
        } else {
          console.log('Dev build completed.');
        }

        if (stats && stats.hasErrors()){
          console.log('ERROR!', err, stats.toJson().errors);
        } else {
          minifiedCompiler.run(function(err, stats){
            if (!stats){
              console.log('Min dev build completed.');
            }

            if (stats.hasErrors()){
              console.log('DEV MINIFIER ERROR', stats.toJson().errors);
              return;
            }

            var seconds = (stats.endTime - stats.startTime) / 1000;
            console.log('Min dev build done: ' + seconds + 's, at ' + new Date() + ' for ' + path);
          });

          releaseCompiler.run(function(err, stats){
            if (!stats){
              console.log('Release build completed.');
            }

            if (stats.hasErrors()){
              console.log('RELEASE MINIFIER ERROR', stats.toJson().errors);
              return;
            }

            var seconds = (stats.endTime - stats.startTime) / 1000;
            console.log('Release build done: ' + seconds + 's, at ' + new Date() + ' for ' + path);
          });
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
