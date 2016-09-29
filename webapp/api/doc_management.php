<?php

//////////////////////////////////////////////////////////////////////////////////////////////////
//                   Sardar Vallabhbhai National Institute of Technology.                       //
//                                                                                              //
// Title:            Document Search Engine                                                     //
// File:             doc_management.php                                                                 //
// Since:            29-Mar-2016 : PM 11:29                                                     //
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


/* Add new document */
$app->post("/addDocument", function () use ($app) {
    $db = new DbHandler();
    $response = array();
    $request = json_decode($app->request->getBody());
    var_dump($request);

    $creation_date=Utils::getCurrentDate();
    $updated_date = $creation_date;
    session_start();

    try {
        $db->setAutoCommit(FALSE);
        $sql = "INSERT INTO dse (title,keywords,caption,doc_type,url,DoC,DoU) VALUES ('$request->title','$request->keywords','$request->caption','$request->doc_type','$request->url','$creation_date','$updated_date')";

        if (!($stmt = $db->conn->prepare($sql))) {
            throw new Exception("Prepare failed: (" . $db->conn->errno . ") ");
        }
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: (" . $stmt->errno . ") ");
        }
        else
        {
            $response["status"] = "success";
            $response["message"] = "Document added successfully.";
            $response["cause"] = "";
            $response["response"]="";
            $db->commit();
        }
        echoResponse(200, $response);
    } catch (Exception $error) {
        $db->rollback();
        $response["status"] = "error";
        $response["message"] = "Server Not able to add document";
        $response["cause"] = "Exception:" . $error->getMessage();
        $response["response"] = "Trace:" . $error->getTraceAsString();
        echoResponse(401, $response);
    }
});


/* Update the data of document */
$app->post("/updateDocument", function () use ($app) {
    $db = new DbHandler();
    $response = array();
    $request = json_decode($app->request->getBody());
    var_dump($request);

    $updated_date=Utils::getCurrentDate();
    session_start();

    try {
        $db->setAutoCommit(FALSE);
        $sql = "UPDATE dse SET title='$request->title', keywords='$request->keywords', caption='$request->caption', DoU='$updated_date' WHERE id='$request->id'";

        if (!($stmt = $db->conn->prepare($sql))) {
            throw new Exception("Prepare failed: (" . $db->conn->errno . ") ");
        }
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: (" . $stmt->errno . ") ");
        }
        else
        {
            $response["status"] = "success";
            $response["message"] = "Document updated successfully.";
            $response["cause"] = "";
            $response["response"]="";
            $db->commit();
        }
        echoResponse(200, $response);
    } catch (Exception $error) {
        $db->rollback();
        $response["status"] = "error";
        $response["message"] = "Server Not able to update document";
        $response["cause"] = "Exception:" . $error->getMessage();
        $response["response"] = "Trace:" . $error->getTraceAsString();
        echoResponse(401, $response);
    }
});


/* Delete the document */
$app->delete("/deleteDocument", function () use ($app) {
    $db = new DbHandler();
    $response = array();
    $request = json_decode($app->request->getBody());
    var_dump($request);

    session_start();

    try {
        $db->setAutoCommit(FALSE);
        $sql = "DELETE FROM dse WHERE id='$request->id'";

        if (!($stmt = $db->conn->prepare($sql))) {
            throw new Exception("Prepare failed: (" . $db->conn->errno . ") ");
        }
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: (" . $stmt->errno . ") ");
        }
        else
        {
            $response["status"] = "success";
            $response["message"] = "Document deleted successfully.";
            $response["cause"] = "";
            $response["response"]="";
            $db->commit();
        }
        echoResponse(200, $response);
    } catch (Exception $error) {
        $db->rollback();
        $response["status"] = "error";
        $response["message"] = "Server Not able to delete the document";
        $response["cause"] = "Exception:" . $error->getMessage();
        $response["response"] = "Trace:" . $error->getTraceAsString();
        echoResponse(401, $response);
    }
});


