CREATE VIEW dbo.OnlineBookingAvailability_V AS
WITH
ProductSpotsUsed AS (
    SELECT
        ob.HeatMainID AS HeatNo
        , ob.ProductsID
        , SUM(obr.Quantity) AS ProductSpotsUsed
    FROM
        dbo.OnlineBookingReservations obr
    INNER JOIN dbo.OnlineBookings ob
        ON ob.OnlineBookingsID = obr.OnlineBookingsID
    GROUP BY ob.HeatMainID, ob.ProductsID
),
ProductSpotsUsedGroupSum AS (
    SELECT
        ob.HeatMainID AS HeatNo
        , SUM(obr.Quantity) AS ProductSpotsUsedGroupSum
    FROM
        dbo.OnlineBookingReservations obr
    INNER JOIN dbo.OnlineBookings ob
        ON ob.OnlineBookingsID = obr.OnlineBookingsID
    GROUP BY ob.HeatMainID
),
HeatSpotsUsedPhysical AS (
    SELECT
        hd.HeatNo
        , COUNT(*) AS HeatSpotsUsedPhysical
    FROM dbo.HeatDetails hd
    INNER JOIN dbo.HeatMain hm
        ON hd.HeatNo = hm.HeatNo
    GROUP BY hd.HeatNo
),
HeatSpotsTotalOnline AS(
    SELECT
        ob.HeatMainID
        , SUM(ob.QuantityTotal) AS HeatSpotsTotalOnline
    FROM dbo.OnlineBookings ob
    GROUP BY ob.HeatMainID
),
HeatSpotsAvailableOnline AS (
    SELECT
        hm.HeatNo
        , CASE WHEN ob.QuantityTotal < hm.RacersPerHeat - hsup.HeatSpotsUsedPhysical
            THEN ob.QuantityTotal
            ELSE hm.RacersPerHeat - hsup.HeatSpotsUsedPhysical
        END AS HeatSpotsAvailableOnline
    FROM dbo.HeatMain hm
    INNER JOIN dbo.OnlineBookings ob
        ON hm.HeatNo = ob.HeatMainID
    INNER JOIN HeatSpotsUsedPhysical hsup
        ON hm.HeatNo = hsup.HeatNo
),
ReservationCalculations AS (
    SELECT
        hm.HeatNo
        , ob.OnlineBookingsID
        , ob.ProductsID -- could also used OnlineBookingReservationsID if we wanted
        , ISNULL(psu.ProductSpotsUsed, 0) AS ProductSpotsUsed
        , ISNULL(psugs.ProductSpotsUsedGroupSum, 0) As ProductSpotsUsedGroupSum
        , ISNULL(ob.QuantityTotal, 0) AS ProductSpotsTotal
        , hm.NumberOfReservation AS HeatSpotsUsedTemporary
        , ISNULL(hsup.HeatSpotsUsedPhysical, 0) AS HeatSpotsUsedPhysical
        , hm.RacersPerHeat AS HeatSpotsTotalActual
        , hsto.HeatSpotsTotalOnline
    FROM dbo.HeatMain hm
    INNER JOIN dbo.OnlineBookings ob -- keep the online bookings split into multiple rows
        ON ob.HeatMainID = hm.HeatNo
    LEFT OUTER JOIN ProductSpotsUsed psu
        ON psu.HeatNo = ob.HeatMainID
        AND psu.ProductsID = ob.ProductsID
    LEFT OUTER JOIN ProductSpotsUsedGroupSum psugs
        ON psugs.HeatNo = ob.HeatMainID
    LEFT OUTER JOIN dbo.Products p
        ON p.ProductID = ob.ProductsID
    LEFT OUTER JOIN HeatSpotsUsedPhysical hsup
        ON hsup.HeatNo = hm.HeatNo
    LEFT OUTER JOIN HeatSpotsTotalOnline hsto
        ON hsto.HeatMainID = hm.HeatNo
),
ReservationCalculations2 AS (
    SELECT
        rc.HeatNo
        , rc.OnlineBookingsID
        , rc.ProductsID
        , rc.HeatSpotsUsedPhysical
        , rc.HeatSpotsUsedTemporary
        , (rc.HeatSpotsUsedPhysical + rc.HeatSpotsUsedTemporary) AS HeatSpotsUsedActual
        , rc.HeatSpotsTotalActual
        , rc.ProductSpotsUsed
        , (rc.ProductSpotsTotal - rc.ProductSpotsUsed) AS ProductSpotsAvailable
        , rc.ProductSpotsTotal
        , rc.ProductSpotsUsedGroupSum
        , (rc.HeatSpotsTotalOnline - rc.ProductSpotsUsedGroupSum) AS HeatSpotsAvailableOnline
        , rc.HeatSpotsTotalOnline
        , (rc.HeatSpotsTotalActual - rc.HeatSpotsUsedPhysical - rc.HeatSpotsUsedTemporary - rc.ProductSpotsUsedGroupSum) AS HeatSpotsAvailableCombined -- takes into account the current online reservations as well
    FROM ReservationCalculations rc
),
ReservationCalculations3 AS (
    SELECT
        rc.HeatNo
        , rc.OnlineBookingsID
        , rc.ProductsID
        , rc.HeatSpotsUsedActual
        , rc.HeatSpotsAvailableCombined
        , rc.HeatSpotsTotalActual
        , rc.HeatSpotsAvailableOnline
        , CASE WHEN rc.HeatSpotsAvailableCombined < rc.HeatSpotsAvailableOnline
            THEN rc.HeatSpotsAvailableCombined
            ELSE rc.HeatSpotsAvailableOnline
        END AS HeatSpotsAvailableByLessThanComparison
        , rc.HeatSpotsTotalOnline
        , rc.ProductSpotsUsed
        , rc.ProductSpotsAvailable
        , rc.ProductSpotsTotal
    FROM ReservationCalculations2 rc
),
ReservationCalculations4 AS (
    SELECT
        rc.HeatNo
        , rc.OnlineBookingsID
        , rc.ProductsID
        , rc.HeatSpotsTotalActual
        , rc.HeatSpotsAvailableCombined
        , rc.HeatSpotsAvailableOnline
        , rc.ProductSpotsUsed
        -- ensure that we can never have productspotsavailable set to anything greater than the actual number of heat spots available
        , CASE WHEN rc.ProductSpotsAvailable < rc.HeatSpotsAvailableByLessThanComparison
            THEN rc.ProductSpotsAvailable
            ELSE rc.HeatSpotsAvailableByLessThanComparison
        END AS ProductSpotsAvailableOnline
        , rc.ProductSpotsTotal
    FROM ReservationCalculations3 rc
)
SELECT
    hm.HeatNo
    , ht.HeatTypeNo
    , ht.HeatTypeName AS HeatDescription
    , hm.ScheduledTime AS HeatStartsAt
    , hm.Finish AS HeatEndsAt
    , rc.OnlineBookingsID
    , ob.IsPublic
    , rc.ProductsID
    , p.ProductType
    , p.Description AS ProductDescription
    , p.Price1 -- price 1-5 exist -- used for membership price calculations
    , rc.HeatSpotsTotalActual
    , rc.HeatSpotsAvailableCombined
    , rc.HeatSpotsAvailableOnline
    , rc.ProductSpotsUsed
    , rc.ProductSpotsAvailableOnline
    , rc.ProductSpotsTotal
FROM HeatMain hm
INNER JOIN ReservationCalculations4 rc
    ON rc.HeatNo = hm.HeatNo
INNER JOIN HeatTypes ht
    ON ht.HeatTypeNo = hm.HeatTypeNo
INNER JOIN dbo.Products p
    ON rc.ProductsID = p.ProductID
INNER JOIN dbo.OnlineBookings ob
    ON rc.OnlineBookingsID = ob.OnlineBookingsID