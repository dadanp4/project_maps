<?php 

    include_once('connect.php');

    $title = $_POST['title'];
    $address = $_POST['address'];
    $lat = $_POST['lat'];
    $lng = $_POST['lng'];
    $kategori = $_POST['kategori'];
    $deskripsi = $_POST['deskripsi'];
    

    $sql = "INSERT INTO `tb_location`(`title`, `address`, `lat`, `lng`, `kategori`, `deskripsi`)
    VALUES ('$title','$address','$lat','$lng','$kategori','$deskripsi')";

    if (mysqli_query($conn, $sql)) {
        echo "New record created successfully";
        echo '<script type="text/javascript">';
        echo 'window.location = "../index.php"';
        echo '</script>';
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    mysqli_close($conn);

?>