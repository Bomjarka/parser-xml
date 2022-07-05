<?php

namespace App\Services;

class XMLService
{

    public function getXMLObject($path)
    {
        $xmlDataString = file_get_contents($path);

        return simplexml_load_string($xmlDataString);
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getDataFromXMLAsArray($path)
    {
        $jsonObject = json_encode($this->getXMLObject($path));

        return json_decode($jsonObject, true);
    }
}
