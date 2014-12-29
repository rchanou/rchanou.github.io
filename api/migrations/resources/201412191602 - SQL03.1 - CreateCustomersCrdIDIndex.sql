-- Note: PDO does NOT accept GO statements, and CREATE INDEX must be the first statement in a batch

CREATE NONCLUSTERED INDEX IX_Customers_CrdID
ON dbo.Customers(
    CrdID ASC
)
WITH (
    PAD_INDEX = OFF
    , STATISTICS_NORECOMPUTE = OFF
    , SORT_IN_TEMPDB = OFF
    , DROP_EXISTING = OFF
    , ONLINE = OFF
    , ALLOW_ROW_LOCKS = ON
    , ALLOW_PAGE_LOCKS = ON
)
ON [PRIMARY]