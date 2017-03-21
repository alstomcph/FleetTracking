<?php
    $sql        = isset($_GET['query']) ? $_GET['query'] : "SELECT * FROM points";
    $sql       .= isset($_GET['limit']) ? ' ORDER BY timestamp DESC LIMIT ' . $_GET['limit'] : "";
    $format     = isset($_GET['format']) ? $_GET['format'] : "JSON";
    
    $servername = "localhost";
    $username   = "readonly";
    $password   = "readonly";
    $dbname     = "gps";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 
    if($result = $conn->query($sql)){
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        
        switch($format){
            case "JSON":
                echo json_encode($rows);
                break;
            case "KML":
                writeKML($rows);
                break;
            default:
                echo "format type invalid";
        }
        $result->free();
    }
    else{
        echo "error in query: " . $sql;
    }
    $conn->close();

    //function definitions =====
    function writeKML($rows){
        $coordinates = '';
        foreach($rows as $row){
            $coordinates = $coordinates . $row['longitude'] . "," . $row['latitude'] . ",0" . "\r\n";
        }
       
        $kml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><kml></kml>');
        $kml->addAttribute('xmlns', 'http://www.opengis.net/kml/2.2');
        $kml_Document = $kml->addChild('Document');
        $kml_Document->addChild('name','route');
        $kml_Document->addChild('description','description');
        
        $kml_folder = $kml_Document->addChild('Folder');
        $kml_folder->addChild('name','Vehicle #01');
        
        //add style====
        $kml_style = $kml_folder->addChild('Style');
        $kml_style->addAttribute('id','linestyle');
        $kml_linestyle = $kml_style->addChild('LineStyle');
        $kml_linestyle->addChild('color','AA1400FF');
        $kml_linestyle->addChild('width','7');
        $kml_polystyle = $kml_style->addChild('PolyStyle');
        $kml_polystyle->addChild('color','7f00ff00');

        $kml_style2 = $kml_folder->addChild('Style');
        $kml_style2->addAttribute('id','pointstyle');
        $kml_iconstyle = $kml_style2->addChild('IconStyle');
        $kml_icon = $kml_iconstyle->addChild('Icon');
        $kml_icon->addChild('href','ambulance2.png');

         //add path====
        $kml_placemark = $kml_folder->addChild('Placemark');
        $kml_placemark->addChild('name','Path');
        $kml_placemark->addChild('description','Description for Vehicle #01 path');
        $kml_placemark->addChild('styleUrl','#linestyle');
        $kml_linestring = $kml_placemark->addChild('LineString');
        $kml_linestring->addChild('extrude', '1');
        $kml_linestring->addChild('tessellate', '1');
        $kml_coordinates = $kml_linestring->addChild('coordinates', $coordinates);

        //add point====
        $kml_placemark2 = $kml_folder->addChild('Placemark');
        $kml_placemark2->addChild('name','Location');
        $kml_placemark2->addChild('description','Description for Vehicle #01 location');
        $kml_placemark2->addChild('styleUrl','#pointstyle');

        $kml_point = $kml_placemark2->addChild('Point');
        $kml_point->addChild('coordinates', $rows[0]['longitude'] . "," . $rows[0]['latitude'] . ",0");
        
        print $kml->asXML();
    }
?>