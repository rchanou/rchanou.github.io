REM git fetch origin master
REM git reset --hard HEAD
REM git clean -df

copy "admin\www\web.config" "admin\www\web.config.bak" /Y
copy "api\web.config" "api\web.config.bak" /Y
copy "booking\www\web.config" "booking\www\web.config.bak" /Y
copy "cs-registration\www\web.config" "cs-registration\www\web.config.bak" /Y

git pull https://clubspeed-api-updater:,git2014!GusGus,@github.com/clubspeed/clubspeedapps.git

copy "admin\www\web.config.bak" "admin\www\web.config" /Y
copy "api\web.config.bak" "api\web.config" /Y
copy "booking\www\web.config.bak" "booking\www\web.config" /Y
copy "cs-registration\www\web.config.bak" "cs-registration\www\web.config" /Y