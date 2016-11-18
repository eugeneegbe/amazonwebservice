<?php

/**
 * Created by PhpStorm.
 * User: eugene
 * Date: 11/16/16
 * Time: 1:37 PM
 */
//Enter your IDs

class Server {

    private static $response_status;
    private  $signed_url;
    private $itemPage;

    /**
     * check in client request of the @next parameter is is found there
     *
     */
    public static function checkForNextParameter($clientUrl){

       self::sendGetRequest($clientUrl);

        if (isset($_GET["next"]))
        {
            return true;
        }else{
            return false;
        }

    }


    /**
     * @params $page ,  $searchItem , $category
     * This is where we do the Url signing with all the parameters
     * @return $signedResponse
     */
    public static function signRequest($page_number, $search_term, $category) {

        define("AWS_ASSOCIATE_TAG", "swapps-20");
        define("AWS_API_KEY", "AKIAIZT5IMI6CLTFZMGQ");
        define("AWS_API_SECRET_KEY", "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");
        define("RESPONSE_GROUP", "Images,ItemAttributes,EditorialReview");
        define("OPERATION", "ItemSearch");

        // format string to be signed
        //fials with Power=Binding:Kindle%20Edition attribute : No reason yet

        $url = "GET\nwebservices.amazon.com\n/onca/xml\n" .
            "AWSAccessKeyId=" . AWS_API_KEY .
            "&AssociateTag=" . AWS_ASSOCIATE_TAG .
            "&ItemPage=". $page_number .
            "&Keywords=" . rawurlencode(ucwords(str_replace("-", " ", $search_term))) .
            "&Operation=" . OPERATION .
            "&ResponseGroup=" . str_replace(",", "%2C", RESPONSE_GROUP) .
            "&SearchIndex=" . str_replace(",", "%2C", $category) .
            "&Service=AWSECommerceService" .
            "&Timestamp=" . rawurlencode(gmdate("Y-m-d\TH:i:s\Z")) .
            "&Version=2011-08-01";

        $signature = base64_encode(hash_hmac("sha256", $url, AWS_API_SECRET_KEY, True));

        $signature = str_replace("%7E", "~", rawurlencode($signature));

       $signed_signature = str_replace("GET\nwebservices.amazon.com\n/onca/xml\n", "http://webservices.amazon.com/onca/xml?", $url) . "&Signature=" . $signature;
        return $signed_signature;
    }

    /**
     * Send get request to signed URL
     *
     */
    public function sendGetRequest($sighnedUrl)
    {

      //  echo $sighnedUrl;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,
            $sighnedUrl
        );
        $content = curl_exec($ch);

        //echo $content;
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        self::setResponseStatus($httpcode);

        curl_close($ch);

        return $content;
    }

    /**
     * The server uses this piece of code to parse results
     */
    public function parseXmlResultSet($parsed_xml){

        $xml = simplexml_load_string($parsed_xml);
 //       $json = json_encode($xml , JSON_PRETTY_PRINT);

//        print_r($json);

        return $xml;
    }

    /**
     * Arrange Json data in the result recommended format
     */
    public function getJsonResultItems($xml_response){

        print("<table>");
        if(!empty($xml_response)) {
            foreach ($xml_response->Items->Item as $current) {
                print("<td><font size='-1'><b>" . $current->ItemAttributes->Title . "</b>");
                if (isset($current->ItemAttributes->Author)) {
                    print("<br>Title: " . $current->ItemAttributes->Title);
                }
                if (isset($current->MediumImage->URL)) {
                    print("<br>Image: " . $current->MediumImage->URL);
                }

                if (isset($current->EditorialReviews->EditorialReview->Content)) {
                        print("<br>Review: " . $current->EditorialReviews->EditorialReview->Content);
                    }

                if (isset($current->ItemLinks->ItemLink->URL)) {
                        print("<br>ItemLink: " . $current->ItemLinks->ItemLink->URL);
                    }
                if (isset($current->ItemAttributes->PublicationDate)) {
                        print("<br>Publication Date: " . $current->ItemAttributes->PublicationDate);
                    }

                    else {
                        print("<center>No matches found.</center>");
                    }

            }

        }

//        $json = json_encode($xml_response , true);
//
////        var_dump($json);
//        return $json;

    }
    /**
     * Prepares the response in case it is a success depending on the return status code
     */
    public static function prepareSuccessResponse($json){

           var_dump($json);

    }

    /**
     * Prepares the failure in case it is a failure depending on the return status code
     */
    public static function prepareFailureResponse(){

        echo self::$response_status;

    }


    /**
     * returns the response to the client
     */
    public static function returnResponse(){


        if(self::$response_status == 200){



        }else{

            // error response

        }

    }

    /**
     * gets the response item page and sets the current page on the server
     */
    public static function VerifyRequestITemPage($signedURL)
    {
        $query = parse_url($signedURL, PHP_URL_QUERY);
        parse_str($query, $params);

        if (isset($_GET["ItemPage"]))
        {
            self::setItemPage($params['ItemPage']);
            return true;

        }else{

            return false;
        }

    }
    /**
     * gets the response TotalPages
     */
    public static function getResponseTotalPages($parsed_xml){

       // $value = (int) $parsed_xml->Items->ItemPage;
        $value2 = (int) $parsed_xml->Items->TotalPages;

       // echo $value;
        return $value2;
    }

    /**
     * @return mixed
     */
    public function getResponseStatus()
    {
        return self::$response_status;
    }

    /**
     * @param mixed $response_status
     */
    public static function setResponseStatus($response_stat)
    {
        self::$response_status = $response_stat;
    }

    /**
     * @return mixed
     */
    public function getSignedUrl()
    {
        return $this->signed_url;
    }

    /**
     * @param mixed $signed_url
     */
    public function setSignedUrl($signed_url)
    {
        $this->signed_url = $signed_url;
    }

    /**
     * @return mixed
     */
    public function getItemPage()
    {
        return $this->itemPage;
    }

    /**
     * @param mixed $itemPage
     */
    public function setItemPage($itemPage)
    {
        $this->itemPage = $itemPage;
    }


}