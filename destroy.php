<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Initialize server-specific variables
        $server = "localhost";
        $username = "root";
        $password = "TMs7aedJuMNX";
        $database = "ContactBook";

        // Parse the POST request
        $body = json_decode(file_get_contents('php://input'), true);
        $FirstName = $body["FirstName"];
        $LastName = $body["LastName"];
        $Email = $body["Email"];
        $PhoneNumber = $body["PhoneNumber"];
        $UserID = $body["UserID"];


        // Initiate the server connection
        $conn = new mysqli($server, $username, $password, $database);

        /**************************************
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
        ***************************************/

        // Validate connection
        if ($conn->connect_error) {
            returnStatus($conn->connect_error);
        } else {
            // Poor man's debugger
            returnStatus("Before prepared statement.");

             // Seed the SQL Query with basic format
             $stmt = $conn.prepare(" DELETE FROM Contacts
                WHERE FirstName = ?
                AND LastName = ?
                AND Email = ?
                AND PhoneNumber = ?
                AND UserID = ?
            ");

            // Poor man's debugger
            returnStatus("After prepared statement.");

             // Check if the query preparation completed successfully
             if ( false === $stmt ) {
                returnStatus('FAILURE: Query preparation failed.');
                exit();
            }

             // Grab JSON and populate the SQL Query
            $bind = $stmt->bind_param("ssssi",
                $FirstName,
                $LastName,
                $Email,
                $PhoneNumber,
                $UserID
            );
             // Execute the SQL Query
             $exec = $stmt->execute();

             // Status will be the number of rows deleted. This should be either a 1 for success or 0 for failure
             $status = $stmt->get_result()->fetch_assoc();

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
        $stmt->close();
        $conn->close();
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
