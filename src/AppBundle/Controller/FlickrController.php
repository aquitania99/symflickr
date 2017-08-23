<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FlickrController extends Controller
{
    private $apiKey;
    private $apiSecret;
    private $apiUrl;
    protected $keyword;
    protected $method;
    protected $format;

    public function indexAction(Request $request)
    {
        if($request->isMethod('POST'))
        {
            $this->apiKey = $this->container->getParameter('flickr_key');
            $this->apiSecret = $this->container->getParameter('flickr_secret');
            $this->apiUrl = $this->container->getParameter('flickr_url');

            $this->keyword = urlencode($request->request->get('keyword'));
            $this->method = 'flickr.photos.search';
            $url = $this->apiUrl."api_key=".$this->apiKey."&method=".$this->method."&text=".$this->keyword."&format=php_serial";
            $response = $this->curlCall($url);

            return $this->render('@App/layout.html.twig', array('name' => $response));
        }
        else {
            return $this->render('@App/layout.html.twig', array('name' => 'BLAH!'));
        }

    }

    public function searchAction( Request $request)
    {

    }

    protected function curlCall($url) {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($curl);
        curl_close($curl);
        $result = unserialize ($output);
        return $result;
    }


}
