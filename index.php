<?php
    include_once("controller/connect.php");
    $result = mysqli_query($conn, "SELECT * FROM `tb_location` ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>G-Maps</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<h1 class="text-center">My Maps</h1>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-7">
            <div id="googleMap" style="width:100%;height:400px;"></div>
        </div>
        <div class="col-sm-5">
            <form action="controller/insert_maps_data.php" method="post" class="was-validated">
                <div class="form-group">
                  <input type="text" class="form-control" placeholder="Title" name="title" required>
                  <div class="invalid-feedback">Please fill out this field.</div>
                  <br>
                  <input type="text" class="form-control" placeholder="Address" name="address" id="address" required>
                  <div class="invalid-feedback">Please fill out this field.</div>
                  <br>
                  <select class="form-control" id="kategori" name="kategori">
                    <option>Kuliner</option>
                    <option>Pendidikan</option>
                    <option>Pariwisata</option>
                  </select>
                  <br>
                  <input type="text" class="form-control" placeholder="Late and long" name="latlong" id="latlong" required disabled>
                  <input type="text" name="lat" id="lat" hidden>
                  <input type="text" name="lng" id="lng" hidden>
                  <br>
                  <textarea class="form-control" rows="3" name="deskripsi" placeholder="Deskripsi" required></textarea>
                  <div class="invalid-feedback">Please fill out this field.</div>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
              </form>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-sm-4">
            <div class="form-check-inline">
            <label class="form-check-label">
                <input type="radio" name="radio" id="radio" class="form-check-input" value="Pendidikan">Pendidikan
            </label>
            </div>
            <div class="form-check-inline">
            <label class="form-check-label">
                <input type="radio" name="radio" id="radio" class="form-check-input" value="Kuliner">Kuliner
            </label>
            </div>
            <div class="form-check-inline">
            <label class="form-check-label">
                <input type="radio" name="radio" id="radio" class="form-check-input" value="Pariwisata">Pariwisata
            </label>
            </div>
            <button type="button" onclick="filterMaps()" class="btn btn-primary">Filter</button>
        </div>
        <div class="col-sm-8">
            
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-sm-12">
            <table class="table">
                <thead class="thead-dark">
                  <tr>
                    <th>Title</th>
                    <th>Address</th>
                    <th>Kategori</th>
                    <th>Deskripsi</th>
                  </tr>
                </thead>
                <tbody>
                    <?php  
                        while($data = mysqli_fetch_array($result)) {         
                            echo "<tr>";
                            echo "<td>".$data['title']."</td>";
                            echo "<td><button id='getAddress' style='background:none; border:none;' onclick='getAddressFunction(".$data['lat'].",".$data['lng'].")'>".$data['address']."</button></td>";
                            echo "<td>".$data['kategori']."</td>";    
                            echo "<td>".$data['deskripsi']."</td>";    
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA1MgLuZuyqR_OGY3ob3M52N46TDBRI_9k&callback=myMap"></script>

<script>
    var position = [-6.917464,107.619123];
    
    function initialize() { 
        var latlng = new google.maps.LatLng(position[0], position[1]);
        var myOptions = {
            zoom: 10,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById("googleMap"), myOptions);
    
        marker = new google.maps.Marker({
            position: latlng,
            map: map,
            title: "Latitude:"+position[0]+" | Longitude:"+position[1]
        });
    
        google.maps.event.addListener(map, 'click', function(event) {
            var result = [event.latLng.lat(), event.latLng.lng()];
            transition(result);
        });

        fetch('http://localhost/test_project/controller/json_parsing.php')
        .then(response => response.json())
        .then(data => {
            data.forEach(element => {
                AddLocation(element['lat'], element['lng'], element['title'], element['kategori'], element['kategori']);
            });
        });

    }
    
    google.maps.event.addDomListener(window, 'load', initialize);
    

    function AddLocation(lt, lg, tit, urm, kat) {
        var d = new google.maps.LatLng(lt, lg);
        var urm = urm;

        if (urm == 'Pendidikan') {
            urm = 'asset/img/pendidikan.jpg';
        }else if(urm == 'Kuliner'){
            urm = 'asset/img/kuliner.jpg';
        }else if(urm == 'Pariwisata'){
            urm = 'asset/img/pariwisata.jpg';
        }

        var icon = {
            url: urm,
            size: new google.maps.Size(50, 50),
            scaledSize: new google.maps.Size(50, 50),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(0, 0)
        };
        var markerAdd = new google.maps.Marker({
            position: d,
            map: map,
            title: tit,
            category: kat
            
        });
        markerAdd.setIcon(icon);
    }
    
    var numDeltas = 100;
    var delay = 5; //milliseconds
    var i = 0;
    var deltaLat;
    var deltaLng;
    
    function transition(result){
        i = 0;
        deltaLat = (result[0] - position[0])/numDeltas;
        deltaLng = (result[1] - position[1])/numDeltas;
        moveMarker();
    }
    
    function moveMarker(){
        position[0] += deltaLat;
        position[1] += deltaLng;
        var latlng = new google.maps.LatLng(position[0], position[1]);
        marker.setTitle("Latitude:"+position[0]+" | Longitude:"+position[1]);
        marker.setPosition(latlng);
        if(i!=numDeltas){
            i++;
            setTimeout(moveMarker, delay);
        }
        addresDetail(position[0], position[1])
    }

    function addresDetail(lat, lng) {
        var geocoder = new google.maps.Geocoder();
        var latlng = {lat: lat, lng: lng};
        geocoder.geocode({'location': latlng}, function(results, status) {
            if (status === 'OK') {
                if (results[0]) {
                    var address = results[0].formatted_address;
                    document.getElementById('address').value = address;
                    document.getElementById('latlong').value = latlng.lat+", "+latlng.lng;
                    document.getElementById('lat').value = latlng.lat;
                    document.getElementById('lng').value = latlng.lng;
                } else {
                    console.log('No results found');
                }
            }
        });
    }

    function getAddressFunction(lat, lng){
        var myLatlng = new google.maps.LatLng(lat, lng);
        map.setZoom(13);
        map.setCenter(myLatlng);
        console.log(lat+", "+lng);
    }

    function filterMaps() {
        var radio = document.getElementById("radio");
        var radioValue = radio.value;
        var filteredMarkers = markers.filter(function(marker) {
            return marker.title === radioValue;
        });
    }

    </script>



</body>
</html>