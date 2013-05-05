<html>
  <head>
    <title>My Map using php mysql </title>
    <link rel="stylesheet" href="style.css"/>
    <script src="http://maps.googleapis.com/maps/api/js?sensor=false"
            type="text/javascript"></script>
    <script type="text/javascript">
    //<![CDATA[
    var map;
    var markers=[];
    var infoWindow;
    var locationSelect;

    function load(){
      map=new google.maps.Map(document.getElementById("map"), {
        center: new google.maps.LatLng(20,80),
        zoom: 4,
        mapTypeId: 'roadmap',
        mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU}
      });
      infoWindow=new google.maps.InfoWindow();

      locationSelect=document.getElementById("locationSelect");
      locationSelect.onchange = function() {
        var markerNum=locationSelect.options[locationSelect.selectedIndex].value;
        if (markerNum!="none"){
          google.maps.event.trigger(markers[markerNum], 'click');
        }
      };
   }
    function searchLocations(str) {
     var address=document.getElementById(str).value;
     var geocoder=new google.maps.Geocoder();
     geocoder.geocode({'address': address}, function(results, status) {
       if (status==google.maps.GeocoderStatus.OK) {

        searchLocationsNear(results[0].geometry.location);

       } else {
         alert(address + ' not found');
       }
     });
   }

   function clearLocations() {
     infoWindow.close();
     for (var i =0;i<markers.length; i++) {
       markers[i].setMap(null);
     }
     markers.length = 0;
     locationSelect.innerHTML="";
     var option = document.createElement("option");
     option.value = "none";
     option.innerHTML = "";
     locationSelect.appendChild(option);
   }

   function searchLocationsNear(center) {
     clearLocations();

     var radius = 30;
     var searchUrl = 'mysqlsearch.php?lat=' + center.lat() + '&lng=' + center.lng() + '&radius=' + radius;
     downloadUrl(searchUrl, function(data) {
       var xml = parseXml(data);
       var markerNodes = xml.documentElement.getElementsByTagName("marker");
       var bounds = new google.maps.LatLngBounds();
       for (var i = 0; i < markerNodes.length; i++) {
         var name = markerNodes[i].getAttribute("name");
         var address = markerNodes[i].getAttribute("address");
         var distance = parseFloat(markerNodes[i].getAttribute("distance"));
         var latlng = new google.maps.LatLng(
              parseFloat(markerNodes[i].getAttribute("lat")),
              parseFloat(markerNodes[i].getAttribute("lng")));

         //createOption(name, distance, i);
         createMarker(latlng, name, address);
         bounds.extend(latlng);
       }
       map.fitBounds(bounds);
       locationSelect.style.visibility = "visible";
       locationSelect.onchange = function() {
         var markerNum = locationSelect.options[locationSelect.selectedIndex].value;
         google.maps.event.trigger(markers[markerNum], 'click');
       };
      });
    }

    function createMarker(latlng, name, address) {
      var html = "<b>" + name + "</b> <br/>" + address;
      var marker = new google.maps.Marker({
        map: map,
        position: latlng
      });
      google.maps.event.addListener(marker, 'click', function() {
        infoWindow.setContent(html);
        infoWindow.open(map, marker);
      });
      markers.push(marker);
    }

    function createOption(name, distance, num) {
      var option = document.createElement("option");
      option.value = num;
      option.innerHTML = name + "(" + distance.toFixed(1) + ")";
      locationSelect.appendChild(option);
    }

    function downloadUrl(url, callback) {
      var request = window.ActiveXObject ?
          new ActiveXObject('Microsoft.XMLHTTP') :
          new XMLHttpRequest;

      request.onreadystatechange = function() {
        if (request.readyState == 4) {
          request.onreadystatechange = doNothing;
          callback(request.responseText, request.status);
        }
      };

      request.open('GET', url, true);
      request.send(null);
    }

    function parseXml(str) {
      if (window.ActiveXObject) {
        var doc = new ActiveXObject('Microsoft.XMLDOM');
        doc.loadXML(str);
        return doc;
      } else if (window.DOMParser) {
        return (new DOMParser).parseFromString(str, 'text/xml');
      }
    }

    function doNothing() {}

    //]]>
  </script>
  </head>

  <body onload="load()">

    <div><select id="locationSelect" style="width: 100%; visibility:hidden"></select></div>
    <div id="map" style="width: 540px; height: 480px"></div>
    <div id="add" >
	<h1>ADDRESS</h1>
	<h2>DURGESH KUMAR</h2>
	<div id="add1"><h4><option class="cc" href="" id="addressInput1" onclick="searchLocations('addressInput1')" value="Patna Golghar">Location1</option>Golghar, Patna, Bihar,India</h4></div>
	<div id="add2"><h4><option href="" id="addressInput2" onclick="searchLocations('addressInput2')" value="Taj Mahal, Agra">Location2</option>Taj Mahal, Agra, Uttar Pradesh 282001, India</br></h4></div>
	<div id="add3"><h4><option href="" id="addressInput3" onclick="searchLocations('addressInput3')" value="Qutub Minar,Mehrauli">Location3</option>Qutub Minar, Mehrauli, New Delhi, Delhi 110016, India</br></h4></div>
	<div id="add4"><h4><option href="" id="addressInput4" onclick="searchLocations('addressInput4')" value="Red Fort">Location4</option>Red Fort, Chandni Chowk,Netaji Subhash Rd, New Delhi, Delhi 110006, India</br></h4></div>
	

</div>
  </body>
</html>
