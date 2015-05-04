USE [ClubspeedV8]
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER OFF
GO

ALTER PROCEDURE [dbo].[GetFB_Customer]
(@CustID int) 
AS 
SELECT FB_CustId,FB_Customers_New.CustId,UId,Access_token,AllowEmail,AllowPost 
FROM [FB_Customers_New]
INNER JOIN [Customers] ON FB_Customers_New.CustID=Customers.CustID 
  WHERE FB_Customers_New.CustID = @CustID AND Enabled='True' AND Customers.Privacy4='True' AND 1 = 0