-- Note: PDO does NOT accept GO statements, and CREATE INDEX must be the first statement in a batch
CREATE NONCLUSTERED INDEX [IX_RacingData_AutoNo]
ON [dbo].[RacingData] ([AutoNo])
INCLUDE ([HeatNo],[LTime],[TimeStamp],[LapNum]);