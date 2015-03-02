This is the root folder of all React-based admin pages.

You should only make code edits to files in the src folder.

To activate automatic re-compilation of files from the src folder to the build folder:

1. Make sure you have Node/NPM properly installed and accessible from the command line.

2. Via a Bash or Windows shell, navigate to the folder that contains this README.

4. If you haven't already, enter "npm install" to fetch all dependencies. (You may have to set a python version environmental variable to "python2.7", and add a Visual Studio version parameter targeting the one you have installed, because the scripts for certain dependencies like d3 need this. I will add more specific instructions regarding this later. For now, just google any errors you come across for the fix.)

4. Enter "node webpack" to begin automatic transpilation as you save edits. Press Ctrl+C to stop it. You may have to press it multiple times.

5. Enter "node webpack deploy" to begin transpilation and deployment to clubspeedapps.

"node webpack" simply executes the "webpack.js" script in this folder, using node. If it finds the parameter "deploy", it will also deploy it to the clubspeedapps folder.

Open the "webpack.js" source to view, edit, and add inputs/entry points, watch transformations, and output files.
