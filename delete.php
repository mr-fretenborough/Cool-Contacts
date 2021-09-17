<?php
	if($_SERVER['REQUEST_METHOD']==='POST')
    {
        // Pull the JSON object from the POST request
        $reqBody = json_decode(file_get_contents('php://input'), true);

        // Establish the connection to the MySQL Instance
        $conn = new mysqli("localhost", "DBADMIN", "DBADMIN", "ContactBook");

        // Validate the connection
        if( $conn->connect_error )
        {
            returnWithError( $conn->connect_error );
        }
        else
        {
            // Create the query template
            $stmt = $conn->prepare(" DELETE FROM Contacts
                WHERE FirstName = ?
                AND LastName = ?
                AND Email = ?
                AND PhoneNumber = ?
                AND UserID = ?
            ");

            // Bind the parameters from the JSON object to the SQL query
            $stmt->bind_param("ssssi", $reqBody["FirstName"], $reqBody["LastName"], $reqBody["Email"], $reqBody["PhoneNumber"], $reqBody["UserID"]);

            // Execute the SQL query
            $stmt->execute();

            // Grab the result. This should be the number of rows deleted
            $deleted = mysql_affected_rows();

            if ($deleted === 1) {
                returnWithError($deleted . ' rows deleted');
            } elseif ($deted === 0) {
                returnWithError("0 rows affected");
            } else {
                returnWithError("More than one row was deleted.");
            }

            $stmt->close();
            $conn->close();
        }
    }

    function sendResultInfoAsJson( $obj )
    {
        header('Content-type: application/json');
        echo $obj;
    }

    function returnWithError( $err )
    {
        $retValue = '{"error":"' . $err . '"}';
        sendResultInfoAsJson( $retValue );
    }

    function returnWithInfo( $id )
    {
        $retValue = '{"result":' . $id . '"}';
        sendResultInfoAsJson( $retValue );
    }
?>