/* Search Query - Get All Documents */
$app->post("/getAllDocuments", function () use ($app) {
    $db = new DbHandler();
    //$response = array();
    $request = json_decode($app->request->getBody());
    var_dump($request);

    $sql = "SELECT * FROM dse WHERE keywords LIKE '%$request->searchkey%' ORDER BY visitors DESC, DoU DESC";
    $row = $db->conn->query($sql) or die($this->mysqli->error.__LINE__);
    if ($row->num_rows > 0) {
        $result = array();
        while ($r = $row->fetch_assoc()) {
            $result[] = $r;
        }
        $response["status"] = "success";
        $response["message"] = "Document list successfully fetched.";
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


// Retrieve the document
$app->post('/getDocument', function () use ($app) {
    $db = new DbHandler();
    $request = json_decode($app->request->getBody());
    var_dump($request);

    $sql = "SELECT * FROM dse WHERE id='$request->id'";
    $row = $db->conn->query($sql) or die($this->mysqli->error.__LINE__);
    if ($row->num_rows > 0) {
        $result = array();
        while ($r = $row->fetch_assoc()) {
            $result[] = $r;

            //Once fetched, Increment the visitors
            $visitors=$r['visitors'];
            $id=$r['id'];
            //var_dump($id, $visitors);
            $sql1 = "UPDATE dse SET visitors=$visitors+1 WHERE id='$id'";
            $row1 = $db->conn->query($sql1) or die($this->mysqli->error.__LINE__);
        }

        $response["status"] = "success";
        $response["message"] = "Document successfully fetched.";
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


// Download the document
$app->post('/downloadDocument', function () use ($app) {
    $db = new DbHandler();
    $request = json_decode($app->request->getBody());
    $fullPath = $request->filename;
    var_dump($request);

    if ($fd = fopen ($fullPath, "r")) { 
        $fsize = filesize($fullPath); 
        $path_parts = pathinfo($fullPath); 
        $ext = strtolower($path_parts["extension"]); 
        switch ($ext) { 
            case "pdf": 
                header("Content-type: application/pdf"); // add here more headers for diff. extensions 
                header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a download 
                var_dump("PDF document downloaded");
                break; 
            default: 
                header("Content-type: application/octet-stream"); 
                header("Content-Disposition: filename=\"".$path_parts["basename"]."\""); 
        } 
        header("Content-length: $fsize"); 
        header("Cache-control: private"); //use this to open files dire

        $response["status"] = "success";
        $response["message"] = "Document successfully fetched.";
        $response["cause"] = "";
        //$response["data"] = $result;
        echoResponse(200, $response);
        exit;
    } else {
        $response["status"] = "failure";
        $response["message"] = "Data Not Found.";
        $response["cause"] = "";
        $response["response"] = [];
        echoResponse(200, $response);
    }
});



/* Increment the counter of visits in the history table */
$app->post("/updateVisits", function () use ($app) {
    $db = new DbHandler();
    $response = array();
    $request = json_decode($app->request->getBody());
    var_dump($request);

    $updated_date=Utils::getCurrentDate();
    session_start();

    try {
        $db->setAutoCommit(FALSE);

        $sql = "SELECT * FROM history WHERE keyword='$request->keyword'";
        $row = $db->conn->query($sql) or die($this->mysqli->error.__LINE__);
        if ($row->num_rows > 0) {
            while ($r = $row->fetch_assoc()) {
                $visits=$r['visits'];
                var_dump($visits);
            }
        }
        else{/* Keyword not searched in the history = New keyword found*/
            var_dump($request->keyword);
            $sql2 = "INSERT INTO history (keyword,visits) VALUES ('$request->keyword', 0)";
            $row2 = $db->conn->query($sql2) or die($this->mysqli->error.__LINE__);
            $visits=0;
        }
        
        $sql1 = "UPDATE history SET visits=$visits+1 WHERE keyword='$request->keyword'";
        if (!($stmt = $db->conn->prepare($sql1))) {
            throw new Exception("Prepare failed: (" . $db->conn->errno . ") ");
        }
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: (" . $stmt->errno . ") ");
        }
        else
        {
            $response["status"] = "success";
            $response["message"] = "visits of particular keywords updated successfully.";
            $response["cause"] = "";
            $response["response"]="";
            $db->commit();
        }
        echoResponse(200, $response);
    } catch (Exception $error) {
        $db->rollback();
        $response["status"] = "error";
        $response["message"] = "Server Not able to increment visits of particular keywords";
        $response["cause"] = "Exception:" . $error->getMessage();
        $response["response"] = "Trace:" . $error->getTraceAsString();
        echoResponse(401, $response);
    }
});


/* Get Trends */
$app->get("/getTrends", function () use ($app) {
    $db = new DbHandler();
    //$length=$const->TRENDING_KEYWORDS;
    //$response = array();
    $request = json_decode($app->request->getBody());
    var_dump($request);

    $sql = "SELECT * FROM history ORDER BY visits DESC LIMIT 5";
    $row = $db->conn->query($sql) or die($this->mysqli->error.__LINE__);
    if ($row->num_rows > 0) {
        $result = array();
        while ($r = $row->fetch_assoc()) {
            $result[] = $r;
        }
        $response["status"] = "success";
        $response["message"] = "Trending keyword list successfully fetched.";
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









/* Add user testing */
$app->post("/addUserTest", function () use ($app) {
    $db = new DbHandler();
    $response = array();
    $request = json_decode($app->request->getBody());
    var_dump($request);

    $creation_date=Utils::getCurrentDate();
    $updated_date = $creation_date;
    session_start();

    try {
        $db->setAutoCommit(FALSE);
        $sql = "INSERT INTO dse (title,keywords,caption,doc_type,url,DoC,DoU) VALUES ('$request->title','$request->keywords','$request->caption','$request->doc_type','$request->url','$creation_date','$updated_date')";

        if (!($stmt = $db->conn->prepare($sql))) {
            throw new Exception("Prepare failed: (" . $db->conn->errno . ") ");
        }
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: (" . $stmt->errno . ") ");
        }
        else
        {
            $response["status"] = "success";
            $response["message"] = "Document added successfully.";
            $response["cause"] = "";
            $response["response"]="";
            $db->commit();
        }
        echoResponse(200, $response);
    } catch (Exception $error) {
        $db->rollback();
        $response["status"] = "error";
        $response["message"] = "Server Not able to add document";
        $response["cause"] = "Exception:" . $error->getMessage();
        $response["response"] = "Trace:" . $error->getTraceAsString();
        echoResponse(401, $response);
    }
});