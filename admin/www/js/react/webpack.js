/*
 USAGE: in the command line, in this file's folder, enter "node --harmony webpack <build mode> <project index in MAIN_PATHS>"
        e.g. "node --harmony webpack _ 2" to do a dev build of 'booking/manage'
        or   "node --harmony webpack release 1" to do a build and deploy of 'mobileApp/menuItems'

        This will initiate one of the following modes:
        "dev" mode: every time a project file is changed, a dev version of the project will be transpiled to the
          output location. The Dev version will be unminified, and will set the __DEV__ variable for that project to true
          (this variable can be used to, for example, use the 122 API for testing).
        "release" mode: every time a project file is changed, a dev build will be done. After the dev build is done,
          a release build will be completed (takes longer) that


        Ctrl+C to exit
 */

'use strict';

var childProcess = require('child_process');
var webpack = require('webpack');
var _ = require('lodash');
var csp = require('js-csp');


var mainPathFragments = [
  'speedScreen/manage',
  'mobileApp/menuItems',
  'booking/manage',
  //'speedText/logTable',
  //'facebook/logTable'
];


var app = {

  main(opts){
    var baseOpts = {
      mode: null,
      pathIndex: 0
    };

    opts = _.extend(baseOpts, opts);

    var pathFragment = mainPathFragments[opts.pathIndex];

    var channels = {
      testStepReady: csp.chan(csp.buffers.sliding(1)),
      minBuildReady: csp.chan(csp.buffers.sliding(1)),
      minBuildDone: csp.chan()
    };


    var baseConfig = {
      entry: './src/' + pathFragment + '/main.js',
      output: {
        filename: './build/' + pathFragment + '/main.min.js'
      },
      module: {
        loaders: [
          {
            test: /^(?!.*(bower_components|node_modules))+.+\.js$/,
            loader: 'babel-loader?stage=0&optional=runtime&cacheDirectory'
          },
          {
            test: /^.*js-csp.*csp.*.js$/,
            loader: 'babel-loader?stage=0&optional=runtime&cacheDirectory'
          }
        ]
      },
      plugins: [new webpack.DefinePlugin({ __DEV__: true })],
      resolve: {
        extensions: ['', '.js', '.jsx']
      }
    };

    var devCompiler = webpack(baseConfig);

    var watchCommand = app.get.watchCommand({
      compiler: devCompiler,
      callback: function(err, stats){
        if (stats){
          if (stats.hasErrors()){
            console.log('ERROR', err, stats.toJson().errors);
          } else {
            var seconds = (stats.endTime - stats.startTime) / 1000;
            console.log('Dev build done: ' + seconds + 's, at ' + new Date() + ' for ' + pathFragment);

            csp.putAsync(channels.testStepReady);
            csp.putAsync(channels.minBuildReady);
          }
        } else {
          console.log('Dev build completed. Build process interrupted for unknown reasons.');
        }
      }
    });


    if (opts.mode === 'release'){

      var releaseConfig = _.merge(_.cloneDeep(baseConfig), {
        plugins: [
          new webpack.optimize.UglifyJsPlugin(),
          new webpack.DefinePlugin({ __DEV__: false }),
          new webpack.DefinePlugin({
            "process.env": {
              NODE_ENV: JSON.stringify("production")
            }
          })
        ]
      });
      releaseConfig.module.loaders.push({
        test: /(.*)\.js/,
        loader: 'strip-loader?strip[]=assert,strip[]=console.assert,strip[]=console.debug,strip[]=console.dir,strip[]=console.log,strip[]=console.table,strip[]=console.warn'
      });

      var releaseCompiler = webpack(releaseConfig);

      var runReleaseBuildCommand = app.get.runCommand({
        compiler: releaseCompiler,
        callback(err, stats){
          csp.putAsync(channels.minBuildDone, { err, stats });
        }
      });

      csp.go(function* (){
        while (true){
          yield channels.minBuildReady;
          console.log('Creating release version...');

          app.execute(runReleaseBuildCommand);
          var doneEvent = yield channels.minBuildDone;

          if (doneEvent.stats){
            if (doneEvent.stats.hasErrors()){
              console.log('RELEASE ERROR', err, stats.toJson().errors);
            } else {
              var seconds = (doneEvent.stats.endTime - doneEvent.stats.startTime) / 1000;
              console.log('Release build done: ' + seconds + 's, at ' + new Date() + ' for ' + pathFragment);
            }
          } else {
            console.log('Release build completed. Build process interrupted for unknown reasons.');
          }
        }
      });

    } else {

      /*
      //Grr, jest doesn't work with node v0.12 or iojs 1.2.0

      csp.go(function* (){
        while (true){
          yield channels.testStepReady;
          console.log('Running jest tests...');

          childProcess.exec('jest', function (error, stdout, stderr) {
            if (error) {
              console.log('ERROR:', error);
            }
            console.log(stdout);
          });
        }
      });

      */
    }


    app.execute(watchCommand);
  },

  execute(command){ // of course, we could call the command directly, but this is to explicitly call out side-effecting operations (as opposed to pure functions)
    return command();
  },

  get: {

    watchCommand(opts){
      if (!opts.compiler){
        throw new Exception('compiler required, yo');
      }

      var baseOpts = {
        delay: 200,
        callback: function(err, stats){
          console.log(err, stats);
        }.bind(this)
      };

      opts = _.merge(baseOpts, opts);

      return opts.compiler.watch.bind(opts.compiler, opts.delay, opts.callback);
    },

    runCommand(opts){
      if (!opts.compiler){
        throw new Exception('compiler required, yo');
      }

      var baseOpts = {
        callback(err, stats){
          console.log(err, stats);
        }
      };

      opts = _.merge(baseOpts, opts);

      return opts.compiler.run.bind(opts.compiler, opts.callback);
    }

  }

};


//build process determined from third command line "word" (first two are "node webpack")

if (process.argv[2] === 'list' || process.argv[2] === 'help'){

  mainPathFragments.forEach(function(fragment, i){
    console.log(i + ': ' + fragment);
  });

  console.log('"node --harmony webpack _ <project #>" for dev, or "node --harmony webpack release <project #>" for release');

} else if (typeof process.argv[3] === 'undefined'){

  mainPathFragments.forEach(function(fragment, i){
    app.main({
      mode: process.argv[2],
      pathIndex: i
    });
  });

} else {

  app.main({
    mode: process.argv[2],
    pathIndex: process.argv[3]
  });

}
