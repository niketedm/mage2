<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mancini\PowerReviews\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    const PR_API_URL = "http://readservices-b2c.powerreviews.com";
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var Curl
     */
    protected $_curl;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Registry $registry,
        \PowerReviews\ReviewDisplay\Helper\Data $powerReviewsHelper,
        \Magento\Framework\HTTP\Client\Curl $curl
    ) {
        parent::__construct($context);
        $this->_registry    =   $registry;
        $this->_powerReviewsHelper = $powerReviewsHelper;
        $this->_curl = $curl;
    }

    /**
     * Function to get the  current product
     */    
    public function getCurrentProduct()
    {        
        return $this->_registry->registry('current_product');
    }
    /**
    * Function to retrieve the product rating from Power Reviews
    * @return string
    */
    public function getProductReviewSnippet($pageId) {

      $average_rating = 0;

      $url = self::PR_API_URL."/m/".$this->_powerReviewsHelper->getMerchantId()."/l/".$this->_powerReviewsHelper->getLocale()."/product/".$pageId."/snippet/?apikey=".$this->_powerReviewsHelper->getApiKey();
      $this->_curl->addHeader("Content-Type", "application/json");

      $this->_curl->get($url);

      $http_status = $this->_curl->getStatus();

      if ($http_status != 200) {
          return array();
      }

      $response = $this->_curl->getBody();

      $response = json_decode($response, true);
      if (empty($response)) {
        return null;
      }
      else {
        $results = $response['results'];
        foreach($results as $result) {
          $average_rating = $result['rollup']['average_rating'];
        }
        return $average_rating;
      }

    }

    /**
     * Function to get the current product
     */
    public function getCurrentSKU(){
        $product = $this->getCurrentProduct();

        $productCode = $product->getSku();

        return $productCode;
    }
    
    /**
     * Get the reviews of product using the read API of Power review, call the number oif reviews as per the pagination param
     */
    public function getProductReviews($pageFrom = 0, $prdSku = '')
    {
        
        //$productCode =  'Config';
        if($prdSku != ''){
            $productCode = $prdSku;
        } else {
            $productCode = $this->getCurrentSKU();
        }

        //$productCode = str_replace(" ","",$productCode);

        $url = self::PR_API_URL."/m/".$this->_powerReviewsHelper->getMerchantId()."/l/".$this->_powerReviewsHelper->getLocale(). "/product/" . $productCode . "/reviews/?apikey=" . $this->_powerReviewsHelper->getApiKey() . "&paging.size=5&paging.from=" . $pageFrom . "&sort=Newest&image_only=false";
        $this->_curl->addHeader("Content-Type", "application/json");
        $this->_curl->get($url);

        $http_status = $this->_curl->getStatus(); 
       
        if ($http_status != 200) {
            return array();
        }
        $response = $this->_curl->getBody();

        $average = $this->getProductReviewSnippet($productCode);
        $response = json_decode($response, true);
        $response['summary'] = $average;

        return $response;
       
    }

    /**
     * From the result get the media for the product to be displayed alomng with summary
     */
    public function getMediaForProduct($allReviews)
    {
        $allMedia = [];
        if(isset($allReviews['results'])):
            foreach ($allReviews['results'][0]['reviews'] as $review):
                foreach ($review['media'] as $media):
                // print_r($media);
                    if ($media['type'] == "image") 
                    {
                        array_push($allMedia, array(
                            'type'=>$media['type'],
                            'uri'=>$media['uri']
                            ));
                    } else if ($media['type'] == "video") {
                        array_push($allMedia, array(
                            'type'=>$media['type'], 
                            'uri'=>$media['uri'])
                        );
                    }
                endforeach;
            endforeach;
        endif;
        return $allMedia;
    }

    public function getProductReviewMedia()
    {
       // https://readservices-b2c.powerreviews.com/m/1335309536/l/en_US/media?apikey=89a825fe-c519-41a7-a785-701173f9f299
       // or get media from the existing reviews fetched
    }

    /**
     * Method for adding helpful and unhelpful votes to each review
     * @param ugcId - Id of the product
     * @param voteType - helpful, unhelpful
     */
    public function voteUGC($ugcId , $voteType)
    {
        $url = "https://writeservices.powerreviews.com/voteugc";
        $body = array (
            "ugc_id" => $ugcId,
            "createdDate"=> time(),
            "vote_type" => $voteType,
            "merchant_id" => $this->_powerReviewsHelper->getMerchantId(),
            "cookie" => $ugcId
        );
        $data = json_encode($body);

        // //set curl options
        // $this->_curl->setOption(CURLOPT_HEADER, 0);
        // $this->_curl->setOption(CURLOPT_TIMEOUT, 60);
        // $this->_curl->setOption(CURLOPT_RETURNTRANSFER, true);
        // $this->_curl->addHeader("Content-Type", "application/json");
        // $this->_curl->post($url, $data);

        // //read response
        // $response = $this->_curl->getBody();

        // return $response;
        $ch = curl_init();
 
        curl_setopt($ch, CURLOPT_URL,$url);
        //curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
      
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
 
        // receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        $result = curl_exec($ch);
       
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);
        if ( $http_status != 200)
        {  
 
            return false;
        }
        # Print response.
        return $result;
    }
   
    /**
     * Get the tome difference i.e. days, months, years for the review post date
     */
    public function getTimeDiff($reviewTime)
    {
        $time = time();
     //   echo new \DateTime(date('Y-m-d h:i:s', floor(($reviewTime / 1000)));
        $reviewTime = (int)substr((string)$reviewTime, 0 ,-3);
       
        $time = new \DateTime(date('Y-m-d H:i:s', time()));
        $timenow = new \DateTime(date('Y-m-d H:i:s', $reviewTime));

        $interval = $timenow->diff($time);

        $min = $interval->format('%i');
        $sec = $interval->format('%s');
        $hour = $interval->format('%h');
        $mon = $interval->format('%m');
        $day = $interval->format('%d');
        $year = $interval->format('%y');
        if ($interval->format('%i%h%d%m%y') == "00000")
        {
            //echo $interval->format('%i%h%d%m%y')."<br>";
            if ($sec > 1)
            {
                return $sec . " seconds ago";
            }
            else
            {
                return $sec . "second ago";
            }
        }
        else if ($interval->format('%h%d%m%y') == "0000")
        {
            if ($min > 1)
            {
                return $min . " minutes ago";
            }
            else
            {
                return $min . " minute ago";
            }
        }
        else if ($interval->format('%d%m%y') == "000")
        {
            if ($hour > 1)
            {
                return $hour . " hours ago";
            }
            else
            {
                return $hour . " hour ago";
            }
        }
        else if ($interval->format('%m%y') == "00")
        {
            if ($day > 1)
            {
                return $day . " days ago";
            }
            else
            {
                return $day . " day ago";
            }
        }
        else if ($interval->format('%y') == "0")
        {
            if ($mon > 1)
            {
                return $mon . " months ago";
            }
            else
            {
                return $mon . " month ago";
            }
        }
        else
        {
            if ($year > 1)
            {
                return $year . " years ago";
            }
            else
            {
                return $year . " year ago";
            }
        }
        //exit;

    }
}
