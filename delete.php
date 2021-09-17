<?php
	if($_SERVER['REQUEST_METHOD']==='POST')
    {
        //the raw body MUST be in json format. body is case sensitive
        $reqBody = json_decode(file_get_contents('php://input'), true);
        $conn = new mysqli("localhost", "DBADMIN", "DBADMIN", "ContactBook");

        if( $conn->connect_error )
        {
            returnWithError( $conn->connect_error );
        }
        else
        {
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
                returnWithInfo( $result );
            }
            else
            {
                returnWithError( $result );
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