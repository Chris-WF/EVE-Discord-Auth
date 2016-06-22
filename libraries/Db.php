<?php

function insertInviteCode($db, $user, $pass, $dbName, $fleetId, $inviteCode, $accessToken)
{
    $conn = new mysqli($db, $user, $pass, $dbName);

    $sql = "INSERT INTO openFleetInvites (fleetId, inviteCode, accessToken) VALUES ('$fleetId','$inviteCode','$accessToken')"
        . "ON DUPLICATE KEY UPDATE inviteCode='$inviteCode', accessToken='$accessToken'";

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
    
    if ($stmt = $conn->prepare("SELECT token FROM openFleetInvites WHERE fleetId= ? AND inviteCode= ?")) {
        // Bind the variables to the parameter as strings.
        $stmt->bind_param("ss", $fleetId, $inviteCode);
        // Execute the statement.
        if($result = $stmt->query())
        {
           if($result->num_rows > 0)
           {
              $row = mysqli_fetch_array($result);
              $retToken = $row[0];
           }
           $result->close();
        }
      $stmt->close();
    }
    return $retToken;
}

