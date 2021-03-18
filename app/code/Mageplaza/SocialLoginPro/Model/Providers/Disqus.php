<?php
/*!
* HybridAuth
* http://hybridauth.sourceforge.net | https://github.com/hybridauth/hybridauth
*  (c) 2009-2014 HybridAuth authors | hybridauth.sourceforge.net/licenses.html
*/

namespace Mageplaza\SocialLoginPro\Model\Providers;

/**
 * Class Disqus
 * @package Mageplaza\SocialLoginPro\Model\Providers
 */
class Disqus extends \Hybrid_Provider_Model_OAuth2
{
    // default permissions
    // (read,email) => public info and email
    public $scope = "read,email";

    /**
     * IDp wrappers initializer
     * @throws \Exception
     */
    function initialize()
    {
        parent::initialize();

        // Provider api end-points
        $this->api->api_base_url = "https://disqus.com/api/3.0/";
        $this->api->authorize_url = "https://disqus.com/api/oauth/2.0/authorize";
        $this->api->token_url = "https://disqus.com/api/oauth/2.0/access_token/";
    }

    /**
     * load the user profile from the IDp api client
     * @return \Hybrid_User_Profile
     * @throws \Exception
     */
    function getUserProfile()
    {
        $data = $this->api->get("users/details", array('api_key' => $this->api->client_id, 'api_secret' => $this->api->client_secret));

        if (!isset($data->code)) {
            throw new \Exception("User profile request failed! {$this->providerId} returned an invalid response.", 6);
        } else {
            if ($data->code != 0) {
                throw new \Exception("User profile request failed! {$this->providerId} returned error code" . $data->code . ".", 6);
            }
        }

        $this->user->profile->identifier = @ $data->response->id;
        $this->user->profile->displayName = @ $data->response->name;
        $this->user->profile->description = @ $data->response->bio;
        $this->user->profile->photoURL = @ $data->response->avatar->permalink;
        $this->user->profile->profileURL = @ $data->response->profileUrl;
        $this->user->profile->email = @ $data->response->email;
        $this->user->profile->region = @ $data->response->location;
        $this->user->profile->description = @ $data->response->about;

        if (!$this->user->profile->displayName) {
            $this->user->profile->displayName = @ $data->response->username;
        }

        return $this->user->profile;
    }
}
