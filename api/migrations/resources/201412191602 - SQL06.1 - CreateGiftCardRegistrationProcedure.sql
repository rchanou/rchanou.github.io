CREATE PROCEDURE [dbo].[GiftCardRegistration]
      @CardsCSV NVARCHAR(MAX) = NULL
    , @DesiredMoney MONEY = NULL
    , @DesiredPoints MONEY = NULL
    , @UserID INT = 1
    , @Notes NVARCHAR(255) = 'Gift Card Registration'
    , @GiftCardName NVARCHAR(255) = 'Gift Card'
    , @IPAddress NVARCHAR(255) = ''
AS
BEGIN
/*
    dbo.GiftCardRegistration

    To be used to both register lists of new gift cards,
    as well as reset lists of existing gift cards
    to a supplied amount of points or money.

    Works with both monetary gift cards as well as
    point gift cards either simultaneously or separately.

    Steps:
    1. Accept a list of cards in a string formatted as CSV.
    2. Split the list of cards and determine which cards exist, and which don't.
    3. Create customer records for the list of cards which do not exist.
    4. If money is passed, reset the combined list's gift card balances to 0,
        then insert gift card history records to move up to the desired gift card balance.
    5. If points are passed, reset the combined list's point balances to 0,
        then insert point history to move up to the desired point balance.
    6. If replication is being used at the current location,
        then insert trigger logs for all of the new customer records.
*/
SET XACT_ABORT ON; -- automatic rollback on run-time error
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE; -- ensure full isolation (note that this is NOT functional with dbo.Customers due to the lack of a true primary key)
SET NOCOUNT ON;
BEGIN TRANSACTION;

---- TEST DATA
--DECLARE @CardsCSV VARCHAR(MAX); SET @CardsCSV = '123,456,1450,1451,1452,1453,1454,1456,1457';
--DECLARE @GiftCardName VARCHAR(255); SET @GiftCardName = 'Gift Card'
--DECLARE @Notes VARCHAR(255); SET @Notes = 'Gift Card Register Test'
--DECLARE @UserID INT; SET @UserID = 1;
--DECLARE @DesiredMoney MONEY; SET @DesiredMoney = 25.00;
--DECLARE @DesiredPoints MONEY; SET @DesiredPoints = 100;
--DECLARE @IPAddress VARCHAR(255); SET @IPAddress = '127.0.0.1';

DECLARE @CARDS TABLE(
    Idx INT
    , CrdID INT
    , DesiredMoney MONEY
    , DesiredPoints MONEY
    , CurrentMoney MONEY
    , CurrentPoints MONEY
    , ExistingCustID INT
    , NewCustID INT
);
DECLARE @LocationID INT;
DECLARE @IsReplicable BIT;
DECLARE @CustID INT;

IF (@GiftCardName IS NULL OR LTRIM(RTRIM(@GiftCardName)) = '')
    SET @GiftCardName = 'Gift Card';

IF (@Notes IS NULL OR LTRIM(RTRIM(@Notes)) = '')
    SET @Notes = 'Gift Card Registration';

IF (@IPAddress IS NULL OR LTRIM(RTRIM(@IPAddress)) = '')
    SET @IPAddress = '';

SET @IsReplicable = (
    SELECT TOP 1 cp.Settingvalue
    FROM dbo.ControlPanel cp
    WHERE cp.SettingName = 'ReplicateCustomerInfo'
);
SET @LocationID = (
    SELECT TOP 1 cp.Settingvalue
    FROM dbo.ControlPanel cp
    WHERE cp.SettingName = 'LocationID'
);
SET @CustID = (
    SELECT ISNULL(MAX(c.CustID),(@LocationID*1000000))+1 
    FROM dbo.Customers c
    WHERE c.CustID BETWEEN ((@LocationID*1000000)+1) AND ((@LocationID+1)*1000000)
);

-- Build the table variable up with the data we need.
-- Ensure that we determine which cards already exist,
-- and which cards require new customer records to be inserted.
WITH CARDS_CTE AS (
    SELECT
        ROW_NUMBER() OVER (ORDER BY cst.CustID, c.Value) - 1 AS Idx
        , cst.CustID
        , c.Value AS CrdID
    FROM
        dbo.Split(@CardsCSV, DEFAULT) c
    LEFT OUTER JOIN dbo.Customers cst
        ON c.Value = cst.CrdID
)
INSERT INTO @CARDS (Idx, CrdID, ExistingCustID, NewCustID, DesiredMoney, DesiredPoints, CurrentMoney, CurrentPoints)
SELECT
      c.Idx
    , c.CrdID
    , c.CustID
    , CASE WHEN c.CustID IS NULL
        THEN @CustID + c.Idx
        ELSE NULL
    END
    , @DesiredMoney
    , @DesiredPoints
    , ISNULL(gcbv.[Money], 0)
    , ISNULL(gcbv.Points, 0)
FROM CARDS_CTE c
LEFT OUTER JOIN dbo.Customers cst
    ON c.CrdID = cst.CrdID
LEFT OUTER JOIN dbo.GiftCardBalance_V gcbv
    ON c.CrdID = gcbv.CrdID
;

