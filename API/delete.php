<?php
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


    if ($_SERVER['REQUEST_METHOD']==='POST') {
        // Pull the JSON object from the POST request
        $body = json_decode(file_get_contents('php://input'), true);

        // Parse the JSON object
        $first = $body["FirstName"];
        $last = $body["LastName"];
        $email = $body["Email"];
        $number = $body["PhoneNumber"];
        $user = $body["UserID"];

        // Establish the connection to the MySQL Instance
        $conn = new mysqli("localhost", "DBADMIN", "DBADMIN", "ContactBook");

        // Validate the connection
        if ( $conn->connect_error ) {
            returnStatus( $conn->connect_error );
        } else {
            // Create the query template
            $query = $conn->prepare(" DELETE FROM Contacts
                WHERE FirstName = ?
                AND LastName = ?
                AND Email = ?
                AND PhoneNumber = ?
                AND UserID = ?
            ");

            // Bind the parameters from the JSON object to the SQL query
            $query->bind_param("ssssi", $first, $last, $email, $number, $user);

            // Execute the SQL query
            $query->execute();

            // Grab the result. This should be the number of rows deleted
            $deleted = $conn->affected_rows;

            if ($deleted === 1) {
                returnStatus("SUCCESS: One row was deleted.");
            } elseif ($deted === 0) {
                returnStatus("ERROR: No rows were deleted.");
            } else {
                returnStatus("FAILURE: Multiple rows were deleted.");
            }

            $query->close();
            $conn->close();
        }
    }

    function returnStatus( $message )
    {
        $object = '{"status":"' . $message . '"}';
        header('Content-type: application/json');
        echo $object;
    }
?>