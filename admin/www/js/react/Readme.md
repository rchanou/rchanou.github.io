This is the root folder of all React-based admin pages.

You should only make code edits to files in the src folder.

To activate automatic re-compilation of files from the src folder to the build folder:

1. Make sure you have an **ES6-compatible** version of IOJS/Node/NPM properly installed and accessible from the command line.

2. Via a Bash or Windows shell, navigate to the folder that contains this README.

4. If you haven't already, enter `npm install` to fetch all dependencies. You may have to set a python version environmental variable to `python2.7`, and add a Visual Studio version parameter targeting the one you have installed, because the scripts for certain dependencies like d3 need this. So you may have to enter something like `export PYTHON=python2.7`, then `npm --msvs_version=2012 install`. (Google for exact details on these issues.)

5. Enter `node --harmony webpack` to begin automatic transpilation of dev version as you save edits. Press Ctrl+C to stop it.

6. Enter `node --harmony webpack release` to begin transpilation of release version.

`node --harmony webpack` simply executes the `webpack.js` script in this folder, using node, with the harmony flag set to enable ES6 features. If it finds the parameter `release`, it will also deploy it to the clubspeedapps folder.

Open the `webpack.js` source to view, edit, and add inputs/entry points, watch transformations, and output files.
