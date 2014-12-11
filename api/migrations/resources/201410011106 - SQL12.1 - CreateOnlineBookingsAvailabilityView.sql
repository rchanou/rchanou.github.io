CREATE VIEW dbo.OnlineBookingAvailability_V AS
WITH
ProductSpotsUsed AS ( -- get the spots used, split by product and heat
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
ProductSpotsUsedGroupSum AS ( -- get the spots used, split only by heat (multiple online booking containers for the same heat)
    SELECT
        ob.HeatMainID AS HeatNo
        , SUM(obr.Quantity) AS ProductSpotsUsedGroupSum
    FROM
        dbo.OnlineBookingReservations obr
    INNER JOIN dbo.OnlineBookings ob
        ON ob.OnlineBookingsID = obr.OnlineBookingsID
    GROUP BY ob.HeatMainID
),
ProductSpotsUsedNonPermanentGroupSum AS (
    -- side note, when a status is set to permanent, it should NOT be used for calculations with the heat,
    -- as it is technically a representation (and duplicate) of the dbo.HeatMain.NumberOfReservation increment or dbo.HeatDetails record.
    -- awkward, but this must be done in order to determine that heat placement came from OnlineBooking, which is necessary to decrement available slots.
    SELECT
        ob.HeatMainID AS HeatNo
        , SUM(obr.Quantity) AS ProductSpotsUsedNonPermanentGroupSum
    FROM
        dbo.OnlineBookingReservations obr
    INNER JOIN dbo.OnlineBookings ob
        ON ob.OnlineBookingsID = obr.OnlineBookingsID
    INNER JOIN dbo.OnlineBookingReservationStatus obrs
        ON obrs.OnlineBookingReservationStatusID = obr.OnlineBookingReservationStatusID
    WHERE obrs.Status != 'PERMANENT'
    GROUP BY ob.HeatMainID
),
HeatSpotsUsedPhysical AS ( -- get the heat spots used by sp_intake / purchased online bookings
    SELECT
        hd.HeatNo
        , COUNT(*) AS HeatSpotsUsedPhysical
    FROM dbo.HeatDetails hd
    INNER JOIN dbo.HeatMain hm
        ON hd.HeatNo = hm.HeatNo
    GROUP BY hd.HeatNo
),
HeatSpotsTotalOnline AS( -- get the total availability for bookings (including multiple booking containers)
    SELECT
        ob.HeatMainID
        , SUM(ob.QuantityTotal) AS HeatSpotsTotalOnline
    FROM dbo.OnlineBookings ob
    GROUP BY ob.HeatMainID
),
HeatSpotsAvailableOnline AS ( -- calculate how many spots are still available across all booking containers, DONT count permanent
    SELECT
        hm.HeatNo
        , CASE WHEN ob.QuantityTotal - ISNULL(psugs.ProductSpotsUsedNonPermanentGroupSum, 0) < hm.RacersPerHeat - hsup.HeatSpotsUsedPhysical
            THEN ob.QuantityTotal - ISNULL(psugs.ProductSpotsUsedNonPermanentGroupSum, 0)
            ELSE hm.RacersPerHeat - hsup.HeatSpotsUsedPhysical
        END AS HeatSpotsAvailableOnline
    FROM dbo.HeatMain hm
    INNER JOIN dbo.OnlineBookings ob
        ON hm.HeatNo = ob.HeatMainID
    LEFT OUTER JOIN HeatSpotsUsedPhysical hsup
        ON hm.HeatNo = hsup.HeatNo
    LEFT OUTER JOIN ProductSpotsUsedNonPermanentGroupSum psugs
        ON psugs.HeatNo = hm.HeatNo
),
ReservationCalculations AS (
    SELECT
        hm.HeatNo
        , ob.OnlineBookingsID
        , ob.ProductsID -- could also used OnlineBookingReservationsID if we wanted
        , ISNULL(psu.ProductSpotsUsed, 0) AS ProductSpotsUsed
        , ISNULL(psugs.ProductSpotsUsedGroupSum, 0) AS ProductSpotsUsedGroupSum
        , ISNULL(psunpgs.ProductSpotsUsedNonPermanentGroupSum, 0) AS ProductSpotsUsedNonPermanentGroupSum
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
    LEFT OUTER JOIN ProductSpotsUsedNonPermanentGroupSum psunpgs
        ON psunpgs.HeatNo = ob.HeatMainID
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
        , rc.HeatSpotsUsedTemporary -- the NumberOfReservations 
        , (rc.HeatSpotsUsedPhysical + rc.HeatSpotsUsedTemporary) AS HeatSpotsUsedActual -- the combination of HeatDetails and NumberOfReservations
        , rc.HeatSpotsTotalActual -- the total spots defined on HeatMain
        , rc.ProductSpotsUsed
        , (rc.ProductSpotsTotal - rc.ProductSpotsUsed) AS ProductSpotsAvailable
        , rc.ProductSpotsTotal
        , rc.ProductSpotsUsedGroupSum
        , (rc.HeatSpotsTotalOnline - rc.ProductSpotsUsedGroupSum) AS HeatSpotsAvailableOnline
        , rc.HeatSpotsTotalOnline
        , (rc.HeatSpotsTotalActual - rc.HeatSpotsUsedPhysical - rc.HeatSpotsUsedTemporary - rc.ProductSpotsUsedNonPermanentGroupSum) AS HeatSpotsAvailableCombined -- takes into account the current online reservations as well
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
        , CASE 
            WHEN rc.HeatSpotsAvailableCombined < 0 THEN 0   -- potential loss of data: if we must, keep this at the negative numbers
            WHEN rc.HeatSpotsAvailableOnline < 0 THEN 0     -- both can be negative for when sp_intake directly modifies racers/reservations while online reservation is temporary (in the kart)
            WHEN rc.HeatSpotsAvailableCombined < rc.HeatSpotsAvailableOnline THEN rc.HeatSpotsAvailableCombined
            ELSE rc.HeatSpotsAvailableOnline
        END AS HeatSpotsAvailableOnline -- ensure that the online availability is never greater than the physical availability
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
        , rc.HeatSpotsUsedActual AS HeatSpotsUsed
        , CASE WHEN rc.HeatSpotsAvailableOnline < 0
            THEN 0
            ELSE rc.HeatSpotsAvailableOnline
        END AS HeatSpotsAvailableOnline
        , rc.ProductSpotsUsed
        -- ensure that we can never have productspotsavailable set to anything greater than the actual number of heat spots available
        , CASE WHEN rc.ProductSpotsAvailable < rc.HeatSpotsAvailableOnline
            THEN rc.ProductSpotsAvailable
            ELSE rc.HeatSpotsAvailableOnline
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
    , rc.HeatSpotsUsed
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