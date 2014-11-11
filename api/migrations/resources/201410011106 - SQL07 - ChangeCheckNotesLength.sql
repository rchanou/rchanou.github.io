
-- Script to alter Check.Notes from NVARCHAR(50) to NVARCHAR(MAX) for OnlineBooking requirements.

-- Note that in order to alter the length of Check.Notes, we need to ensure that no constraints are tied to it.
-- As we typically have a default constraint, we must drop it, alter the column length, then re-add it.
-- This query uses dynamically generated SQL from the sys tables. If errors occur, the transaction should be rolled-back.


USE ClubspeedV8;
SET XACT_ABORT ON; -- automatic rollback on run-time error
SET TRANSACTION ISOLATION LEVEL SERIALIZABLE; -- ensure full isolation (probably not necessary, but safe)
BEGIN TRANSACTION;

DECLARE @NotesConstraint varchar(255);
DECLARE @NotesConstraintDefinition varchar(255);
DECLARE @sql nvarchar(max);

IF EXISTS (
    SELECT *
    FROM INFORMATION_SCHEMA.COLUMNS c
    WHERE 
            c.COLUMN_NAME = 'Notes'
        AND c.TABLE_NAME = 'CHECKS'
        AND c.CHARACTER_MAXIMUM_LENGTH < 255
        AND c.CHARACTER_MAXIMUM_LENGTH <> -1 -- -1 represents (MAX)
)
BEGIN

WITH CTE as (
    SELECT
        t.name AS TableName
        , c.name AS ColumnName
        , d.name AS ConstraintName
        , d.[definition] AS [Definition]
    FROM sys.default_constraints d
    INNER JOIN sys.columns c
        ON d.parent_column_id = c.column_id
        AND d.parent_object_id = c.object_id
    INNER JOIN sys.tables t
        ON t.object_id = c.object_id
    WHERE
        d.type_desc = 'DEFAULT_CONSTRAINT'
        AND d.is_ms_shipped = 0
        AND t.name = 'Checks'
        AND c.name = 'Notes'
)
SELECT
      @NotesConstraint              = c.ConstraintName
    , @NotesConstraintDefinition    = c.[Definition]
FROM CTE c

SET @sql = N'
ALTER TABLE [dbo].[Checks] DROP CONSTRAINT ' + @NotesConstraint + '
ALTER TABLE [dbo].[Checks] ALTER COLUMN [Notes] nvarchar(MAX);
ALTER TABLE [dbo].[Checks] ADD  CONSTRAINT ' + @NotesConstraint + ' DEFAULT ' + @NotesConstraintDefinition + ' FOR [Notes]
'
EXEC sp_executesql @sql

END;

COMMIT;