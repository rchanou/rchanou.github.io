This is the root folder of all React-based admin pages.

You should only make code edits to files in the src folder.

To activate automatic re-compilation of files from the src folder to the build folder:

1. Make sure you have Node/NPM properly installed and accessible from the command line.

2. Via a Bash or Windows shell, navigate to the folder that contains this README.

4. If you haven't already, enter "npm install" to fetch all dependencies.

4. Enter "node webpack" to begin automatic compilation as you save edits. Press Ctrl+C to stop it.

5. Enter "node webpack deploy" to begin automatic deployment and compilation.

"node webpack" simply has node run the code in "webpack.js" in this folder. If it finds the parameter "deploy", it will also deploy it to the clubspeedapps folder.

Open the "webpack.js" source to view, edit, and add inputs/entry points, watch transformations, and output files.
