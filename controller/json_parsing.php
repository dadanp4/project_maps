<?php
    include_once('connect.php');
    header('Content-Type: application/json');

    $data = array();

    $sql = "SELECT * FROM `tb_location`";
    $result = mysqli_query($conn, $sql);
    
      while($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
      }

    echo json_encode($data);

    mysqli_close($conn);
?>