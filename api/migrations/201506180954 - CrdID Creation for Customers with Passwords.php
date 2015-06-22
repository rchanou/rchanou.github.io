<?php

/**
 * Creates a random CrdID (with no duplicates allowed) for any Customers that have a non-null Hash and have a CrdID <= 0
 *
 * This translates to creating a card ID for customers that registered via /mobile and /booking (and thus have a password)
 * prior to those applications having been updated to assign a CrdID upon customer creation.
 *
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

$conn = new PDO( "sqlsrv:server=(local) ; Database=ClubSpeedV8", "", "");
$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

echo "Searching for customers that registered via /mobile or /booking and do not yet have a CrdID...<p>";

$stmt = $conn->prepare("select CustID from Customers where Hash is not NULL and (CrdID <= 0 or CrdID is null)");
$stmt->execute();
$customers = $stmt->fetchAll();
$numOfCustomers = count($customers);
if ($numOfCustomers <= 0)
{
    echo "No customers found that need this migration.";
}
else
{
    echo "Generating unique CrdIDs for $numOfCustomers customers...<p>";
    foreach ($customers as $customer)
    {
        $customerId = $customer['CustID'];
        $nextCardId = generateCardId();

        $stmt = $conn->prepare("UPDATE Customers SET CrdID = :CrdID WHERE CustID = :CustID");
        $stmt->bindParam(':CrdID', $nextCardId);
        $stmt->bindParam(':CustID', $customerId);
        $stmt->execute();
    }

}

die("<p>Done!");

//Generates a CrdID, ensuring it is unique in the entire Customers table.
function generateCardId() {
    global $conn;

    $cardId = -1;
    while($cardId < 0) {
        $tempCardId = mt_rand(1000000000, 2147483647);
        $customersWithSameIdAlready = array();
        $stmt = $conn->prepare("select CustID from Customers where CrdID = :CrdID");
        $stmt->bindParam(':CrdID', $tempCardId);
        $stmt->execute();
        $customersWithSameIdAlready = $stmt->fetchAll();
        if (empty($customersWithSameIdAlready))
            $cardId = $tempCardId;
    }
    return $cardId;
}