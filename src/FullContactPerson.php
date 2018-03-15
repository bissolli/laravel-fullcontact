<?php

namespace Bissolli\FullContact;

/**
 * This class handles everything related to the Person lookup API.
 *
 * @package  Services\FullContact
 */
class FullContactPerson extends FullContact
{
    /**
     * Supported lookup methods
     * @var $_supportedMethods
     */
    protected $_supportedMethods = array('email', 'phone', 'twitter', 'facebookUsername', 'stats');
    protected $_resourceUri = '/person.json';

    public function lookupByEmail($search)
    {
        $this->_execute(array('email' => $search, 'method' => 'email'));

        return $this->response_obj;
    }

    public function lookupByEmailMD5($search)
    {
        $this->_execute(array('emailMD5' => $search, 'method' => 'email'));

        return $this->response_obj;
    }

    public function lookupByPhone($search, $country = 'US')
    {
        $this->_execute(array('phone' => $search, 'countryCode' => $country, 'method' => 'phone'));

        return $this->response_obj;
    }

    public function lookupByTwitter($search)
    {
        $this->_execute(array('twitter' => $search, 'method' => 'twitter'));

        return $this->response_obj;
    }

    public function lookupByFacebook($search)
    {
        $this->_execute(array('facebookUsername' => $search, 'method' => 'facebookUsername'));

        return $this->response_obj;
    }

    public function accountStats($search = NULL)
    {
        $this->_execute(array('period' => $search, 'method' => 'stats'), '/stats.json');
        return $this->response_obj;
    }
}
