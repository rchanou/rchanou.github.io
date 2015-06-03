USE ClubspeedV8;
SET XACT_ABORT ON; -- automatic rollback on run-time error
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE; -- ensure full isolation (probably not necessary, but safe)
BEGIN TRANSACTION;

DECLARE @UseDrop BIT;
SET @UseDrop = 0; -- set to 1 to drop the table if it exists

IF (@UseDrop = 1 AND OBJECT_ID('dbo.TransactionReferences', 'U') IS NOT NULL)
    DROP TABLE dbo.TransactionReferences

IF NOT EXISTS (
    SELECT *
    FROM INFORMATION_SCHEMA.TABLES t
    WHERE t.TABLE_NAME = 'TransactionReferences'
)
BEGIN
    CREATE TABLE dbo.TransactionReferences (
          [TransactionReferencesID]     INT IDENTITY(1,1)   NOT NULL
        , [CheckID]                     INT                 NOT NULL
        -- , [TransactionID]               NVARCHAR(MAX)       NOT NULL
        , [TransactionReference]        NVARCHAR(MAX)       NOT NULL
        , [Amount]                      DECIMAL(18,2)       NOT NULL
        , [Currency]                    NVARCHAR(50)        NOT NULL
        -- , [Created]                     DATETIME            DEFAULT (GETDATE())
        , CONSTRAINT PK_TransactionReferencesID
            PRIMARY KEY CLUSTERED (TransactionReferencesID)
        , CONSTRAINT FK_TransactionReferences_Checks
            FOREIGN KEY (CheckID)
            REFERENCES dbo.Checks (CheckID)
            ON UPDATE CASCADE
            ON DELETE CASCADE
    )
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The TransactionReference ID'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = TransactionReferences
        , @level2type = 'Column', @level2name = [TransactionReferencesID];
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The ID for the check which the transaction is referencing'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = TransactionReferences
        , @level2type = 'Column', @level2name = [CheckID];
    -- EXEC sp_addextendedproperty
    --     @name = 'MS_Description'
    --     , @value = 'The ID the transaction which was provided to the payment processor. Will most likely be the CheckID.'
    --     , @level0type = 'Schema', @level0name = dbo
    --     , @level1type = 'Table',  @level1name = TransactionReferences
    --     , @level2type = 'Column', @level2name = [TransactionID];
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The transaction reference provided by the payment processor'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = TransactionReferences
        , @level2type = 'Column', @level2name = [TransactionReference];
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The payment amount for the transaction'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = TransactionReferences
        , @level2type = 'Column', @level2name = [Amount];
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The currency type for the transaction'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = TransactionReferences
        , @level2type = 'Column', @level2name = [Currency];
    -- EXEC sp_addextendedproperty
    --     @name = 'MS_Description'
    --     , @value = 'The ID for the check which the transaction is referencing'
    --     , @level0type = 'Schema', @level0name = dbo
    --     , @level1type = 'Table',  @level1name = TransactionReferences
    --     , @level2type = 'Column', @level2name = [Created];
END;

COMMIT;

SELECT *
FROM dbo.TransactionReferences tr