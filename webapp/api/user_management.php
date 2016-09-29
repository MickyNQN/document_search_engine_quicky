<?php

//////////////////////////////////////////////////////////////////////////////////////////////////
//                   Sardar Vallabhbhai National Institute of Technology.                       //
//                                                                                              //
// Title:            Document Search Engine                                                     //
// File:             user_management.php                                                                 //
// Since:            28-Mar-2016 : PM 06:29                                                     //
//                                                                                              //
// Author:           Heet Sheth                                                                 //
// Email:            u13co005@svnit.ac.in                                                       //
//                                                                                              //
/////////////////////////////////////////////////////////////////////////////////////////////////


// Sample GET method for testing
$app->get('/', function () use ($app) {
    $response = "Simple Get method running...";
    echoResponse(200, $response);
});



/* Add new user */
$app->post("/addUser", function () use ($app) {
    $db = new DbHandler();
    $response = array();
    $request = json_decode($app->request->getBody());
    var_dump($request);

    $creation_date=Utils::getCurrentDate();
    $updated_date = $creation_date;
    session_start();

    try {
        $db->setAutoCommit(FALSE);
        //For Gender: (Male, Female, Others) -> (0,1,2)
        $sql = "INSERT INTO user (username,firstname,lastname,email,password,gender,DoB,DoC,DoU)
            VALUES ('$request->username','$request->firstname','$request->lastname','$request->email','$request->password','$request->gender','$request->DoB','$creation_date','$updated_date')";

        if (!($stmt = $db->conn->prepare($sql))) {
            throw new Exception("Prepare failed: (" . $db->conn->errno . ") ");
        }
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: (" . $stmt->errno . ") ");
        }
        else
        {
            $response["status"] = "success";
            $response["message"] = "User added successfully.";
            $response["cause"] = "";
            $response["response"]="";
            $db->commit();
        }
        echoResponse(200, $response);
    } catch (Exception $error) {
        $db->rollback();
        $response["status"] = "error";
        $response["message"] = "Server Not able to add User";
        $response["cause"] = "Exception:" . $error->getMessage();
        $response["response"] = "Trace:" . $error->getTraceAsString();
        echoResponse(401, $response);
    }
});


// Retrieve a particular user data
$app->post('/getUser', function () use ($app) {
    $db = new DbHandler();
    $request = json_decode($app->request->getBody());
    var_dump($request);
    
    $sql = "SELECT * FROM user WHERE id='$request->id'";
    $row = $db->conn->query($sql) or die($this->mysqli->error.__LINE__);
    if ($row->num_rows > 0) {
        $result = array();
        while ($r = $row->fetch_assoc()) {
            $result[] = $r;
        }
        $response["status"] = "success";
        $response["message"] = "User Data successfully fetched.";
        $response["cause"] = "";
        $response["data"] = $result;
        echoResponse(200, $response);
    } else {
        $response["status"] = "success";
        $response["message"] = "Data Not Found.";
        $response["cause"] = "";
        $response["response"] = [];
        echoResponse(200, $response);
    }
});


/* Get All the Users */
$app->get("/getAllUsers", function () use ($app) {
    $db = new DbHandler();
    $response = array();
    $request = json_decode($app->request->getBody());
    var_dump($request);

    $sql = "SELECT * FROM user";
    $row = $db->conn->query($sql) or die($this->mysqli->error.__LINE__);
    if ($row->num_rows > 0) {
        $result = array();
        while ($r = $row->fetch_assoc()) {
            $result[] = $r;
        }
        $response["status"] = "success";
        $response["message"] = "User list successfully fetched.";
        $response["cause"] = "";
        $response["data"] = $result;
        echoResponse(200, $response);
    } else {
        $response["status"] = "success";
        $response["message"] = "Data Not Found.";
        $response["cause"] = "";
        $response["response"] = [];
        echoResponse(200, $response);
    }
});


/* Delete user */
$app->post("/deleteUser", function () use ($app) {
    $db = new DbHandler();
    $response = array();
    $request = json_decode($app->request->getBody());
    var_dump($request);

    session_start();

    try {
        $db->setAutoCommit(FALSE);
        $sql = "DELETE FROM user WHERE id='$request->id'";

        if (!($stmt = $db->conn->prepare($sql))) {
            throw new Exception("Prepare failed: (" . $db->conn->errno . ") ");
        }
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: (" . $stmt->errno . ") ");
        }
        else
        {
            $response["status"] = "success";
            $response["message"] = "User deleted successfully.";
            $response["cause"] = "";
            $response["response"]="";
            $db->commit();
        }
        echoResponse(200, $response);
    } catch (Exception $error) {
        $db->rollback();
        $response["status"] = "error";
        $response["message"] = "Server Not able to delete User";
        $response["cause"] = "Exception:" . $error->getMessage();
        $response["response"] = "Trace:" . $error->getTraceAsString();
        echoResponse(401, $response);
    }
});


/* Update the profile of user */
$app->post("/updateUserProfile", function () use ($app) {
    $db = new DbHandler();
    $response = array();
    $request = json_decode($app->request->getBody());
    var_dump($request);

    $updated_date=Utils::getCurrentDate();
    session_start();

    try {
        $db->setAutoCommit(FALSE);
        $sql = "UPDATE user SET firstname='$request->firstname', lastname='$request->lastname', email='$request->email', gender='$request->gender', DoB='$request->DoB', DoU='$updated_date' WHERE id='$request->id'";

        if (!($stmt = $db->conn->prepare($sql))) {
            throw new Exception("Prepare failed: (" . $db->conn->errno . ") ");
        }
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: (" . $stmt->errno . ") ");
        }
        else
        {
            $response["status"] = "success";
            $response["message"] = "User Profile updated successfully.";
            $response["cause"] = "";
            $response["response"]="";
            $db->commit();
        }
        echoResponse(200, $response);
    } catch (Exception $error) {
        $db->rollback();
        $response["status"] = "error";
        $response["message"] = "Server Not able to update user profile";
        $response["cause"] = "Exception:" . $error->getMessage();
        $response["response"] = "Trace:" . $error->getTraceAsString();
        echoResponse(401, $response);
    }
});


/* Change the password of user */
$app->post("/changePassword", function () use ($app) {
    $db = new DbHandler();
    $response = array();
    $request = json_decode($app->request->getBody());
    var_dump($request);

    $updated_date=Utils::getCurrentDate();
    session_start();

    try {
        $db->setAutoCommit(FALSE);
        $sql = "UPDATE user SET password='$request->password', DoU='$updated_date' WHERE id='$request->id'";

        if (!($stmt = $db->conn->prepare($sql))) {
            throw new Exception("Prepare failed: (" . $db->conn->errno . ") ");
        }
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: (" . $stmt->errno . ") ");
        }
        else
        {
            $response["status"] = "success";
            $response["message"] = "Password updated successfully.";
            $response["cause"] = "";
            $response["response"]="";
            $db->commit();
        }
        echoResponse(200, $response);
    } catch (Exception $error) {
        $db->rollback();
        $response["status"] = "error";
        $response["message"] = "Server Not able to update user password";
        $response["cause"] = "Exception:" . $error->getMessage();
        $response["response"] = "Trace:" . $error->getTraceAsString();
        echoResponse(401, $response);
    }
});

