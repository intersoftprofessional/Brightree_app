<?php

// set error reporting level
if (version_compare(phpversion(), '5.3.0', '>=') == 1)
  error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
else
  error_reporting(E_ALL & ~E_NOTICE);

function bytesToSize1024($bytes, $precision = 2) {
    $unit = array('B','KB','MB');
    return @round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), $precision).' '.$unit[$i];
}

if (isset($_FILES['myfile'])) {
    $sFileName = $_FILES['myfile']['name'];
    $sFileType = $_FILES['myfile']['type'];
    $sFileSize = bytesToSize1024($_FILES['myfile']['size'], 1);
    if (file_exists("gallery-images/10/" . $_FILES["myfile"]["name"]))
    {
        $file_name = explode('.', $_FILES["myfile"]["name"]);
        $name = '';
        foreach($file_name as $id=>$value)
        {   
            if($id == (count($file_name)-1))
                $name .= "_".rand().".".$value;
            else
                $name .= $value;
        }
    }
    else
        $name = $_FILES["myfile"]["name"];
    if (!file_exists("gallery-images/10")) 
    {
         mkdir('gallery-images/10',0777);
    }
    move_uploaded_file($_FILES["myfile"]["tmp_name"],"gallery-images/10/" . $name);
    // Include the UberGallery class
    include('resources/UberGallery.php');
    
    // Initialize the UberGallery object
    $gallery = new UberGallery();
    
    // Initialize the gallery array
    $galleryArray = $gallery->readImageDirectory('gallery-images/10');
    echo <<<EOF
<div class="s">
    <p>Your file: {$sFileName} has been successfully received.</p>
    <p>Type: {$sFileType}</p>
    <p>Size: {$sFileSize}</p>
</div>
EOF;
    
} else {
    echo '<div class="f">An error occurred</div>';
}