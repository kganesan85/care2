<?php
    include('simple_html_dom.php');
    
    $url = 'http://www.care2.com';
    echo "Processed URL : $url<br>";
    
    $sizeCSS =0;
    $totalNumResourcesCSS = 0;
    
    $sizeJS =0;
    $totalNumResourcesJS = 0;
    
    $totalSize = 0;
    $totalNumResources = 0;
    
    // check to see if the URL points to an HTML page
   if (!check_if_html($url))
    {
        $totalSize = get_remote_file_size($url);
        
        echo "Final Total Download Size: $totalSize Bytes.<br>";
        
        $totalNumResources += 1;  //a single resource is still an HTTP request
        
        echo "Final total HTTP requests: $totalNumResources.<br>" ;
        
        return;
        
    }
    
    
    //at this point we know we are dealing with an HTML document
    
    $totalNumResources += 1;
    
    $html = file_get_html($url);
     // find all images:
    foreach($html->find('img') as $element){
        
        $size = get_remote_file_size($element->src);
		
        $totalSize = $totalSize + $size;
        
        $totalNumResources += 1;
            }
    echo "Images: $totalNumResources Request, $totalSize Bytes<br>";
    

    // Find all CSS:
    foreach($html->find('link') as $element)
    {
        
        if (strpos($element->href,'.css') !== false) {
            
            $sizeCSS = get_remote_file_size($element->href);
            
            $totalSize = $totalSize + $sizeCSS;
            
            $sizeCSS += $sizeCSS;
            
             $totalNumResources += 1;
            
            $totalNumResourcesCSS += 1;
        }
       
    }
    echo "CSS: $totalNumResourcesCSS Request, $sizeCSS Bytes<br>";
  
    
    //find all javascript:
    foreach($html->find('script') as $element)
    {
        
        //check to see if it is javascript file:
        if (strpos($element->src,'.js') !== false) {
            
            $sizeJS = get_remote_file_size($element->src);
            $totalSize = $totalSize + $sizeJS;
             $sizeJS += $sizeJS;
            
            $totalNumResources += 1;
            $totalNumResourcesJS += 1;
        
        }
    }
     echo "JS: $totalNumResourcesJS Request, $sizeJS Bytes<br>";
    
    echo "Final total download size: $totalSize Bytes<br>" ;
    
    echo "Final total HTTP requests: $totalNumResources<br>";
    
    function get_remote_file_size($url) {
        $headers = get_headers('http://www.care2.com', 1);
        
        if (isset($headers['Content-Length'])) return $headers['Content-Length'];
        
        //this one checks for lower case "L" IN CONTENT-length:
        if (isset($headers['Content-length'])) return $headers['Content-length'];
        
        $c = curl_init();
        
        curl_setopt_array($c, array(
                                    CURLOPT_URL => $url,
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_HTTPHEADER => array('User-Agent: Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3'),
                                    ));
        
        curl_exec($c);
        
        $size = curl_getinfo($c, CURLINFO_SIZE_DOWNLOAD);
        
      
        
        return $size;
        
        curl_close($c);
        
    }
    
    
    /*checks content type header to see if it is
     an HTML page...
     */
    
    function check_if_html($url){
        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_NOBODY, TRUE);
        
        $data = curl_exec($ch);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE );
        //var_dump( $contentType);
        
        curl_close($ch);
        
        if (strpos($contentType,'text/html') !== false)
            return TRUE; 	// this is HTML, yes!
        else
            return FALSE;
    }

?>