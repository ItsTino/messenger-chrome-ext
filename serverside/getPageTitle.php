<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');



function file_get_contents_curl($url)
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}



if (isset($_POST["url"])) {
  #Sanitise and continue  
  $url = $_POST["url"];
  $cleanURL = filter_var($url, FILTER_SANITIZE_URL);

  getPageTitle($cleanURL);
} else {
  #No URL, die  
  print_r('fail');
  die;
}

function getPageTitle($url)
{
  $html = file_get_contents_curl("$url");

  //parsing begins here:
  $doc = new DOMDocument();
  @$doc->loadHTML($html);
  $nodes = $doc->getElementsByTagName('title');

  //get and display what you need:
  $title = $nodes->item(0)->nodeValue;
  $title = filter_var($title, FILTER_DEFAULT);

  try {
    if (!empty($title)) {
      echo $title;

    } else {
      echo $url;
    }
  } catch (\Exception $ex) {
    echo $url;
  }
}


