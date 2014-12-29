-- Note: PDO does NOT accept GO statements, and CREATE VIEW must be the first statement in a batch

CREATE FUNCTION dbo.[Split] (
    @STRING NVARCHAR(MAX)
    , @DELIMITER NCHAR(1)
)
RETURNS @VALUES TABLE ([Value] NVARCHAR(MAX))
AS
BEGIN
    IF (@STRING IS NULL)
        RETURN;
    IF (LTRIM(RTRIM(@STRING)) = '')
        RETURN;
    IF (@DELIMITER IS NULL OR @DELIMITER = ' ')
        SET @DELIMITER = ',';

    DECLARE @POS INT;
    DECLARE @VALUE NVARCHAR(MAX);

    SET @STRING = LTRIM(RTRIM(@STRING)) + @DELIMITER;
    SET @POS = CHARINDEX(@DELIMITER, @STRING, 1);

    BEGIN
        WHILE @POS > 0
        BEGIN
            SET @VALUE = LTRIM(RTRIM(LEFT(@STRING, @POS-1)));
            IF @VALUE != ''
                INSERT INTO @VALUES (Value) VALUES (@Value) -- o.O
            SET @STRING = RIGHT(@STRING, LEN(@STRING) - @POS);
            SET @POS = CHARINDEX(@DELIMITER, @STRING, 1);
        END;
    END;
    RETURN;
END;