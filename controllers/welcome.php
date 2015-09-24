<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function __construct()
	{
		session_start();
		parent::__construct();
		// To use site_url and redirect on this controller.
        $this->load->helper('url');
	}
	public function index()
	{
		//include_once 'libraries/google-api-php-client/autoload.php';
		include_once APPPATH . "libraries/google-api-php-client-master/src/Google/autoload.php";
		include_once APPPATH . "libraries/google-api-php-client-master/src/Google/Client.php";
		include_once APPPATH . "libraries/google-api-php-client-master/src/Google/Service/Oauth2.php";

		// Store values in variables from project created in Google Developer Console
		$client_id = 'YOUR_CLIENT_ID';
		$client_secret = 'YOUR_CLIENT_SECRET';
		$redirect_uri = 'http://localhost/glogin/';
		$simple_api_key = 'API KEY';

		// Create Client Request to access Google API
		$client = new Google_Client();
		$client->setApplicationName("PHP Google OAuth Login Example");
		$client->setClientId($client_id);
		$client->setClientSecret($client_secret);
		$client->setRedirectUri($redirect_uri);
		$client->setDeveloperKey($simple_api_key);
		$client->addScope("https://www.googleapis.com/auth/userinfo.email");

		// Send Client Request
		$objOAuthService = new Google_Service_Oauth2($client);

		// Add Access Token to Session
		if (isset($_GET['code'])) 
		{
			$client->authenticate($_GET['code']);
			$_SESSION['access_token'] = $client->getAccessToken();
			header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
		}

		// Set Access Token to make Request
		if (isset($_SESSION['access_token']) && $_SESSION['access_token']) 
		{
			$client->setAccessToken($_SESSION['access_token']);
		}

		// Get User Data from Google and store them in $data
		if ($client->getAccessToken()) 
		{
			$userData = $objOAuthService->userinfo->get();
			$data['userData'] = $userData;
			$_SESSION['access_token'] = $client->getAccessToken();
		} 
		else 
		{
			$authUrl = $client->createAuthUrl();
			$data['authUrl'] = $authUrl;
		}
		// Load view and send values stored in $data
		$this->load->view('welcome_message', $data);
	}
	
	public function logout() 
	{
		unset($_SESSION['access_token']);
		redirect(base_url());
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */