<?php

if(!function_exists('mime_content_type')) {

    function mime_content_type($filename) {

        $mime_types = array(
            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
        );

        $ext = strtolower(array_pop(explode('.',$filename)));
        if (array_key_exists($ext, $mime_types)) {
						return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
						return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
    }
}

/**
 * Proxy images as base64
 * Created to get around CORS problems from iPad registration
 *
 * Usage: http://vm-122.clubspeedtiming.com/api/shot/shot.php?base64=192.168.111.133/image.jpg
 */
$whitelist = array('image.jpg','jpeg.cgi','IMAGE.JPG','out.jpg'); // Could be turned into an array of regex's later?
$timeoutInSeconds = 5; // Timeout if image cannot be reached -- in seconds

if(isset($_REQUEST['base64'])) {
	// Set timeout default
	ini_set('default_socket_timeout', $timeoutInSeconds);
	
	// Append http (if not provided)
	$url = (false === strpos($_REQUEST['base64'], '://')) ? 'http://' . $_REQUEST['base64'] : $_REQUEST['base64'];

	// Parse URL
	$imagePath = parse_url($url, PHP_URL_PATH);

	// Enforce Whitelist
    $fileName = basename($url);
    $fileNameIsInWhiteList = false;
    foreach($whitelist as $currentNameInWhitelist)
    {
        if ($fileName === $currentNameInWhitelist)
        {
            $fileNameIsInWhiteList = true;
            break;
        }
    }
	if (!$fileNameIsInWhiteList) exit('Image URL not supported.');
	
	// Get image data or return an error.
	$imageData = file_get_contents($url);
	if(!$imageData) exit('Image not found.');
	
	// Format the image SRC: data:{mime};base64,{data};
	$base64Data = 'data: ' . mime_content_type($imagePath) . ';base64,' . base64_encode($imageData);
	
	// Return base64'd image
	if(isset($_REQUEST['callback'])) { // Handle JSONP requests
    $base64Data = array('image' => $base64Data);
    die("{$_REQUEST['callback']}(" . json_encode($base64Data) . ");");
	} else { // Non JSONP requests
    die($base64Data);
	}
}

/**
 * CONFIGURATION SETTINGS
 */

$cache_life = 120; //caching time, in seconds
$download = false;
$here = dirname(__FILE__) . DIRECTORY_SEPARATOR;
$bin_files = $here . 'phantomjs' . DIRECTORY_SEPARATOR;
$jobs = $here . 'jobs' . DIRECTORY_SEPARATOR;
$cache = $here . 'cache' . DIRECTORY_SEPARATOR;
$w = 400;
$h = 300;
$delay = 500;
$refresh = false;
$supported_types = array('png', 'jpg');
$type = 'png';

// Some maximums to help prevent Denial of Service attacks
$maximum_delay = 5000;
$maximum_width = 2000;
$maximum_height = 2500;


/**
 * CODE (NO CONFIG BELOW HERE)
 */

// Ensure we've been given a URL to load
if (!isset($_REQUEST['url'])) {
    exit('No url given');
}
$url = $_REQUEST['url'];
$url = trim(urldecode($url));
if ($url == '') {
    exit('No url given');
}

// Ensure HTTP is included
if (!stristr($url, 'http://') and !stristr($url, 'https://')) {
    $url = 'http://' . $url;
}

// Ensure there is a host to access
$url_segs = parse_url($url);
if (!isset($url_segs['host'])) {
    exit('Invalid host ' . $url_segs['host']);
}

// Ensure we're accessing only files on THIS host
if ($_SERVER['SERVER_NAME'] !== $url_segs['host']) {
    exit('Can only access files hosted on: ' . $_SERVER['SERVER_NAME'] . ', not ' . $url_segs['host']);
}

// Create directories if they do not exist
if (!is_dir($jobs)) {
    mkdir($jobs);
    file_put_contents($jobs . 'index.php', '<?php exit(); ?>');
}
if (!is_dir($cache)) {
    mkdir($cache);
    file_put_contents($cache . 'index.php', '<?php exit(); ?>');
}

// Set intelligent defaults/sanitize values
if (isset($_REQUEST['w'])) {
    $w = intval($_REQUEST['w']);
}

if (isset($_REQUEST['h'])) {
    $h = intval($_REQUEST['h']);
}

if($w > $maximum_width || $h > $maximum_height) {
		exit('Maximum image dimensions exceeded, please specify a smaller image.');
}

if (isset($_REQUEST['clipw'])) {
    $clipw = intval($_REQUEST['clipw']);
}

if (isset($_REQUEST['cliph'])) {
    $cliph = intval($_REQUEST['cliph']);
}

if (isset($_REQUEST['download'])) {
    $download = $_REQUEST['download'];
}

if (isset($_REQUEST['delay'])) {
    $delay = $_REQUEST['delay'];
}

if($delay > $maximum_delay) {
		exit('Maximum delay time exceeded, please use a shorter delay.');
}

// Setup proper Content-Type and file extension
if (isset($_REQUEST['type']) && in_array($_REQUEST['type'], $supported_types)) {
    switch($_REQUEST['type']) {
			case 'jpg':
				$extension = 'jpg';
				$content_type = 'image/jpeg';
				break;
			case 'png':
			default:
				$extension = 'png';
				$content_type = 'image/png';
				break;
		}
} else {
	$extension = 'png';
	$content_type = 'image/png';
}

$url = strip_tags($url);
$url = str_replace(';', '', $url);
$url = str_replace('"', '', $url);
$url = str_replace('\'', '/', $url);
$url = str_replace('<?', '', $url);
$url = str_replace('<?', '', $url);
$url = str_replace('\077', ' ', $url);

$screen_file = $url_segs['host'] . crc32($url) . '_' . $w . '_' . $h . '.' . $extension;
$cache_job = $cache . $screen_file;

// Remove image files older than $cahche_life
$files = glob($cache . "*");
$time  = time();

foreach ($files as $file)
	if (is_file($file))
		if ($time - filemtime($file) >= $cache_life)
			unlink($file);

// Remove image files older than $cahche_life
$files = glob($jobs . "*");
$time  = time();

foreach ($files as $file)
	if (is_file($file))
		if ($time - filemtime($file) >= $cache_life)
			unlink($file);

// Decide if we should re-run the job
if (is_file($cache_job)) {
    $filemtime = @filemtime($cache_job); // returns FALSE if file does not exist
    if (!$filemtime or (time() - $filemtime >= $cache_life)) {
        $refresh = true;
    }
}

// Need to re-enable this?
//$url = escapeshellcmd($url);

if (!is_file($cache_job) or $refresh == true) {
    $src = "

    var page = require('webpage').create();

    page.viewportSize = { width: {$w}, height: {$h} };

    ";

    if (isset($clipw) && isset($cliph)) {
        $src .= "page.clipRect = { top: 0, left: 0, width: {$clipw}, height: {$cliph} };";
    }

    $src .= "

    page.open('{$url}', function () {
				setTimeout(function() {
								page.render('{$screen_file}');
								phantom.exit();
				}, {$delay});
    });


    ";

    $job_file = $jobs . $url_segs['host'] . crc32($src) . '.js';
    file_put_contents($job_file, $src);

    $exec = $bin_files . 'phantomjs ' . $job_file;

    $escaped_command = escapeshellcmd($exec);

    exec($escaped_command);

    if (is_file($here . $screen_file)) {
        rename($here . $screen_file, $cache_job);
    }
}


if (is_file($cache_job)) {
    if ($download != false) {
        $file = $cache_job;
        $file_name=basename($file);
        header("Content-disposition: attachment; filename={$file_name}");
        header("Content-type: {$content_type}");
        readfile($file);
    } else {
        $file = $cache_job;
        header('Content-Type:' . $content_type);
        header('Content-Length: ' . filesize($file));
        readfile($file);
		}
}