INSERT INTO Customers(
    CustID
    , CrdID
    , Gender
    , FName
    , LName
    , RacerName
    , Custom4
    , LastVisited
    , TotalVisits
    , BirthDate
    , GeneralNotes
    , PhoneNumber
    , [Address]
    , City
    , Zip
    , EmailAddress
    , AccountCreated
    , Custom2
    , Custom3
    , OriginalID
    , IsGiftCard
)
-- Note that these fields/defaults are based off of the old dbo.InsertGiftCards stored proc
SELECT
    c.NewCustID
    , c.CrdID
    , '0' -- gender
    , @GiftCardName -- first name
    , c.CrdID -- last name
    , @GiftCardName + ' ' + CAST(c.CrdID AS VARCHAR(16)) -- racer name
    , '' -- custom4
    , GETDATE() -- last visited
    , '0' -- totalvisits
    , NULL -- birthdate
    , @Notes
    , '' -- phonenumber
    , '' -- address
    , '' -- city
    , '' -- zip
    , NULL -- email address
    , GETDATE() -- account created
    , '' -- custom2
    , '' -- custom3
    , '1' -- originalId ??
    , 1 -- IsGiftCard
FROM @CARDS c
WHERE
    c.ExistingCustID IS NULL

UPDATE c
SET
    c.ExistingCustID = c.NewCustID
    --, c.NewCustID = NULL -- save the null values so we know to put these in the trigger logs
FROM @CARDS c
WHERE
    c.NewCustID IS NOT NULL

-- Zero out the points balance
INSERT INTO dbo.PointHistory(
    CustID
    , UserID
    , ReferenceID
    , PointAmount
    , [Type]
    , PointDate
    , PointExpDate
    , Notes
    , IsManual
)
SELECT
    c.ExistingCustID
    , @UserID
    , c.CrdID
    , -1 * c.CurrentPoints
    , 5 -- HistoryType.Substract [sic]
    , GETDATE()
    , '9999-12-31T00:00:00'
    , @Notes
    , 0
FROM @CARDS c
WHERE
        c.CurrentPoints != 0
    AND c.CurrentPoints != c.DesiredPoints
    AND c.DesiredPoints IS NOT NULL

-- Increase points balance to desired points
INSERT INTO dbo.PointHistory(
    CustID
    , UserID
    , ReferenceID
    , PointAmount
    , [Type]
    , PointDate
    , PointExpDate
    , Notes
    , IsManual
)
SELECT
    c.ExistingCustID
    , @UserID
    , c.CrdID
    , c.DesiredPoints
    , 4 -- HistoryType.Add
    , GETDATE()
    , '9999-12-31T00:00:00'
    , @Notes
    , 0
FROM @CARDS c
WHERE
        c.DesiredPoints IS NOT NULL
    AND c.CurrentPoints != c.DesiredPoints
    AND c.DesiredPoints != 0

-- Zero out the gift card balance
INSERT INTO dbo.GiftCardHistory (
      CustID
    , UserID
    , Points
    , [Type]
    , Notes
    , CheckID
    , CheckDetailID
    , IPAddress
    , TransactionDate
)
SELECT
    c.ExistingCustID
    , @UserID
    , -1 * c.CurrentMoney
    , 9 -- GiftCardHistory.VoidSell (???) -- there is no subtract enum for gift card history
    , @Notes
    , 0 -- check id
    , NULL -- check detail id
    , @IPAddress
    , GETDATE() -- transaction date
FROM @CARDS c
WHERE
        c.CurrentMoney != 0
    AND c.CurrentMoney != c.DesiredMoney
    AND c.DesiredMoney IS NOT NULL

-- Increase gift card balance to desired money
INSERT INTO dbo.GiftCardHistory (
      CustID
    , UserID
    , Points
    , [Type]
    , Notes
    , CheckID
    , CheckDetailID
    , IPAddress
    , TransactionDate
)
SELECT
    c.ExistingCustID
    , @UserID
    , c.DesiredMoney
    , 1 -- GiftCardHistory.TransferIn (what the old stored proc was using)
    , @Notes
    , 0 -- check id
    , NULL -- check detail id
    , @IPAddress
    , GETDATE() -- transaction date
FROM @CARDS c
WHERE
    c.DesiredMoney IS NOT NULL
    AND c.CurrentMoney != c.DesiredMoney
    AND c.DesiredMoney != 0

-- Insert new customers into TriggerLogs, where applicable
IF @IsReplicable = 1
BEGIN
    INSERT INTO dbo.TriggerLogs (CustID, LastUpdated, TableName, [Type], Deleted)
    SELECT
        c.NewCustID
        , GETDATE()
        , 'Customers'
        , 'Insert/Updated'
        , 0
    FROM @CARDS c
    WHERE -- or do we want ExistingCustID? what all gets replicated? if it's just customers, this should be fine.
        c.NewCustID IS NOT NULL
END

--SELECT
--    c.*
--    , gcbv.Balance AS GiftCardBalance
--    , pbv.Balance AS PointBalance
--FROM @CARDS c
--LEFT OUTER JOIN dbo.GiftCardBalance_V gcbv
--    ON c.CrdID = gcbv.CrdID
--LEFT OUTER JOIN dbo.PointBalance_V pbv
--    ON c.CrdID = pbv.CrdID

COMMIT;

END;