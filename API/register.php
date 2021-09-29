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
            $stmt = $conn->prepare("INSERT into Users (FirstName,LastName,Login,Password) VALUES (?,?,?,?)");
            $stmt->bind_param("ssss", $reqBody["FirstName"], $reqBody["LastName"], $reqBody["Login"], $reqBody["Password"]);
            $stmt->execute();
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

    function returnWithInfo( $firstName, $lastName, $id )
    {
        $retValue = '{"ID":' . $id . ',"FirstName":"' . $firstName . '","LastName":"' . $lastName . '"}';
        sendResultInfoAsJson( $retValue );
    }
?>