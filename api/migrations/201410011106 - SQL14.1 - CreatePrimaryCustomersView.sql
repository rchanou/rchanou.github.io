-- Note: PDO does NOT accept GO statements, and CREATE VIEW must be the first statement in a batch

CREATE VIEW [dbo].[PrimaryCustomers_V] AS
WITH PrimaryCustomer AS (
    SELECT
        ROW_NUMBER() OVER (
            PARTITION BY
                c.FName -- consider FName, LName, and BirthDate to be the grouping columns
                , c.LName
                , c.BirthDate
                -- do we also need email in here?
            ORDER BY
                CASE WHEN (c.Password IS NULL OR LEN(LTRIM(RTRIM(c.Password))) = 0) THEN 1 ELSE 0 END -- push null or empty passwords to the bottom
                , Points DESC
                , c.TotalRaces DESC
                , c.LastVisited DESC
                , c.RPM DESC
        ) AS Rank
        , c.CustID
        , c.FName
        , c.LName
        , c.BirthDate
        , ISNULL(c.EmailAddress, '') AS EmailAddress
        , c.RPM AS ProSkill
    FROM CUSTOMERS c
    LEFT OUTER JOIN (
        SELECT
            p.CustID
            , SUM(ISNULL(p.PointAmount, 0)) as Points
        FROM POINTHISTORY p
        WHERE
            p.PointExpDate IS NULL
            OR p.PointExpDate >= GETDATE()
        GROUP BY p.CustID
    ) AS p ON p.CustID = c.CustID
    WHERE
        c.Deleted = 0
)
SELECT
      c.CustID
    , c.FName
    , c.LName
    , c.BirthDate
    , c.EmailAddress
    , c.ProSkill
FROM PrimaryCustomer c
WHERE c.Rank = 1