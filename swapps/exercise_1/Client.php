<?php

require_once 'Server.php';
/**
 * Created by PhpStorm.
 * User: eugene
 * Date: 11/16/16
 * Time: 2:00 PM
 */
class Client
{
    private $request_url;
    private $category;


    // this is the search term the client will provide to the server
    private  $query;

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param mixed $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }


    /**
     * Sends a request to server from sample URL
     */
    public function sendGetRequest($url){

        //  echo $sighnedUrl;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,
            $url
        );
        $content = curl_exec($ch);

        curl_close($ch);

        return true;
    }

    /**
     * @return mixed
     */
    public function getRequestUrl()
    {
        return $this->request_url;
    }

    /**
     * @param mixed $request_url
     */
    public function setRequestUrl($request_url)
    {
        $this->request_url = $request_url;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }


}