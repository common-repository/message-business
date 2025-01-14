<?php
/**
 * Message_Business_PartnerApi
 * PHP version 5
 *
 * @category Class
 * @package  Swagger\Client
 * @author   http://github.com/swagger-api/swagger-codegen
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache Licene v2
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * Message Business API
 *
 * REST API allowing you to interact with your message business account.
 *
 * OpenAPI spec version: v4
 * 
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace Swagger\Client\Api;

use \Swagger\Client\Message_Business_Configuration;
use \Swagger\Client\Message_Business_ApiClient;
use \Swagger\Client\Message_Business_ApiException;
use \Swagger\Client\Message_Business_ObjectSerializer;

/**
 * Message_Business_PartnerApi Class Doc Comment
 *
 * @category Class
 * @package  Swagger\Client
 * @author   http://github.com/swagger-api/swagger-codegen
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache Licene v2
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class Message_Business_PartnerApi
{

    /**
     * API Client
     *
     * @var \Swagger\Client\Message_Business_ApiClient instance of the Message_Business_ApiClient
     */
    protected $apiClient;

    /**
     * Constructor
     *
     * @param \Swagger\Client\Message_Business_ApiClient|null $apiClient The api client to use
     */
    public function __construct(\Swagger\Client\Message_Business_ApiClient $apiClient = null)
    {
        if ($apiClient == null) {
            $apiClient = new Message_Business_ApiClient();
            $apiClient->getConfig()->setHost('https://services.message-business.com/api/rest/v4');
        }

        $this->apiClient = $apiClient;
    }

    /**
     * Get API client
     *
     * @return \Swagger\Client\Message_Business_ApiClient get the API client
     */
    public function getApiClient()
    {
        return $this->apiClient;
    }

    /**
     * Set the API client
     *
     * @param \Swagger\Client\Message_Business_ApiClient $apiClient set the API client
     *
     * @return Message_Business_PartnerApi
     */
    public function setApiClient(\Swagger\Client\Message_Business_ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
        return $this;
    }

    /**
     * Operation partnerGetPartnerAccount
     *
     * Check if your partner account is still active
     *
     * @param string $mbcode The code given to you by message business (required)
     * @param int $account_id account Id (required)
     * @return string
     * @throws \Swagger\Client\Message_Business_ApiException on non-2xx response
     */
    public function partnerGetPartnerAccount($mbcode, $account_id)
    {
        list($response) = $this->partnerGetPartnerAccountWithHttpInfo($mbcode, $account_id);
        return $response;
    }

    /**
     * Operation partnerGetPartnerAccountWithHttpInfo
     *
     * Check if your partner account is still active
     *
     * @param string $mbcode The code given to you by message business (required)
     * @param int $account_id account Id (required)
     * @return Array of string, HTTP status code, HTTP response headers (array of strings)
     * @throws \Swagger\Client\Message_Business_ApiException on non-2xx response
     */
    public function partnerGetPartnerAccountWithHttpInfo($mbcode, $account_id)
    {
        // verify the required parameter 'mbcode' is set
        if ($mbcode === null) {
            throw new \InvalidArgumentException('Missing the required parameter $mbcode when calling partnerGetPartnerAccount');
        }
        // verify the required parameter 'account_id' is set
        if ($account_id === null) {
            throw new \InvalidArgumentException('Missing the required parameter $account_id when calling partnerGetPartnerAccount');
        }
        // parse inputs
        $resourcePath = "/Partner/{mbcode}/{accountId}";
        $httpBody = '';
        $queryParams = array();
        $headerParams = array();
        $formParams = array();
        $_header_accept = $this->apiClient->selectHeaderAccept(array('application/json', 'application/xml'));
        if (!is_null($_header_accept)) {
            $headerParams['Accept'] = $_header_accept;
        }
        $headerParams['Content-Type'] = $this->apiClient->selectHeaderContentType(array());

        // path params
        if ($mbcode !== null) {
            $resourcePath = str_replace(
                "{" . "mbcode" . "}",
                $this->apiClient->getSerializer()->toPathValue($mbcode),
                $resourcePath
            );
        }
        // path params
        if ($account_id !== null) {
            $resourcePath = str_replace(
                "{" . "accountId" . "}",
                $this->apiClient->getSerializer()->toPathValue($account_id),
                $resourcePath
            );
        }
        // default format to json
        $resourcePath = str_replace("{format}", "json", $resourcePath);

        
        // for model (json/xml)
        if (isset($_tempBody)) {
            $httpBody = $_tempBody; // $_tempBody is the method argument, if present
        } elseif (count($formParams) > 0) {
            $httpBody = $formParams; // for HTTP post (form)
        }
        // make the API Call
        try {
            list($response, $statusCode, $httpHeader) = $this->apiClient->callApi(
                $resourcePath,
                'GET',
                $queryParams,
                $httpBody,
                $headerParams,
                'string',
                '/Partner/{mbcode}/{accountId}'
            );

            return array($this->apiClient->getSerializer()->deserialize($response, 'string', $httpHeader), $statusCode, $httpHeader);
        } catch (Message_Business_ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), 'string', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }

    /**
     * Operation partnerPostPartnerAccount
     *
     * Create a new Message Business account
     *
     * @param \Swagger\Client\Model\Message_Business_PartnerSignUp $partner_signup  (required)
     * @return \Swagger\Client\Model\Message_Business_PartnerInformation
     * @throws \Swagger\Client\Message_Business_ApiException on non-2xx response
     */
    public function partnerPostPartnerAccount($partner_signup)
    {
        list($response) = $this->partnerPostPartnerAccountWithHttpInfo($partner_signup);
        return $response;
    }

    /**
     * Operation partnerPostPartnerAccountWithHttpInfo
     *
     * Create a new Message Business account
     *
     * @param \Swagger\Client\Model\Message_Business_PartnerSignUp $partner_signup  (required)
     * @return Array of \Swagger\Client\Model\Message_Business_PartnerInformation, HTTP status code, HTTP response headers (array of strings)
     * @throws \Swagger\Client\Message_Business_ApiException on non-2xx response
     */
    public function partnerPostPartnerAccountWithHttpInfo($partner_signup)
    {
        // verify the required parameter 'partner_signup' is set
        if ($partner_signup === null) {
            throw new \InvalidArgumentException('Missing the required parameter $partner_signup when calling partnerPostPartnerAccount');
        }
        // parse inputs
        $resourcePath = "/Partner";
        $httpBody = '';
        $queryParams = array();
        $headerParams = array();
        $formParams = array();
        $_header_accept = $this->apiClient->selectHeaderAccept(array('application/json', 'application/xml'));
        if (!is_null($_header_accept)) {
            $headerParams['Accept'] = $_header_accept;
        }
        $headerParams['Content-Type'] = $this->apiClient->selectHeaderContentType(array('application/json','text/json','application/xml','text/xml','application/x-www-form-urlencoded'));

        // default format to json
        $resourcePath = str_replace("{format}", "json", $resourcePath);

        // body params
        $_tempBody = null;
        if (isset($partner_signup)) {
            $_tempBody = $partner_signup;
        }

        // for model (json/xml)
        if (isset($_tempBody)) {
            $httpBody = $_tempBody; // $_tempBody is the method argument, if present
        } elseif (count($formParams) > 0) {
            $httpBody = $formParams; // for HTTP post (form)
        }
        // make the API Call
        try {
            list($response, $statusCode, $httpHeader) = $this->apiClient->callApi(
                $resourcePath,
                'POST',
                $queryParams,
                $httpBody,
                $headerParams,
                '\Swagger\Client\Model\Message_Business_PartnerInformation',
                '/Partner'
            );

            return array($this->apiClient->getSerializer()->deserialize($response, '\Swagger\Client\Model\Message_Business_PartnerInformation', $httpHeader), $statusCode, $httpHeader);
        } catch (Message_Business_ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\Message_Business_PartnerInformation', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }

}
