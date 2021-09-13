<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Grab the data and initiate the connection
        $body = json_decode(file_get_contents('php://input'), true);
        $conn = new mysqli("localhost", "DBADMIN", "DBADMIN", "ContactBook");

        // Validate connection
        if ($conn->connect_error) {
            returnStatus($conn->connect_error);
        } else {
            // Seed the SQL Query with basic format
            $query = $conn.prepare(" INSERT INTO ContactBook.Contacts
                FirstName,
                LastName,
                Email,
                PhoneNumber,
                UserID
            ) VALUES (
                ?, ?, ?, ?, ?
            )");
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
            if ($status) {
                returnStatus("SUCCESS: Contact creation completed successfully.");
            } else {
                returnStatus("FAILURE: Contact creation failed to succeed.");
            }
        }

        // Let 'em go
        $query->close();
        $conn->close();
    }

    function returnStatus($msg) {
        $format = '{"status":"' . $msg . '"}';
        // Report the status of the operation
        header('Content-type: application/json');
        echo $format;
    }
?>