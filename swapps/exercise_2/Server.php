<?php

/**
 * Created by PhpStorm.
 * User: eugene
 * Date: 11/16/16
 * Time: 1:37 PM
 */
//Enter your IDs

class Server {

    public static $response_status;
    private  $signed_url;
    private $itemPage;
    private static $json_response_data;

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

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,
            $sighnedUrl
        );
        $content = curl_exec($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        self::$response_status = $httpcode;

        curl_close($ch);

        return $content;
    }

    /**
     * The server uses this piece of code to parse results
     */
    public function parseXmlResultSet($parsed_xml){

        $xml = simplexml_load_string($parsed_xml);

        return $xml;
    }

    /**
     * Arrange Json data in the result recommended format
     */
    public function getJsonResultItems($xml_response){

        //NOTE: THE PRINT STATMENTS ARE USED TO TEST FOR THE VALIDITY OF THE VARIABLE VALUES

//        print  "<h2>THE SEARCH RESULTS ARE DISPLAYED BELOW</h2>";
//        print("<table>");


//        if(!empty($xml_response)) {
//
//            foreach ($xml_response->Items->Item as $current) {
//
//                if (isset( $current->ItemAttributes->Title)) {
//
//                    print("<br><b>Title:</b> " . $current->ItemAttributes->Title);
//                }
//
//                if (isset($current->ItemAttributes->Author)) {
//
//                    print("<br><b>Author</b>: " . $current->ItemAttributes->Author);
//                }
//
//                if (isset($current->MediumImage->URL)) {
//
//                    print("<br><b>Image:</b><img src= '" . $current->MediumImage->URL."'");
//                }
//
//                if (isset($current->EditorialReviews->EditorialReview->Content)) {
//
//                    print("<br><b>Review:</b> " . $current->EditorialReviews->EditorialReview->Content);
//                }
//
//                if (isset($current->ItemLinks->ItemLink->URL)) {
//
//                 print("<br><b>ItemLink:</b> " . $current->ItemLinks->ItemLink->URL);
//
//                }
//                if (isset($current->ItemAttributes->PublicationDate)){
//
//                   print("<br><b>Publication Date:</b> " . $current->ItemAttributes->PublicationDate);
//                    echo "<br>";
//                }
//
//                    else {
//                            echo "<h3> Sorry There is no Result Set For you Request</h3>";
//                    }
//            }
//
//
//        }
        if(!empty($xml_response)) {


            foreach ($xml_response->Items->Item as $current) {
                print('<div style="border-style: solid;border-width: thin;border-radius: 3px;border-color: #333333; padding: 17px">');
                print('<div>');
                print('<div style="float:left; width:20%" >');

                if (isset( $current->ItemAttributes->Title)) {

                    print('<h2>'. $current->ItemAttributes->Title.'</h2>');

                }
                if (isset($current->MediumImage->URL)) {

                    print("<img src='". $current->MediumImage->URL."' style='
                     width: 150px;
                    height: 150px;
                    background-size: cover;
                    background-position: center;
                    border-radius: 50%;'>");
                }
                print('</div>'); //end of title and image div

                print('<div style="float:left; width:80%">');// start of author public date description

                if (isset($current->ItemAttributes->Author)) {

                    print('<span>'.$current->ItemAttributes->Author .'</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
                }
                if (isset($current->ItemAttributes->PublicationDate)){

                    print('<span>'.$current->ItemAttributes->PublicationDate .'</span>');
                }
                if (isset($current->EditorialReviews->EditorialReview->Content)) {

                    print("<p>" . $current->EditorialReviews->EditorialReview->Content."</p>");
                }

                print('</div>');

                print('</div>'); //end large div container
                print('<div style="clear:both"></div>');
                print('</div>');

            }

        }
    }

    /**
     * Prepares the failure in case it is a failure depending on the return status code
     */
    public static function prepareFailureResponse(){

        echo "<b>OOps! Something wentWrong \n please check  status code ==> </b>"."<b>".self::$response_status."</b>";

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

    public static function getResponseStatus(){

        return self::$response_status;
    }

    /**
     * @return mixed
     */
    public static function getJsonResponseData()
    {
        return self::$json_response_data;
    }


}