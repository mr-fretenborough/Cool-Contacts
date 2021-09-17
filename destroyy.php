<?php
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Initialize server-specific values
        $server = "localhost";
        $username = "DBADMIN";
        $password = "DBADMIN";
        $database = "ContactBook";

        // Parse and store the POST JSON object
        $body = json_decode(file_get_contents('php://input'), true);
        $FirstName = $body["FirstName"];
        $LastName = $body["LastName"];
        $Email = $body["Email"];
        $PhoneNumber = $body["PhoneNumber"];
        $UserID = $body["UserID"];

        // Establish the connection to the MySQL Instance on the server
        $conn = new mysqli($server, $username, $password, $database);

        if ( $conn->connect_error ) {
            returnWithError( $conn->connect_error );
        } else {
            $stmt = $conn->prepare(" DELETE FROM Contacts
            WHERE FirstName = ?
            AND LastName = ?
            AND Email = ?
            AND PhoneNumber = ?
            AND UserID = ?
        ");
            $stmt->bind_param("ssssi", $reqBody["FirstName"], $reqBody["LastName"], $reqBody["Email"], $reqBody["PhoneNumber"], $reqBody["UserID"]);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            if( $result )
            {
                returnWithInfo( "with info" );
            }
            else
            {
                returnWithError( "with error" );
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
