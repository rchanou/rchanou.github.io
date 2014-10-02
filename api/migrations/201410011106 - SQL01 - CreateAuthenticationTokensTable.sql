USE ClubspeedV8;
SET XACT_ABORT ON; -- automatic rollback on run-time error
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE; -- ensure full isolation (probably not necessary, but safe)
BEGIN TRANSACTION;

DECLARE @UseDrop BIT;
SET @UseDrop = 0; -- set to 1 to drop the table if it exists

IF (@UseDrop = 1 AND OBJECT_ID('dbo.AuthenticationTokens', 'U') IS NOT NULL)
    DROP TABLE dbo.AuthenticationTokens

IF NOT EXISTS (
    SELECT *
    FROM INFORMATION_SCHEMA.TABLES t
    WHERE t.TABLE_NAME = 'AuthenticationTokens'
)
BEGIN
    CREATE TABLE dbo.AuthenticationTokens (
        AuthenticationTokensID INT IDENTITY(1,1) NOT NULL
        , CustomersID INT NOT NULL
        , RemoteUserID NVARCHAR(255) NOT NULL
        , TokenType NVARCHAR(255) NOT NULL
        , Token NVARCHAR(MAX) NOT NULL
        , CreatedAt DATETIME2 DEFAULT SYSDATETIME()
        , ExpiresAt DATETIME2
        , Meta NVARCHAR(MAX)
        , CONSTRAINT PK_AuthenticationTokens
            PRIMARY KEY CLUSTERED (AuthenticationTokensId)
        , CONSTRAINT FK_AuthenticationTokens_Customers
            FOREIGN KEY (CustomersID)
            REFERENCES dbo.CUSTOMERS (CustID)
            ON UPDATE CASCADE
            ON DELETE CASCADE
    )
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The Customer ID'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = AuthenticationTokens
        , @level2type = 'Column', @level2name = CustomersID
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The unique user ID provided by the external token system'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = AuthenticationTokens
        , @level2type = 'Column', @level2name = RemoteUserID
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The string representation of the type of token'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = AuthenticationTokens
        , @level2type = 'Column', @level2name = TokenType
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The authentication token'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = AuthenticationTokens
        , @level2type = 'Column', @level2name = Token
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The timestamp when the token was created'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = AuthenticationTokens
        , @level2type = 'Column', @level2name = CreatedAt
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'The timestamp when the token is set to expire'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = AuthenticationTokens
        , @level2type = 'Column', @level2name = ExpiresAt
    EXEC sp_addextendedproperty
        @name = 'MS_Description'
        , @value = 'A JSON representation of any extended metadata for the token type'
        , @level0type = 'Schema', @level0name = dbo
        , @level1type = 'Table',  @level1name = AuthenticationTokens
        , @level2type = 'Column', @level2name = Meta
END;

COMMIT;