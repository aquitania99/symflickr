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
    protected $option;
    protected $method;
    protected $format;

    public function indexAction()
    {
        $method = 'flickr.photos.getRecent';
        $getImages = $this->flickrAction($method);
        return $this->render('@App/index.html.twig', array('flickr' => $getImages['flickrResponse']));
    }

    public function searchAction(Request $request, $page=null)
    {
            if ($request->isMethod('POST')) {
                $keyword = urlencode($request->request->get('keyword'));
            }
            else {
                $page = urlencode($request->query->get('page'));
                $keyword = urlencode($request->query->get('keyword'));
            }
            $flickrPage = !$page ? "": $page;
            $method = 'flickr.photos.search';

            $response = $this->flickrAction($method, $flickrPage, $keyword);

            return $this->render('@App/search.html.twig', array('flickr' => $response['flickrResponse'], "keyword" => $keyword));

    }

    public function getPhotoAction(Request $request, $photo_id)
    {
            $photo_secret = $request->query->get('photo_secret');
            $method = 'flickr.photos.getInfo';
            $option = ["photo_id" => $photo_id, "photo_secret" => $photo_secret];
            $response = $this->flickrAction($method, "", $option);
            $keyword = $response['flickrResponse']['photo']['title']['_content'];
            $result = $this->getDetails($response);
            dump($result);
            
            return $this->render('@App/detail.html.twig', array('flickr' => $response['flickrResponse'], "keyword" => $keyword));

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
        curl_close($curl);
        if ($retcode == 200) {
            $json = substr( $output, strlen( "jsonFlickrApi(" ), strlen( $output ) - strlen( "jsonFlickrApi(" ) - 1 );
            $data = json_decode( $json, true );
            return $data;
        } else {
            return null;
        }
    }

    protected function flickrAction($method, $page=null, $option=null) {
        $this->apiKey = $this->container->getParameter('flickr_key');
        $this->apiSecret = $this->container->getParameter('flickr_secret');
        $this->apiUrl = $this->container->getParameter('flickr_url');
        $extras = urlencode('tags, description');
        $resultsPerPage = 10;
        if ( !is_array($option) ) {
          $this->option = urlencode($option);
        }
        else $this->option = $option;
//        dump($this->option);

        $this->method = $method;

        switch ($this->method) {
          case 'flickr.photos.search':
            $url = $this->apiUrl."api_key=".$this->apiKey."&method=".$this->method."&text=".$this->option."&page=".$page."&extras=".$extras."&per_page=".$resultsPerPage."&format=json";
            $response = $this->curlCall($url);
            break;

            case 'flickr.photos.getRecent':
              $url = $this->apiUrl."api_key=".$this->apiKey."&method=".$this->method."&extras=".$extras."&page=".$page."&per_page=".$resultsPerPage."&format=json";
              $response = $this->curlCall($url);
              break;

            case 'flickr.photos.getPopular':
              $url = $this->apiUrl."api_key=".$this->apiKey."&method=".$this->method."&extras=".$extras."&page=".$page."&per_page=".$resultsPerPage."&format=json";
              $response = $this->curlCall($url);
              break;

            case 'flickr.photos.getInfo':
              if ( is_array($this->option) ) {
                $photo_id = $this->option['photo_id'];
                $photo_secret = $option['photo_secret'];
                $url = $this->apiUrl."api_key=".$this->apiKey."&method=".$this->method."&photo_id=".$photo_id."&secret=".$photo_secret."&format=json";
                $response = $this->curlCall($url);
              }
              else $response = false;
              break;

          default:
            # code...
            break;
        }
        
        return array("flickrResponse"=>$response);
    }
    
    protected function getDetails($photo_data) {
        foreach ($photo_data as $key => $value) {
            echo "<span style='padding:1em;'>" . $key . "<span>";
            if(is_array($value)){
                foreach ($value as $item => $val) {
                    echo "<span style='padding:1em;'>" . $item . "<span>";
                    echo "<span style='padding:1em;'>" . $item . "<span>";
                }
            }
        }
    }
}
