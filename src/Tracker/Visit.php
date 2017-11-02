<?php
namespace Module\Visitor\Tracker;

use Yii;

class Visit
{
	private $mobileDetect = null;
	private $crawlerDetect = null;
	private $request;


	private $deviceType = null;

	private $isMobile = null;

	private $isTablet = null;

	private $isCrawler = null;

    public function __construct()
    {
    	$this->request = Yii::$app->get('request');


    	$this->mobileDetect = Yii::$container->get('device.detect');
    	$this->crawlerDetect = Yii::$container->get('crawler.detect');
    }

	public function getIp()
	{
		return $this->request->getUserIP();
	}

	public function getUserAgent()
	{
		return $this->request->getUserAgent();
	}

	public function getReferer()
	{
		return $this->request->getReferrer();
	}

	public function getRefererHost()
	{
		return !is_null($this->request->getReferrer()) ? parse_url($this->request->getReferrer(), PHP_URL_HOST) : null;
	}

	public function isProxy()
	{
		$proxy_headers = [
	        'HTTP_VIA',
	        'HTTP_X_FORWARDED_FOR',
	        'HTTP_FORWARDED_FOR',
	        'HTTP_X_FORWARDED',
	        'HTTP_FORWARDED',
	        'HTTP_CLIENT_IP',
	        'HTTP_FORWARDED_FOR_IP',
	        'VIA',
	        'X_FORWARDED_FOR',
	        'FORWARDED_FOR',
	        'X_FORWARDED',
	        'FORWARDED',
	        'CLIENT_IP',
	        'FORWARDED_FOR_IP',
	        'HTTP_PROXY_CONNECTION'
	    ];

	    foreach($proxy_headers as $val){
	        if (isset($_SERVER[$val]))
	        	return true;
	    }

	    return false;
	}

	public function getDeviceType()
	{
		if ($this->deviceType === null) {
			if ($this->isMobile()) {
				$this->deviceType = 'mobile';
			} elseif ($this->isTablet()) {
				$this->deviceType = 'tablet';
			} else {
				$this->deviceType = 'desktop';
			}
		}

		return $this->deviceType;
	}

	public function isMobile()
	{
		if ($this->isMobile === null) {
			$this->isMobile = $this->mobileDetect->isMobile();
		}

		return $this->isMobile;
	}

	public function isTablet()
	{
		if ($this->isTablet === null) {
			$this->isTablet = $this->mobileDetect->isTablet();
		}

		return $this->isTablet;
	}

	public function isCrawler()
	{
		if ($this->isCrawler === null) {
			$this->isCrawler = $this->crawlerDetect->isCrawler();
		}

		return $this->isCrawler;
	}

	public function getRefererType()
	{
		$se = new SERefererDetect;

		if ($this->getReferer() === '') {
			return 'bookmark';
		} elseif ($this->getRefererHost() === $_SERVER['HTTP_HOST']) {
			return 'internal';
		} elseif ($se->fromSE($this->getReferer())) {
			return 'se';
		} elseif (filter_var($this->getReferer(), FILTER_VALIDATE_URL)) {
			return 'links';
		} else {
			return 'other';
		}
	}

    /**
     * Returns the user's preferred language from the browser
     *
     * @return string|null the preferred language from the browser or NULL
     */
    public function getBrowserLanguage()
    {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']);
        }

        return null;
    }
}