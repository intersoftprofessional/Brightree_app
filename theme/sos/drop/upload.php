<?php
$uploaddir = 'news/images/';

// The posted data, for reference
$file = $_POST['value'];
$name = $_POST['name'];

// Get the mime
$getMime = explode('.', $name);
$mime = end($getMime);

// Separate out the data
$data = explode(',', $file);

// Encode it correctly
$encodedData = str_replace(' ','+',$data[1]);
$decodedData = base64_decode($encodedData);

// You can use the name given, or create a random name.
// We will create a random name!

$randomName = $getMime[0].'_'.substr_replace(sha1(microtime(true)), '', 4).'.'.$mime;
if(file_put_contents($uploaddir.$randomName, $decodedData)) {
	$path = $uploaddir.$randomName;
        // Include the UberGallery class
        include('../upload_script/resources/UberGallery.php');
        $save_pdth = $_SERVER['DOCUMENT_ROOT'].'/projects/chris/sos/admin/uploads/news/160x115/';
        // Initialize the UberGallery object
        $gallery = new UberGallery();
        $gallery->_createThumbnail($_SERVER['DOCUMENT_ROOT'].'/projects/chris/sos/admin/theme/sos/drop/'.$path, '160', '115',80, $save_pdth); 
	echo $path.":uploaded successfully";
}
else {
	// Show an error message should something go wrong.
	echo "Something went wrong. Check that the file isn't corrupted";
}


?>
