<?php
/**
 * This script creates/updates the UpdateCheckDetailsGpoints stored procedure.
 */

require_once('../config.php');
require_once('../vendors/autoload.php');
require_once('../ClubSpeed/ClubSpeedLoader.php');
$_REQUEST['debug'] = true;

$dropProcedure = <<<EOD
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[UpdateCheckDetailsGpoints]') AND type in (N'P', N'PC'))

DROP PROCEDURE [dbo].[UpdateCheckDetailsGpoints]
EOD;

$createProcedure = <<<EOD
CREATE PROCEDURE [dbo].[UpdateCheckDetailsGpoints]
@CheckDetailID int,
@Points money,
@CustID int
AS
BEGIN
    UPDATE CheckDetails
    SET G_Points = @Points,
        G_CustID = @CustID
    WHERE CheckDetailID = @CheckDetailID
END
EOD;

	error_reporting(E_ALL);
	ini_set('display_errors', '1');

$conn = new PDO( "sqlsrv:server=(local) ; Database=ClubSpeedV8", "", "");
$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

// Execute statement
$result = $conn->query($dropProcedure);

$result = $conn->query($createProcedure);

// Confirm success
die('Done.');