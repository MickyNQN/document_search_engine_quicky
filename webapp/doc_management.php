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
    //$request = json_decode($_POST['doc_details']);

    $creation_date=Utils::getCurrentDate();
    $updated_date = $creation_date;
    session_start();

    try {
        $db->setAutoCommit(FALSE);
        
        $sql = "INSERT INTO dse (title,keywords,caption,thumbnail,DoC,DoU,doc_type,url) VALUES ('$request->title','$request->keywords','$request->caption','$request->thumbnailName','$creation_date','$updated_date','$request->documentType','$request->documentName')";

        if (!($stmt = $db->conn->prepare($sql))) {
            throw new Exception("Prepare failed: (" . $db->conn->errno . ") ");
        }
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: (" . $stmt->errno . ") ");
        }
        else
        {
            $document_id=$stmt->insert_id;
            $db->commit();
            $response["status"] = "success";
            $response["message"] = "Full Document added successfully.";
            $response["cause"] = "";
            $response["response"]["id"]=$document_id;
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


/* upload document */
$app->post("/uploadDocument", function () use ($app) {
    $db = new DbHandler();
    $response = array();
    session_start();

    try {
        $db->setAutoCommit(FALSE);

        $doc_name = $_FILES["file"]["name"];
        $doc_type = $_FILES['file']['type'];
        $doc_size = $_FILES['file']['size'];
        $doc_tmp_name= $_FILES['file']['tmp_name'];
        move_uploaded_file($doc_tmp_name,"docs/$doc_name");

        $response["status"] = "200";
        $response["message"] = "Document uploaded successfully.";
        $response["cause"] = "";
        $response["response"]="";
        $db->commit();
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


/* upload thumbnail */
$app->post("/uploadThumbnail", function () use ($app) {
    $db = new DbHandler();
    $response = array();
    session_start();

    try {
        $db->setAutoCommit(FALSE);

        $doc_name = $_FILES['file']['name'];
        $doc_type = $_FILES['file']['type'];
        $doc_size = $_FILES['file']['size'];
        $doc_tmp_name = $_FILES['file']['tmp_name'];
        move_uploaded_file($_FILES['file']['tmp_name'],"images/" . $_FILES['file']['name']);

        $response["status"] = "200";
        $response["message"] = "Thumbnail uploaded successfully.";
        $response["cause"] = "";
        $response["response"]="";
        $db->commit();
        echoResponse(200, $response);
    } catch (Exception $error) {
        $db->rollback();
        $response["status"] = "error";
        $response["message"] = "Server Not able to add thumbnail";
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
    //var_dump($request);

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
            $response["response"]=$request->id;
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
    //$requestID=$_POST['id'];
    //var_dump($request);

    session_start();

    try {
        $db->setAutoCommit(FALSE);
        $thumbnailName = "images/" . $request->thumbnail ;
        $urlName = "docs/" . $request->url ;
        //var_dump($thumbnailName);
        //var_dump($urlName);
        unlink($thumbnailName);
        unlink($urlName);
        //unlink("docs/$request->url");

        $sql = "DELETE FROM dse WHERE id='$request->id'";

        if (!($stmt = $db->conn->prepare($sql))) {
            throw new Exception("Prepare failed: (" . $db->conn->errno . ") ");
        }
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: (" . $stmt->errno . ") ");
        }
        else
        {
            $response["status"] = "200";
            $response["message"] = "Document deleted successfully.";
            $response["data"] = $request->id;
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


/* Search Query */
$app->post("/search", function () use ($app) {
    $db = new DbHandler();
    $response = array();
    $request = json_decode($app->request->getBody());
    //var_dump($request);

    if (strpos($request->searchQuery, ',') !== false)
        $myArray = explode(',', $request->searchQuery);
    else
        $myArray=$request->searchQuery;
    //var_dump($myArray);
    $count=sizeof($myArray);
    //var_dump($count);


    if (strpos($request->searchkey, ' ') !== false)
        $docArray = explode(' ', $request->searchkey);
    else
        $docArray=$request->searchkey;
    //var_dump($docArray);
    $count1=sizeof($docArray);
    //var_dump($count1);


    //If not selected any options then search in keywords
    if($count==0 && $count1==1){
        $sql = "SELECT * FROM dse 
                WHERE keywords LIKE '%$docArray%' 
                ORDER BY visitors DESC, DoU DESC";
    }else if($count==0 && $count1==2){
        $sql = "SELECT * FROM dse 
                WHERE keywords LIKE '%$docArray[0]%' 
                    AND doc_type LIKE '%$docArray[1]%' 
                ORDER BY visitors DESC, DoU DESC";
    }

    if($count==1 && $count1==1){
        $sql = "SELECT * FROM dse WHERE keywords LIKE '%$docArray%' ORDER BY visitors DESC, DoU DESC";
    }else if($count==1 && $count1==2){
        $sql = "SELECT * FROM dse WHERE keywords LIKE '%$docArray[0]%' AND doc_type LIKE '%$docArray[1]%' ORDER BY visitors DESC, DoU DESC";
    }

    if($count==2 && $count1==1){
        //var_dump($myArray[0],$docArray[0]);
        $sql = "SELECT * FROM dse WHERE $myArray[0] LIKE '%$docArray%' ORDER BY visitors DESC, DoU DESC";
    }else if($count==2 && $count1==2){
        //var_dump($myArray[0],$docArray[0],$myArray[1],$docArray[1]);
        $sql = "SELECT * FROM dse 
                WHERE $myArray[0] LIKE '%$docArray[0]%' 
                    AND doc_type LIKE '%$docArray[1]%' 
                ORDER BY visitors DESC, DoU DESC";
    }

    if($count==3 && $count1==1){
        //var_dump($myArray[0],$myArray[1],$docArray[2],$docArray[0]);
        $sql = "SELECT * FROM dse 
                WHERE $myArray[0] LIKE '%$docArray%' 
                    OR $myArray[1] LIKE '%$docArray%' 
                ORDER BY visitors DESC, DoU DESC";
    }else if($count==3 && $count1==2){
        //var_dump($myArray[0],$myArray[1],$myArray[2],$docArray[0],$docArray[1]);
        $sql = "SELECT * FROM dse 
                WHERE $myArray[0] LIKE '%$docArray[0]%' 
                        AND doc_type LIKE '%$docArray[1]%' 
                    OR $myArray[1] LIKE '%$docArray[0]%' 
                        AND doc_type LIKE '%$docArray[1]%' 
                ORDER BY visitors DESC, DoU DESC";
    }

    if($count==4 && $count1==1){
        //var_dump($myArray[0],$myArray[1],$docArray[2],$docArray[0]);
        $sql = "SELECT * FROM dse 
                WHERE $myArray[0] LIKE '%$docArray%' 
                    OR $myArray[1] LIKE '%$docArray%' 
                    OR $myArray[2] LIKE '%$docArray%'
                ORDER BY visitors DESC, DoU DESC";
    }else if($count==4 && $count1==2){
        //var_dump($myArray[0],$myArray[1],$myArray[2],$docArray[0],$docArray[1]);
        $sql = "SELECT * FROM dse 
                WHERE $myArray[0] LIKE '%$docArray[0]%' 
                        AND doc_type LIKE '%$docArray[1]%' 
                    OR $myArray[1] LIKE '%$docArray[0]%' 
                        AND doc_type LIKE '%$docArray[1]%' 
                    OR $myArray[2] LIKE '%$docArray[0]%' 
                        AND doc_type LIKE '%$docArray[1]%' 
                ORDER BY visitors DESC, DoU DESC";
    }

    /*else if($count==4){
        $sql = "SELECT * FROM dse WHERE $myArray[0] LIKE '%$request->searchkey%' OR $myArray[1] LIKE '%$request->searchkey%' OR $myArray[2] LIKE '%$request->searchkey%' ORDER BY visitors DESC, DoU DESC";
    }*/

    //$sql = "SELECT * FROM dse WHERE keywords LIKE '%$request->searchkey%' ORDER BY visitors DESC, DoU DESC";
    $row = $db->conn->query($sql) or die($this->mysqli->error.__LINE__);
    

    if ($row->num_rows > 0) {
        $result = array();
        while ($r = $row->fetch_assoc()) {
            $result[] = $r;

            //Update visitors after fetching
            $visitors=$r['visitors'];
            $id=$r['id'];
            //var_dump($id, $visitors);
            $sql1 = "UPDATE dse SET visitors=$visitors+1 WHERE id='$id'";
            $row1 = $db->conn->query($sql1) or die($this->mysqli->error.__LINE__);
        }
        $response["status"] = "200";
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


/* Search Query for Android Application */
$app->post("/search1", function () use ($app) {
    $db = new DbHandler();
    $response = array();
    $request = json_decode($app->request->getBody());

    //verifyRequiredParams(array('document_name'), $request);
    $search_query=$request->document_name;
    $newParameter='%'.$search_query.'%';
    $column_name="keywords";
    $table_prefix="";
    
    try
    {
        $sql_query_search_document_name = "SELECT id,title,caption,DoC,thumbnail FROM dse WHERE $column_name LIKE ? ORDER BY visitors DESC, DoU DESC";
        //echo "Query:".$sql_query_search_user_name;

        if (!($stmt = $db->conn->prepare($sql_query_search_document_name))) {
            throw new Exception("Prepare failed: (" . $db->conn->errno . ") ");
        }

        if (!$stmt->bind_param("s",$newParameter)) {
            throw new Exception("Binding parameters failed: (" . $stmt->errno . ")");
        }

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: (" . $stmt->errno . ") ");
        }


        $row = Utils::fetchRowAsArray($stmt);
        if ($row == null) {
            $row = array();
        }

        $response["status"] = "success";
        $response["message"] = "document list successfully fetched.";
        $response["cause"] = "";
        $response["response"]= $row;
        echoResponse(200, $response);
    } catch (Exception $e)  {
        $db->rollback();
        $response["status"] = "failure";
        $response["message"] = $e->getMessage();
        $response["cause"] = "";
        $response["response"] = [];
        echoResponse(200, $response);
    }
});


/*find doc by author sahi wala*/
$app->post("/searchByTitle1", function() use ($app)  {

    $db = new DbHandler();
    $response = array();
    $request = json_decode($app->request->getBody());

    //verifyRequiredParams(array('document_author'), $request);

    $search_query=$request->document_author;

    $newParameter='%'.$search_query.'%';
    $column_name="title";
    $table_prefix="";
    try {

        $sql_query_search_document_name = "SELECT id,title,caption,DoC,thumbnail FROM dse WHERE $column_name LIKE ? ORDER BY visitors DESC, DoU DESC";

        //echo "Query:".$sql_query_search_user_name;

        if (!($stmt = $db->conn->prepare($sql_query_search_document_name))) {
            throw new Exception("Prepare failed: (" . $db->conn->errno . ") ");
        }

        if (!$stmt->bind_param("s",$newParameter)) {
            throw new Exception("Binding parameters failed: (" . $stmt->errno . ")");
        }

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: (" . $stmt->errno . ") ");
        }


        $row = Utils::fetchRowAsArray($stmt);
        if ($row == null) {
            $row = array();
        }

        $response["status"] = "success";
        $response["message"] = "document list successfully fetched.";
        $response["cause"] = "";
        $response["response"]= $row;
        echoResponse(200, $response);

    } catch (Exception $e) {
        $db->rollback();
        $response["status"] = "error";
        $response["cause"] = "error";
        $response["message"] = $e->getMessage();
        $response["response"] = json_decode("{}");//"Trace:" .$e->getTraceAsString();
        echoResponse(201, $response);
    }

    $db->setAutoCommit(TRUE);
});



/* Get All Documents */
$app->post("/getAllDocuments", function () use ($app) {
    $db = new DbHandler();
    $response = array();
    $request = json_decode($app->request->getBody());
    //var_dump($request);

    $sql = "SELECT * FROM dse";// ORDER BY visitors DESC, DoU DESC";
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
            //var_dump("hmm");
        }
        $response["status"] = "200";
        $response["message"] = "Document list successfully fetched..";
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


/* For Android App */
$app->post("/getAllDocuments1", function ()  {

    $db = new DbHandler();
    $IMG_BASE_URL=Utils::getImageBucketURL();
    $DOC_BASE_URL=Utils::getDocBucketURL();
    $sql_query = "SELECT id,title,caption,DoC,thumbnail FROM dse ORDER BY visitors DESC, DoU DESC";

    $r = $db->conn->query($sql_query) or die($this->mysqli->error.__LINE__) ;
    if ($r->num_rows > 0) {
        $result = array();
        while ($row = $r->fetch_assoc()) {
            $result[] = $row;
        }
        $response["status"] = "success";
        $response["message"] = "Document list successfully fetched.";
        $response["cause"] = "";
        $response["response"] = $result;
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
    //var_dump($request);

    $sql = "SELECT * FROM dse WHERE id='$request->id'";
    $row = $db->conn->query($sql) or die($this->mysqli->error.__LINE__);
    if ($row->num_rows > 0) {
        $result = array();
        while ($r = $row->fetch_assoc()) {
            $result[] = $r;

            //Once fetched, Increment the visitors
            /*$visitors=$r['visitors'];
            $id=$r['id'];
            //var_dump($id, $visitors);
            $sql1 = "UPDATE dse SET visitors=$visitors+1 WHERE id='$id'";
            $row1 = $db->conn->query($sql1) or die($this->mysqli->error.__LINE__);*/
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



$app->get("/downloadDocument", function() use ($app)
{
    $allPostVars = $app->request->get();
    $title=$_GET['file'];
    //$response = array();
    //$request = json_decode($app->request->getBody());
    // var_dump($request);
    //$title= $request->title;

    $path = "./docs/";

    $dl_file = preg_replace("([^\w\s\d\-_~,;:\[\]\(\).]|[\.]{2,})", '', $title); // simple file name validation
    $dl_file = filter_var($dl_file, FILTER_SANITIZE_URL); // Remove (more) invalid characters
    $fullPath = $path.$dl_file;
    //echo $dl_file;
    if ($fd = fopen ($fullPath, "r")) {
     $fsize = filesize($fullPath);
     $path_parts = pathinfo($fullPath);
     $ext = strtolower($path_parts["extension"]);
     //echo $ext;
     // echo $path_parts["basename"];
    switch ($ext) {
         case "pdf":
         header("Content-type: application/pdf");
         header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a file download
         break;
         // add more headers for other content types here
         default;
         header("Content-type: application/octet-stream");
         header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
         break;
    }
    header("Content-length: $fsize");
     header("Cache-control: private"); //use this to open files directly
     while(!feof($fd)) {
         $buffer = fread($fd, 2048);
         echo $buffer;
     }
    }
    fclose ($fd);
    exit;
});


/* Increment the counter of visits in the history table */
$app->post("/updateVisits", function () use ($app) {
    $db = new DbHandler();
    $response = array();
    $request = json_decode($app->request->getBody());
    //var_dump($request);

    if (strpos($request->searchkey, ' ') !== false)
        $myArray = explode(' ', $request->searchkey);
    else
        $myArray=$request->searchkey;
    //var_dump($myArray);
    $count=sizeof($myArray);

    $updated_date=Utils::getCurrentDate();
    session_start();

    try {
        $db->setAutoCommit(FALSE);

        if($count==1){
            $sql = "SELECT * FROM history WHERE keyword='$myArray'";
        }
        else{
            $sql = "SELECT * FROM history WHERE keyword='$myArray[0]'";
        }

        $row = $db->conn->query($sql) or die($this->mysqli->error.__LINE__);
        if ($row->num_rows > 0) {
            while ($r = $row->fetch_assoc()) {
                $visits=$r['visits'];
                //var_dump($visits);
            }
        }
        else{/* Keyword not searched in the history = New keyword found*/
            //var_dump($request->keyword);
            if($count==1){
                $sql2 = "INSERT INTO history (keyword,visits) VALUES ('$myArray', 0)";
            }
            else{
                $sql2 = "INSERT INTO history (keyword,visits) VALUES ('$myArray[0]', 0)";   
            }
            $row2 = $db->conn->query($sql2) or die($this->mysqli->error.__LINE__);
            $visits=0;
        }
        
        if($count==1){
            $sql1 = "UPDATE history SET visits=$visits+1 WHERE keyword='$myArray'";
        }
        else{
            $sql1 = "UPDATE history SET visits=$visits+1 WHERE keyword='$myArray[0]'";   
        }

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


/* Increment the counter of visits in the history table */
/*$app->post("/updateVisitors", function () use ($app) {
    $allPostVars = $app->request->get();
    $id=$_GET['id'];

    $db = new DbHandler();
    $response = array();
    //$request = json_decode($app->request->getBody());
    //var_dump($request);

    $updated_date=Utils::getCurrentDate();
    session_start();

    try {
        $db->setAutoCommit(FALSE);

        $sql = "SELECT visitors FROM dse WHERE id='$id'";
        $row = $db->conn->query($sql) or die($this->mysqli->error.__LINE__);
        if ($row->num_rows > 0) {
            while ($r = $row->fetch_assoc()) {
                $visitors=$r['visitors'];
                //var_dump($visits);
            }
        }
        
        $sql1 = "UPDATE dse SET visitors=$visitors+1 WHERE id='$id'";
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
});*/


/* Get Trends */
$app->get("/getTrends", function () use ($app) {
    $db = new DbHandler();
    //$length=$const->TRENDING_KEYWORDS;
    $response = array();
    $request = json_decode($app->request->getBody());
    //var_dump($request);

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


/* Get Trends */
$app->get("/getStats", function () use ($app) {
    $db = new DbHandler();
    //$length=$const->TRENDING_KEYWORDS;
    $response = array();
    //$request = json_decode($app->request->getBody());
    //var_dump($request);

    $sql = "SELECT SUM(visitors) AS visitors , COUNT(*) AS docs FROM dse";
    $row = $db->conn->query($sql) or die($this->mysqli->error.__LINE__);
    if ($row->num_rows > 0) {
        $result = array();
        while ($r = $row->fetch_assoc()) {
            $result[] = $r;

            $visitors=$r['visitors'];
            $docs=$r['docs'];
        }
        $response["status"] = "success";
        $response["message"] = "Trending keyword list successfully fetched.";
        $response["cause"] = "";
        $response["visitors"] = $visitors;
        $response["docs"] = $docs;
        echoResponse(200, $response);
    } else {
        $response["status"] = "success";
        $response["message"] = "Data Not Found.";
        $response["cause"] = "";
        $response["response"] = [];
        echoResponse(200, $response);
    }
});