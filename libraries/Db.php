<?php

function insertInviteCode($db, $user, $pass, $dbName, $fleetId, $inviteCode, $accessToken, $refreshToken)
{
    $conn = new mysqli($db, $user, $pass, $dbName);

    $sql = "INSERT INTO openFleetInvites (fleetId, inviteCode, accessToken, refreshToken) VALUES ('$fleetId','$inviteCode','$accessToken', '$refreshToken')"
        . "ON DUPLICATE KEY UPDATE inviteCode='$inviteCode', accessToken='$accessToken', refreshToken='$refreshToken'";

    if ($conn->query($sql) === TRUE) {
        return null;
    } else {
        return null;
    }
}

function deleteInviteCode($db, $user, $pass, $dbName, $fleetId)
{
    $conn = new mysqli($db, $user, $pass, $dbName);
    
    if ($stmt = $conn->prepare("DELETE FROM openFleetInvites WHERE fleetId= ?")) {
        // Bind the variables to the parameter as strings.
        $stmt->bind_param("s", $fleetId);
        // Execute the statement.
        $stmt->execute();
        // Close the prepared statement.
        $stmt->close();
        return null;
    }
    return null;
}

function getAccessToken($db, $user, $pass, $dbName, $fleetId, $inviteCode)
{
    $conn = new mysqli($db, $user, $pass, $dbName);
    $retToken = false;
    
    if ($stmt = $conn->prepare("SELECT accessToken FROM openFleetInvites WHERE fleetId= ? AND inviteCode= ?")) {
        // Bind the variables to the parameter as strings.
        $stmt->bind_param("ss", $fleetId, $inviteCode);
        // Execute the statement.
        if( $stmt->execute() 
           && $result = $stmt->get_result() )
        {
           if($result->num_rows > 0)
           {
              $row = mysqli_fetch_array($result);
              $retToken = $row[0];
           }
           else
           {
              error_log("Zero rows returned");
              error_log("FleedId: $fleetId, InviteCode: $inviteCode");
           }
           $result->close();
        }
        else
        {
           error_log("Query failed");
           error_log("FleedId: $fleetId, InviteCode: $inviteCode");
        }
      $stmt->close();
    }
     else
     {
        error_log("Could not connect to database.");
        error_log("DBServer: $db, User: $user, Pass: $pass, DBName: $dbName");
     }
    return $retToken;
}

function getRefreshToken($db, $user, $pass, $dbName, $fleetId, $inviteCode)
{
    $conn = new mysqli($db, $user, $pass, $dbName);
    $retToken = false;
    
    if ($stmt = $conn->prepare("SELECT refreshToken FROM openFleetInvites WHERE fleetId= ? AND inviteCode= ?")) {
        // Bind the variables to the parameter as strings.
        $stmt->bind_param("ss", $fleetId, $inviteCode);
        // Execute the statement.
        if( $stmt->execute() 
           && $result = $stmt->get_result() )
        {
           if($result->num_rows > 0)
           {
              $row = mysqli_fetch_array($result);
              $retToken = $row[0];
           }
           else
           {
              error_log("Zero rows returned");
              error_log("FleedId: $fleetId, InviteCode: $inviteCode");
           }
           $result->close();
        }
        else
        {
           error_log("Query failed");
           error_log("FleedId: $fleetId, InviteCode: $inviteCode");
        }
      $stmt->close();
    }
     else
     {
        error_log("Could not connect to database.");
        error_log("DBServer: $db, User: $user, Pass: $pass, DBName: $dbName");
     }
    return $retToken;
}

function updateAccessToken($db, $user, $pass, $dbName, $fleetId, $inviteCode, $accessToken)
{
    $conn = new mysqli($db, $user, $pass, $dbName);
    $success = false;
    
    if ($stmt = $conn->prepare("UPDATE openFleetInvites SET accessToken= ? WHERE fleetId= ? AND inviteCode= ?")) {
        // Bind the variables to the parameter as strings.
        $stmt->bind_param("sss", $accessToken, $fleetId, $inviteCode);
        // Execute the statement.
        if( $stmt->execute() )
          $success = true;
        $stmt->close();
    }
     else
     {
        error_log("Could not connect to database.");
        error_log("DBServer: $db, User: $user, Pass: $pass, DBName: $dbName");
     }
    return $success;
}
