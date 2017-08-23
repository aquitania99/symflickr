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

    public function indexAction()
    {
        $this->apiKey = $this->container->getParameter('flickr_key');
        $this->apiSecret = $this->container->getParameter('flickr_secret');
        $this->apiUrl = $this->container->getParameter('flickr_url');

//        $this->keyword = urlencode($request->request->get('keyword'));
        $this->method = 'flickr.photos.getRecent';
        $extras = urlencode('tags, description');
        $resultsPerPage = 10;
        $url = $this->apiUrl."api_key=".$this->apiKey."&method=".$this->method."&per_page=".$resultsPerPage."&extras=".$extras."format=json";
        $response = $this->curlCall($url);
        $photos = $response['photos']['photo'];
        dump($url);
        return $this->render('@App/layout.html.twig', array('flickr' => $response));
    }

    public function searchAction(Request $request)
    {
        if($request->isMethod('POST'))
        {
            $this->apiKey = $this->container->getParameter('flickr_key');
            $this->apiSecret = $this->container->getParameter('flickr_secret');
            $this->apiUrl = $this->container->getParameter('flickr_url');
            $extras = urlencode('tags, description');
            $resultsPerPage = 10;

            $this->keyword = urlencode($request->request->get('keyword'));
            $this->method = 'flickr.photos.search';
            $url = $this->apiUrl."api_key=".$this->apiKey."&method=".$this->method."&text=".$this->keyword."&extras=".$extras."&per_page=".$resultsPerPage."&format=php_serial";
            $response = $this->curlCall($url);
            dump($response);
            return $this->render('@App/layout.html.twig', array('flickr' => $response));
        }
        else {
            return $this->render('@App/layout.html.twig', array('flickr' => 'BLAH!'));
        }
    }

    protected function curlCall($url) {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36');
        $output = curl_exec($curl);
        $retcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        dump($output);
        curl_close($curl);
        if ($retcode == 200) {
            $json = substr( $output, strlen( "jsonFlickrApi(" ), strlen( $output ) - strlen( "jsonFlickrApi(" ) - 1 );
            $data = json_decode( $json, true );
            return $data;
        } else {
            return null;
        }
    }

}
