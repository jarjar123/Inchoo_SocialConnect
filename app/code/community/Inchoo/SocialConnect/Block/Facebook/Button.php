<?php
/**
* Inchoo
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@magentocommerce.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Please do not edit or add to this file if you wish to upgrade
* Magento or this extension to newer versions in the future.
** Inchoo *give their best to conform to
* "non-obtrusive, best Magento practices" style of coding.
* However,* Inchoo *guarantee functional accuracy of
* specific extension behavior. Additionally we take no responsibility
* for any possible issue(s) resulting from extension usage.
* We reserve the full right not to provide any kind of support for our free extensions.
* Thank you for your understanding.
*
* @category Inchoo
* @package SocialConnect
* @author Marko Martinović <marko.martinovic@inchoo.net>
* @copyright Copyright (c) Inchoo (http://inchoo.net/)
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
*/

class Inchoo_SocialConnect_Block_Facebook_Button extends Mage_Core_Block_Template
{
    protected $client = null;
    protected $userInfo = null;
    protected $redirectUri = null;

    protected function _construct() {
        parent::_construct();

        $model = Mage::getSingleton('inchoo_socialconnect/facebook_client');

        if(!($this->client = $model->getClient()))
                return;

        $this->userInfo = Mage::registry('inchoo_socialconnect_userinfo');

        $state = Mage::helper('core/url')->getCurrentUrl();

        /* Mage::getSingleton('customer/session')->getBeforeAuthUrl(true)
         * returns value only on first call, we have multiple buttons so must 
         * use registry
         */
        if(($referer = Mage::registry('inchoo_social_connect_before_auth'))) {
            $state = $referer;
        } else if(($referer = Mage::getSingleton('customer/session')->getBeforeAuthUrl(true))) {
            Mage::register('inchoo_social_connect_before_auth', $referer);
            $state = $referer;            
        }
        
        // CSRF protection + redirect uri
        Mage::getSingleton('core/session')->setFacebookCsrf($csrf = md5(uniqid(rand(), TRUE)));

        $this->client->setState(serialize(array($csrf, $state)));

        $this->setTemplate('inchoo/socialconnect/facebook/button.phtml');
    }

    protected function _getButtonUrl()
    {
        if(empty($this->userInfo)) {
            return $this->client->createAuthUrl();
        } else {
            return $this->getUrl('socialconnect/facebook/disconnect');
        }
    }

    protected function _getButtonText()
    {
        if(empty($this->userInfo)) {
            return $this->__('Connect');
        } else {
            return $this->__('Disconnect');
        }
    }

}
