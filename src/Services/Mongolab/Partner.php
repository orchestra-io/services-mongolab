<?php
/**
 * Orchestra.io interacts with many services. This is the MongoLab
 * library to interact with the MongoLab Partner Management APIs
 *
 * @copyright     Copyright 2011 — Orchestra Platform Ltd. <info@orchestra.io>
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */
namespace orchestra\services\mongolab;

/**
 * This requires http://pecl.php.net/pecl_http
 *
 * @link http://pecl.php.net/pecl_http
 * @link http://php.net/http
 */
use \HttpRequest;

/**
 * Interact with the MongoLab API to
 * create new users, create new databases, and delete MongoDB instances.
 *
 * This package uses the PECL http extension and connects
 * to the mongohq.com API. To pass your authentication information
 * simply instantiate the object with 2 parameters. The username
 * then the password:
 *
 * <code>
 *  $obj = new orchestra\services\mongolab\Parnter('user', 'pass');
 *  $obj->getAll();
 * </code>
 *
 */
class Partner
{
    /**
     * The MongoLab API endpoint. This endpoint gets
     * built/constructed in the constructor where the api $version
     * and the $accountName are inserted into the endpoint.
     *
     * @var string $endpoint The API endpoint.
     */
    protected $endpoint = 'https://mongolab.com/api/%d';

    /**
     * A formatted string containing the authentication
     * information to authenticate to mongohq's API.
     *
     * @var string $auth The authentication string.
     */
    protected $auth;

    /**
     * Your account name. In the MongoLab documentation, the
     * account name by default is acme. This has to be replaced
     * by your company name at object instantiation time.
     *
     * @var string $accountName Your MongoLab account name.
     */
    protected $accountName = 'acme';

    /**
     * The API version to invoke. This is going to be merged
     * with the $this->endpoint using sprintf.
     *
     * @var string $version The version of the API to invoke for a certain call.
     */
    protected $version = 1;

    /**
     * The constructor
     *
     * This is the constructor of the object that accepets the
     * authentication information.
     *
     * @param string $accountName  The account name to use for the requests.
     */
    public function __construct($accountName)
    {
        $this->accountName = $accountName;

        $this->endpoint = sprintf(
            $this->endpoint,
            $this->version
        );
    }

    /**
     * Authenticate the user.
     *
     * This method can be used for both configuring the authentication
     * information for a user or a partner (whcih are technically the same thing).
     *
     * Using this method, a mongodb provider will be able to automate the
     * creation of databases for its users.
     *
     * @param string $user  The username to login with.
     * @param string $pass  The password to login with.
     */
    public function setAuth($user, $pass)
    {
        $this->auth = sprintf("%s:%s", $user, $pass);
    }

    /**
     * Get all partners for the account.
     *
     * This method is used to retrieve all the accounts associated
     * to the master account of the authenticated account.
     *
     * @param  double $method  The HTTP method to invoke. Default GET
     * @return object A json-decoded object of the instances.
     */
    public function getAll($method = HTTP_METH_GET)
    {
        $url = sprintf('/partners/%s/accounts', $this->accountName);
        return $this->send($url, $method);
    }

    /**
     * Get a single partner for the account.
     *
     * This method is used to retrieve a single partner
     * associated with the authenticated account.
     *
     * @param string $name  The name of the partner to retrieve.
     * @param  double $method  The HTTP method to invoke. Default GET
     *
     * @return object A json-decoded object of the instance.
     */
    public function get($name, $method = HTTP_METH_GET)
    {
        $url = sprintf('/partners/%s/accounts/%s', $this->accountName, $name);
        $info = $this->send($url, $method);

        $url = sprintf('/partners/%s/accounts/%s/databases', $this->accountName, $name);
        $databases = $this->send($url, $method);

        $info->databases = null;
        if (!empty($databases)) {
            $info->databases = $databases;
        }

        return $info;

    }

