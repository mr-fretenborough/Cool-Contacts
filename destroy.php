<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Poor man's debugger
        returnStatus("before logic");

        // Grab the data and initiate the connection
        $body = json_decode(file_get_contents('php://input'), true);
        $conn = new mysqli("localhost", "DBADMIN", "DBADMIN", "ContactBook");

        /*
        The input is to be of the form:
            {
                "FirstName":"",
                "LastName":"",
                "Email":"",
                "PhoneNumber":"",
                "UserID":""
            }
        The output will be of the form:
            {
                "status":""
            }
        */

        // Validate connection
        if ($conn->connect_error) {
            returnStatus($conn->connect_error);
        } else {
            // Seed the SQL Query with basic format
            $query = $conn.prepare(" DELETE FROM ContactBook.Contacts WHERE FirstName = ? AND LastName = ? AND Email = ? AND PhoneNumber = ? AND UserID = ?");
            // Grab JSON and populate the SQL Query
            $query->bind_param("ssssi",
                $body["FirstName"],
                $body["LastName"],
                $body["Email"],
                $body["PhoneNumber"],
                $body["UserID"]
            );
            // Execute the SQL Query
            $query->execute();
            // Status will be the number of rows deleted. This should be either a 1 for success or 0 for failure
            $status = $query->get_result()->fetch_assoc();

            // Report the status
            if ($status == 1) {
                returnStatus("SUCCESS: Contact deletion completed successfully.");
            } elseif ($status == 0) {
                returnStatus("ERROR: No contacts were deleted.");
            } else {
                returnStatus("FAILURE: More than one contact was deleted.");
            }
        }

        // Let 'em go
        $query->close();
        $conn->close();

        returnStatus("just testing this.");
    }

    function returnJSON( $object )
    {
        header('Content-type: application/json');
        echo $object;
    }

    function returnStatus( $status )
    {
        $format = '{"status":"' . $status . '"}';
        returnJSON( $format );
    }
?>
