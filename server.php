<?php
$serverName='localhost';
$user = 'codecamp.utmatht';
$pass = 'aTX408i4zDvL2uS';
$db = 'utmathtutor';

$db = new mysqli($serverName, $user , $pass , $db) or die("Unable to connect");
// Check connection
if ($db->connect_error) {
  die("Connection failed: " . $db->connect_error);
}

function filewrite($file,$line){
  $myfile = fopen($file, "w") or die("Unable to open file!");
  fwrite($myfile, $line . "\n");
  fclose($myfile);
}

function dbQueryInsertDelete($db,$sql,$valueType,$listValues){
  // example $sql = "SELECT * FROM users WHERE email = ?;";
  $stmt = mysqli_stmt_init($db);
  if (!mysqli_stmt_prepare($stmt,$sql)){
    return false;
  }
  mysqli_stmt_bind_param($stmt, $valueType,...$listValues);
  if (mysqli_stmt_execute($stmt)) {
    http_response_code(201);
    return true;
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
    http_response_code(500);
    return false;
  }
  mysqli_stmt_close($stmt);
}

function dbQuery($db,$sql,$valueType,$listValues){
  // example $sql = "SELECT * FROM users WHERE email = ?;";
  $data = array();
  $stmt = mysqli_stmt_init($db);
  if (!mysqli_stmt_prepare($stmt,$sql)){
    echo "failed to connect to db";
    return false;
  }
  if($valueType!="" and $listValues!=array() ){
    mysqli_stmt_bind_param($stmt, $valueType,...$listValues);

  }
  mysqli_stmt_execute($stmt);

  $resultData=mysqli_stmt_get_result($stmt);


  // if($row = mysqli_fetch_assoc($resultData)){
    while($row = mysqli_fetch_array($resultData)) {
      $newData= array(
        "title" => $row['title'],
        "description" => $row['description'],
        "dueDate" => $row['dueDate'],
        "start" => $row['startDate'],
        "end" => $row['endDate'],
        "color" => $row['color'],
        "difficultyLvl" => $row['difficultyLvl'],
        "completion" => $row['completion'],
        "priorityLvl" => $row['priorityLvl'],
        "taskid" => $row['taskid']
      );
      array_push($data,$newData);
    }
    return $data;
  // } else {
  //   echo "nothing found";
  //   return false;
  // }
  mysqli_stmt_close($stmt);
}

function createTask($db,$title,$description,$dueDate,$startDate,$endDate,$color,$difficultyLvl,$completion,$priorityLvl){
  $sql = "INSERT INTO tasks (title,description,dueDate,startDate,endDate,color,difficultyLvl,completion,priorityLvl) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?);";
  $cols=array($title,$description,$dueDate,$startDate,$endDate,$color,$difficultyLvl,$completion,$priorityLvl);
  dbQueryInsertDelete($db,$sql,"ssssssiii",$cols);
}

function updateTask($db,$title,$description,$dueDate,$startDate,$endDate,$color,$difficultyLvl,$completion,$priorityLvl,$taskid){
  $sql = "UPDATE tasks SET title=?,description=?,dueDate=?,startDate=?,endDate=?,color=?,difficultyLvl=?,completion=?,priorityLvl=? WHERE taskid = ?;";
  $cols=array($title,$description,$dueDate,$startDate,$endDate,$color,$difficultyLvl,$completion,$priorityLvl,$taskid);
  dbQueryInsertDelete($db,$sql,"ssssssiiii",$cols);
}



if($_SERVER['REQUEST_METHOD'] === 'POST' and isset($_POST['newTask'])){

  $sql = "CREATE TABLE IF NOT EXISTS tasks (
    taskid INT(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
    title VARCHAR(128) NOT NULL,
    description VARCHAR(128),
    dueDate VARCHAR(128),
    startDate VARCHAR(128),
    endDate VARCHAR(128),
    color VARCHAR(128),
    difficultyLvl int,
    completion int,
    priorityLvl int  NOT NULL,
    dateCreated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  );";

  if ($db->query($sql) === TRUE) {
    echo "";
  } else {
    echo "Error creating table: " . $db->error;
  }

  $title = "";
  $description = "";
  $dueDate = "";
  $startDate = "";
  $endDate = "";
  $color = "";
  $difficultyLvl = 0;
  $completion = 0;
  $priorityLvl = 0;
  $data = json_decode($_POST['newTask'], false);
      $title = $data->{'title'};
      $description = $data->{'description'};
      $dueDate = $data->{'dueDate'};
      $startDate = $data->{'start'};
      $endDate = $data->{'end'};
      $color = $data->{'color'};
      $difficultyLvl = $data->{'difficultyLvl'};
      $completion = $data->{'completion'};
      $priorityLvl = $data->{'priorityLvl'};

  createTask($db,$title,$description,$dueDate,$startDate,$endDate,$color,$difficultyLvl,$completion,$priorityLvl);
  mysqli_close($db);

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $sql = "SELECT * FROM tasks;";
    $data = dbQuery($db,$sql,"",array());
    // $newlistOfTasks=array();
    // // filewrite("test.txt",$data);
    // for ($i=0; $i < count($data); $i++) {
    //
    //   $newData= array(
    //     "title" => $data[$i]->{'title'},
    //     "description" => $data[$i]->{'description'},
    //     "dueDate" => $data[$i]->{'dueDate'},
    //     "start" => $data[$i]->{'start'},
    //     "end" => $data[$i]->{'end'},
    //     "color" => $data[$i]->{'color'},
    //     "difficultyLvl" => $data[$i]->{'difficultyLvl'},
    //     "completion" => $data[$i]->{'completion'},
    //     "priorityLvl" => $data[$i]->{'priorityLvl'},
    //     "taskID" => $data[$i]->{'taskID'}
    //   );
    //   array_push($newlistOfTasks,$newData)
    //
    // }


    $myJSON = json_encode($data);
    echo $myJSON;
    // echo $data;
    http_response_code(200);

}elseif ($_SERVER['REQUEST_METHOD'] === 'POST' and isset($_POST['taskid'])) {
  $sql = "DELETE FROM tasks WHERE taskid = ?;";
  // $data = json_decode($_POST['taskid'], false);
  $taskid = $_POST['taskid'];
  $cols=array($taskid);
  $worked = dbQueryInsertDelete($db,$sql,"i",$cols);
  filewrite("test.txt",$worked);
  http_response_code(200);



} elseif($_SERVER['REQUEST_METHOD'] === 'POST' and isset($_POST['updateTask'])){


  $title = "";
  $description = "";
  $dueDate = "";
  $startDate = "";
  $endDate = "";
  $color = "";
  $difficultyLvl = 0;
  $completion = 0;
  $priorityLvl = 0;
  $taskid = 0;
  $data = json_decode($_POST['updateTask'], false);
      $title = $data->{'title'};
      $description = $data->{'description'};
      $dueDate = $data->{'dueDate'};
      $startDate = $data->{'start'};
      $endDate = $data->{'end'};
      $color = $data->{'color'};
      $difficultyLvl = $data->{'difficultyLvl'};
      $completion = $data->{'completion'};
      $priorityLvl = $data->{'priorityLvl'};
      $taskid = $data->{'taskid'};


  updateTask($db,$title,$description,$dueDate,$startDate,$endDate,$color,$difficultyLvl,$completion,$priorityLvl,$taskid);
  mysqli_close($db);
  http_response_code(200);

} else {
      echo "404";
      echo $_SERVER['REQUEST_URI'];
      http_response_code(404);
}