    /**
     * Add an instance to your account.
     *
     * This is used to add new instances to your account. By default
     * we select the "free" instance which is the "free" instance.
     *
     * @param  string $plan    The plan of the instance to add to your account.
     * @param  double $method  The HTTP method to invoke. Default POST
     *
     * @return object A json-decoded object of the instances.
     */
    public function create($data = array(), $method = HTTP_METH_POST)
    {
        // As per the MongoLab documentation, new partner accounts have to be prefixed
        // by the account name. We identify if the account name and _ are present and
        // if they aren't we prepend them to the data.
        if (isset($data['name']) && !stristr($data['name'], $this->accountName . '_')) {
            $data['name'] = $this->accountName . '_' . $data['name'];
        }

        $url = sprintf('/partners/%s/accounts', $this->accountName);
        return $this->send($url, $method, $data);
    }

    /**
     * Add an instance to your account.
     *
     * This is used to add new instances to your account. By default
     * we select the "free" instance which is the "free" instance.
     *
     * @param  double $method  The HTTP method to invoke. Default POST
     *
     * @return object A json-decoded object of the instances.
     */
    public function addDatabase(
        $partnerAccountName, $data = array(), $method = HTTP_METH_POST)
    {
        $url = sprintf('/partners/%s/accounts/%s/databases',
            $this->accountName,
            $partnerAccountName
        );

        return $this->send($url, $method, $data);
    }

    /**
     * Update an instance with information.
     *
     * This method is used to update an instance with
     * the specified parameters.
     *
     * @throws \BadMethodCallException
     *
     * @param  string $id      The identifier for the instance to update.
     * @param  array  $data    A data container containing the field to update
     *                         on the authenticated instance.
     * @param  double $method  The HTTP method to invoke. Default PUT
     *
     * @return object A json-decoded object of the instances.
     */
    public function update($id, array $data = array(), $method = HTTP_METH_PUT)
    {
        throw new \BadMethodCallException('This method isn\'t implemented yet.');
    }

    /**
     * Delete a partner.
     *
     * This method is used to delete a certain partner
     * using its unique name.
     *
     * @param  string $name    The partner identifier to delete.
     * @param  double $method  The HTTP method to invoke. Default DELETE
     * @return object A json-decoded object of the instances.
     */
    public function delete($name, $method = HTTP_METH_DELETE)
    {
        $url = sprintf('/partners/%s/accounts/%s', $this->accountName, $name);
        return $this->send($url, $method);
    }

    /**
     * Delete a partner database.
     *
     * This method is used to delete a certain partner database
     * using its unique name.
     *
     * @param  string $name    The partner identifier that owns the
     *                         database to delete.
     * @param  string $dbName  The name of the database to delete.
     * @param  double $method  The HTTP method to invoke. Default DELETE
     * @return object A json-decoded object of the instances.
     */
    public function deleteDatabase($name, $dbName, $method = HTTP_METH_DELETE)
    {
        $url = sprintf(
            '/partners/%s/accounts/%s/databases/%s',
            $this->accountName, $name, $dbName
        );

        return $this->send($url, $method);
    }

    /**
     * Send the request to the webservice.
     *
     * This method is used internally to make the requests
     * to the webservice.
     *
     * @throws \RuntimeException
     *
     * @param  string $url    The URL to request.
     * @param  string $method The method of the HTTP request.
     * @param  array  $data   The data to pass to the webservice.
     * @return object json-decoded object
     */
    protected function send($url, $method, array $data = array())
    {
        $http = new HttpRequest($this->endpoint . $url, $method);

        $http->setOptions(array(
            'httpauth'     => $this->auth,
            'httpauthtype' => HTTP_AUTH_BASIC
        ));

        $http->setHeaders(array(
            'Content-Type' => 'application/json'
        ));

        if ($method == HTTP_METH_POST && !empty($data)) {
            $http->setBody(json_encode($data));
        }

        $http->send();

        if ($http->getResponseCode() == 200) {
            return json_decode($http->getResponseBody());
        }

        throw new \RuntimeException(
            sprintf(
                "The request failed with HTTP response message (%s) and code %s",
                (string)$http->getResponseBody(), (string)$http->getResponseCode()
            )
        );
    }
}
