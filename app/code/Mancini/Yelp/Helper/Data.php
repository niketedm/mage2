<?php

namespace Mancini\Yelp\Helper;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\HTTP\Client\Curl;

class Data extends AbstractHelper {

    protected $_scopeConfig;

    protected $_storeManager;

    protected $cache;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        CacheInterface $cache,
        Curl $curl
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->cache = $cache;
        $this->_curl = $curl;
    }

    public function getRatingImage($rate, $size) {
        $intRate = floor($rate);
        $size = strtolower($size);
        $fileName = $size . '_' . $intRate;
        if ($intRate != $rate) {
            $fileName .= '_half';
        }

        return 'Manini_Yelp::images/' . $size . '/' . $fileName . '.png';
    }

    public function getConfigValue($path) {
        $store = $this->_storeManager->getStore()->getId();
        return $this->_scopeConfig->getValue(
            $path, ScopeInterface::SCOPE_STORE, $store
        );
    }

    public function getYelpAPIKey() {
        return $this->getConfigValue('yelp/general/api_key');
    }

    public function getYelpBusinessReviews($identity, $storeName, $location) {
        try {
            $businessData = $this->getYelpBusinessDataByLocation($identity, $storeName, $location);
            if ($businessData == null) {
                return null;
            }

            $token = $this->getYelpAPIKey();
            if ($token == null) {
                return null;
            }

            $cacheId = 'yelp_business_reviews_' . $identity;
            if (false !== ($data = $this->cache->load($cacheId))) {
                return unserialize($data);
            }

            $url = 'https://api.yelp.com/v3/businesses/' . $businessData['id'] . '/reviews';

            $result = $this->getDataFromApi($url, $token);
            if (isset($result['reviews'])) {
                $this->cache->save(serialize($result['reviews']), $cacheId);
                return $result['reviews'];
            }
        } catch (NoSuchEntityException $e) {
            return null;
        }

        return null;
    }


    public function getYelpBusinessDataByLocation($identity, $storeName, $location) {
        try {
            $cacheId = 'yelp_business_' . $identity;
            if (false !== ($data = $this->cache->load($cacheId))) {
                return unserialize($data);
            }

            $token = $this->getYelpAPIKey();

            if ($token == null) {
                return null;
            }

            $url = 'https://api.yelp.com/v3/businesses/search';
            $params = array(
                'term' => $storeName,
                'location' => $location
            );
            $url .= '?' . http_build_query($params);

            $result = $this->getDataFromApi($url, $token);
            if (isset($result['businesses'][0])) {
                $this->cache->save(serialize($result['businesses'][0]), $cacheId);
                return $result['businesses'][0];
            }
        } catch (NoSuchEntityException $e) {
            return null;
        }

        return null;
    }

    protected function getDataFromApi($url, $token) {

      //$this->_curl->setOption(CURLOPT_SSL_VERIFYHOST,false);
      //$this->_curl->setOption(CURLOPT_SSL_VERIFYPEER,false);
      $this->_curl->addHeader("Content-Type", "application/json");
      $this->_curl->addHeader("Authorization", "Bearer ".$token);
      $this->_curl->get($url);

      $http_status = $this->_curl->getStatus();

      if ($http_status != 200) {
          return array();
      }
      $response = $this->_curl->getBody();
      return json_decode($response, true);
    }
}
