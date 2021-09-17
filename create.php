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
            $query = $conn->prepare(" INSERT INTO Contacts
                    FirstName,
                    LastName,
                    Email,
                    PhoneNumber,
                    UserID
                ) VALUES (
                    ?, ?, ?, ?, ?
                )
            ");

            // Bind the parameters from the JSON object to the SQL query
            $query->bind_param("ssssi", $first, $last, $email, $number, $user);

            // Execute the SQL query
            $query->execute();

            // Grab the result. This should be the number of rows inserted
            $inserted = $conn->insert_id;

            if ($inserted) {
                returnStatus("SUCCESS: Insertion successful.");
            } else {
                returnStatus("ERROR: Insertion unsuccessful.");
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