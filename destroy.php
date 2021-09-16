<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Initialize some variables
        $server = "localhost";
        $username = "root";
        $password = "TMs7aedJuMNX";
        $database = "ContactBook";


        // Grab the data and initiate the connection
        $body = json_decode(file_get_contents('php://input'), true);
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
             // Seed the SQL Query with basic format
             $query = $conn.prepare(" DELETE FROM Contacts WHERE FirstName = ? ");

             // Check if the query preparation completed successfully
             if ( false === $query ) {
                returnStatus('FAILURE: Query preparation failed.');
                exit();
            }

             // Grab JSON and populate the SQL Query
             $bind = $query->bind_param("s", $body["FirstName"]);
             // Execute the SQL Query
             $exec = $query->execute();

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
