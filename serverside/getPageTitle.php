<?php
#Set headers to prevent CORS errors
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');


/*
Function to fetch specific URL using curl
*/

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

/*
Function to get the URL page title
*/
function getPageTitle($url)
{
  #Fetches page data using file_get_contents_curl function
  $html = file_get_contents_curl("$url");

  #Parse through document 
  $doc = new DOMDocument();
  @$doc->loadHTML($html);
  $nodes = $doc->getElementsByTagName('title');

  #Get the title from the page elements
  $title = $nodes->item(0)->nodeValue;
  $title = filter_var($title, FILTER_DEFAULT);

  /*
  If we have recovered a title from the webpage return it to the client to be displayed by the chrome extension
  If a title was not recovered for any reason, return the original url.
  This allows us to gracefully fail without the user seeing any errors.
  */
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


if (isset($_POST["url"])) {
  #Sanitise URL then continue   
  $url = $_POST["url"];
  $cleanURL = filter_var($url, FILTER_SANITIZE_URL);

  getPageTitle($cleanURL);
} else {
  /*
  If no URL is specified it is likely the request was not sent by the chrome extension.
  Simply kill the script, if it is a legitmate request it will be remade by the client.
  */
  print_r('fail');
  die;
}