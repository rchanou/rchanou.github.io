<?php
use Clubspeed\Security\Authenticate;
use ClubSpeed\Enums\Enums as Enums;
use ClubSpeed\Logging\LogService as Log;

class Maintenance   {
     /**
     * @url POST /rebuild_indexes
     */
    public function rebuild_indexes() {
        if (!(Authenticate::localAccess() && Authenticate::publicAccess() ) && !Authenticate::privateAccess()  ){
            throw new RestException(403, "Invalid authorization");
		}
        try {
		     Log::info("Starting to rebuild indexes.", Enums::NSP_MAINTENANCE);
		     $sql_rebuild_indexes = <<<EOD
DECLARE @table NVARCHAR(MAX);
DECLARE @index NVARCHAR(MAX);
DECLARE @fragmentation FLOAT;
DECLARE @rebuilder NVARCHAR(MAX);
SET @table = '';
SET @index = '';
SET @fragmentation = 0.0;
DECLARE c3 SCROLL CURSOR FOR
SELECT
 dbtables.[name] as 'table', 
 dbindexes.[name] as 'index',
 indexstats.avg_fragmentation_in_percent as 'fragmentation'
FROM sys.dm_db_index_physical_stats (DB_ID(), NULL, NULL, NULL, NULL) AS indexstats
INNER JOIN sys.tables AS dbtables
 ON dbtables.[object_id] = indexstats.[object_id]
INNER JOIN sys.schemas AS dbschemas
 ON dbtables.[schema_id] = dbschemas.[schema_id]
INNER JOIN sys.indexes AS dbindexes
 ON dbindexes.[object_id] = indexstats.[object_id]
 AND indexstats.index_id = dbindexes.index_id
WHERE
 indexstats.database_id = DB_ID()
 AND dbindexes.[name] IS NOT NULL
 AND indexstats.index_type_desc = 'CLUSTERED INDEX'
 AND indexstats.avg_fragmentation_in_percent > 5.0
ORDER BY
 indexstats.avg_fragmentation_in_percent DESC
OPEN c3
FETCH FIRST FROM c3 INTO @table, @index, @fragmentation
WHILE @@FETCH_STATUS = 0
BEGIN
 SET @rebuilder = CASE WHEN @fragmentation < 30.0 -- per msdn's suggestions.
   THEN 'ALTER INDEX [' + @index + '] ON [' + @table + '] REORGANIZE'
   ELSE 'ALTER INDEX [' + @index + '] ON [' + @table + '] REBUILD'
 END;
   EXEC sp_executeSql @rebuilder
 FETCH NEXT FROM c3 INTO @table, @index, @fragmentation
END
CLOSE c3
DEALLOCATE c3
EOD;
        $db = $GLOBALS['db'];
        $db->exec($sql_rebuild_indexes);
		 Log::info("Finished rebuilding indexes.", Enums::NSP_MAINTENANCE);
        }
        catch (Exception $e) {
            Log::error($e->getMessage(), Enums::NSP_MAINTENANCE); 
            _error($e);
        }
    }
     /**
     * @url POST /update_statistics
     */
    public function update_statistics() {
        if (!(Authenticate::localAccess() && Authenticate::publicAccess() ) && !Authenticate::privateAccess()  ){
            throw new RestException(403, "Invalid authorization");
		}
        try {   
		Log::info("Starting to update statistics.", Enums::NSP_MAINTENANCE);
        $db = $GLOBALS['db'];        
        $sql_update_statistics = "EXEC sp_MSforeachtable @command1=\"PRINT '  ?' UPDATE STATISTICS ? WITH FULLSCAN, COLUMNS\"";
        $db->exec($sql_update_statistics);
		Log::info("Finished updating statistics.", Enums::NSP_MAINTENANCE);
        }
        catch (Exception $e) {
           Log::error($e->getMessage(), Enums::NSP_MAINTENANCE); 
           _error($e);
        }
    }
  
     private function _error($e) {
        if ($e instanceof RestException)
            throw $e;
        if ($e instanceof CSException)
            throw new RestException($e->getCode() ?: 412, $e->getMessage());
        throw new RestException(500, $e->getMessage());
    }
  
  
}
