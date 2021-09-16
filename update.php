<?php
 	$inData = getRequestInfo();

   $FirstName=$inData["FirstName"];
   $LastName=$inData["LastName"];
   $Email=$inData["Email"];
   $PhoneNumber=["PhoneNumber"];
 	$ID = $inData["ID"];

 $conn = new mysqli("localhost", "DBADMIN", "DBADMIN", "ContactBook");
 	if ($conn->connect_error)
 	{
 		returnWithError( $conn->connect_error );
 	}
 	else
 	{
 		$stmt = $conn->prepare(" UPDATE ContactBook.Contacts
       SET
         FirstName=?,
         LastName=?,
         Email=?,
         PhoneNumber=?,
         WHERE ID=?"
     ) ;
 		$stmt->bind_param("ssssi",
         $inData["FirstName"],
         $inData["LastName"],
         $inData["Email"],
         $inData["PhoneNumber"],
         $inData["ID"]);
 		$stmt->execute();
 		$stmt->close();
 		$conn->close();
 		returnWithError("");
 	}

 	function getRequestInfo()
 	{
 		return json_decode(file_get_contents('php://input'), true);
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

 ?>
