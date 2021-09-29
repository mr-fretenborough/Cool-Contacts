<?php

	$inData = getRequestInfo();
	
	$searchResults = "";
	$searchCount = 0;

	$conn = new mysqli("localhost", "DBADMIN", "DBADMIN", "ContactBook");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$stmt = $conn->prepare("select FirstName, LastName, PhoneNumber, Email, ID from Contacts where UserID=? and (FirstName 			like ? or LastName like ? or concat(FirstName, ' ', LastName) like ? or concat(FirstName, LastName) like ? or PhoneNumber 		like? or Email like ?)");
		$ContactName = "%" . $inData["search"] . "%";
		$stmt->bind_param("sssssss", $inData["userID"], $ContactName, $ContactName, $ContactName, $ContactName, $ContactName, 			$ContactName);
		$stmt->execute();
		
		$result = $stmt->get_result();
		
		while($row = $result->fetch_assoc())
		{
			if( $searchCount > 0 )
			{
				$searchResults .= ",";
			}
			$searchCount++;
			$searchResults .= '{';
			$searchResults .= '"FirstName":"';
			$searchResults .= $row["FirstName"];
			$searchResults .= '",';
			$searchResults .= '"LastName":"';
			$searchResults .= $row["LastName"];
                       $searchResults .= '",';
                       $searchResults .= '"PhoneNumber":"';
			$searchResults .= $row["PhoneNumber"];
			$searchResults .= '",';
			$searchResults .= '"Email":"';
			$searchResults .= $row["Email"];
			$searchResults .= '",';
			$searchResults .= '"contactID":"';
			$searchResults .= $row["ID"];
			$searchResults .= '"';
			$searchResults .= '}';
		}
		
		if( $searchCount == 0 )
		{
			returnWithError( "No Records Found" );
		}
		else
		{
			returnWithInfo( $searchResults );
		}
		
		$stmt->close();
		$conn->close();
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
		$retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
	function returnWithInfo( $searchResults )
	{
		$retValue = '{"results":[' . $searchResults . '],"error":""}';
		sendResultInfoAsJson( $retValue );
	}
	
?>

