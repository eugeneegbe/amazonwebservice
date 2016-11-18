<?php


/**
 * Created by PhpStorm.
 * User: eugene
 * Date: 11/16/16
 * Time: 3:44 PM
 */

require_once 'Client.php';
require_once 'Server.php';

$server = new Server();
$client = new Client();

//we set some default values for the the client's search querry and category to sign request

 $client->setQuery("Potter");
 $client->setCategory("Books");


    // client sends get request to url
    $client->setRequestUrl("https://swapps.biz/hiring/112016/671674439/books.php?next");
    $client->sendGetRequest($client->getRequestUrl());

// server: if next parameter is provided
    if($server->checkForNextParameter($client->getRequestUrl())) {

        //Server sends a GET request to that URL
        $server->sendGetRequest($client->getRequestUrl());

    }else {

        //store client querry and category in variables
        $client_querry = $client->getQuery();
        $client_category = $client->getCategory();

        // server: build signed request to http://webservices.amazon.com/onca/xml
        $signed_url = $server->signRequest(1 , $client_querry , $client_category);

        // Server: sends get request to the signed URL

        $xml_response_data = $server->sendGetRequest($signed_url);

         }

         //Server: if response return code from amazon is 200
         if($server->getResponseStatus() == 200) {

             //Server:parses results set
             $parsed_results = $server->parseXmlResultSet($xml_response_data);

             //Server: get result items server retrieves its Author, Editorial Review Content, Medium
             $server->getJsonResultItems($parsed_results);

             //check if the Itempage is defined and get totalPages and store in Variables

             $isItemPage = $server->VerifyRequestITemPage($signed_url);

             $totalPages = $server->getResponseTotalPages($parsed_results);

             $itemPageNumber = $server->getItemPage();

             //Server: If the Amazon request Item Page is undefined & total pages > 1 || itempage is defined and less than total pages
             if((!($isItemPage) && $totalPages > 1 ) || (($isItemPage) && $itemPageNumber < $totalPages)){

                 //Server: build signed url with page = page + 1
                 //set it as the next url for the request :: return signed URl for server
                  $signed_server_url = $server->signRequest( $itemPageNumber+1, $client_querry , $client_category);
                  $server->setSignedUrl($signed_server_url);

             }

//            $server->getResponseITemPage($parsed_results);


             //Server: prepares a success response
//             $server->prepareSuccessResponse($server->getJsonResultItems($xml_response_data));

             //Server: return the response
            $server->returnResponse();

         }else {

             echo "in else now";
             //Server: else ie response code !==200
             //server: prepares error response

             $server->prepareFailureResponse();
             //Server: returns error response
             $server->returnResponse();

         }

//echo $signatureUrl;
exit(0);
