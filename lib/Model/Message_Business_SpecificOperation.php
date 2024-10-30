<?php
/**
 * Message_Business_SpecificOperation
 *
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

namespace Swagger\Client\Model;

use \ArrayAccess;

/**
 * Message_Business_SpecificOperation Class Doc Comment
 *
 * @category    Class */
/** 
 * @package     Swagger\Client
 * @author      http://github.com/swagger-api/swagger-codegen
 * @license     http://www.apache.org/licenses/LICENSE-2.0 Apache Licene v2
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class Message_Business_SpecificOperation implements ArrayAccess
{
    /**
      * The original name of the model.
      * @var string
      */
    protected static $swaggerModelName = 'Message_Business_SpecificOperation';

    /**
      * Array of property to type mappings. Used for (de)serialization
      * @var string[]
      */
    protected static $swaggerTypes = array(
        'operation_id' => 'int',
        'is_suspended' => 'bool',
        'name' => 'string',
        'file_name' => 'string',
        'separator' => 'string',
        'encoding' => 'string',
        'format' => 'string',
        'field_name' => 'map[string,string]',
        'options' => 'string[]',
        'filters' => 'string[]'
    );

    public static function swaggerTypes()
    {
        return self::$swaggerTypes;
    }

    /**
     * Array of attributes where the key is the local name, and the value is the original name
     * @var string[]
     */
    protected static $attributeMap = array(
        'operation_id' => 'operationId',
        'is_suspended' => 'isSuspended',
        'name' => 'name',
        'file_name' => 'fileName',
        'separator' => 'separator',
        'encoding' => 'encoding',
        'format' => 'format',
        'field_name' => 'fieldName',
        'options' => 'options',
        'filters' => 'filters'
    );

    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     * @var string[]
     */
    protected static $setters = array(
        'operation_id' => 'setOperationId',
        'is_suspended' => 'setIsSuspended',
        'name' => 'setName',
        'file_name' => 'setFileName',
        'separator' => 'setSeparator',
        'encoding' => 'setEncoding',
        'format' => 'setFormat',
        'field_name' => 'setFieldName',
        'options' => 'setOptions',
        'filters' => 'setFilters'
    );

    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     * @var string[]
     */
    protected static $getters = array(
        'operation_id' => 'getOperationId',
        'is_suspended' => 'getIsSuspended',
        'name' => 'getName',
        'file_name' => 'getFileName',
        'separator' => 'getSeparator',
        'encoding' => 'getEncoding',
        'format' => 'getFormat',
        'field_name' => 'getFieldName',
        'options' => 'getOptions',
        'filters' => 'getFilters'
    );

    public static function getters()
    {
        return self::$getters;
    }

    

    

    /**
     * Associative array for storing property values
     * @var mixed[]
     */
    protected $container = array();

    /**
     * Constructor
     * @param mixed[] $data Associated array of property value initalizing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['operation_id'] = isset($data['operation_id']) ? $data['operation_id'] : null;
        $this->container['is_suspended'] = isset($data['is_suspended']) ? $data['is_suspended'] : null;
        $this->container['name'] = isset($data['name']) ? $data['name'] : null;
        $this->container['file_name'] = isset($data['file_name']) ? $data['file_name'] : null;
        $this->container['separator'] = isset($data['separator']) ? $data['separator'] : null;
        $this->container['encoding'] = isset($data['encoding']) ? $data['encoding'] : null;
        $this->container['format'] = isset($data['format']) ? $data['format'] : null;
        $this->container['field_name'] = isset($data['field_name']) ? $data['field_name'] : null;
        $this->container['options'] = isset($data['options']) ? $data['options'] : null;
        $this->container['filters'] = isset($data['filters']) ? $data['filters'] : null;
    }

    /**
     * show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalid_properties = array();
        return $invalid_properties;
    }

    /**
     * validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properteis are valid
     */
    public function valid()
    {
        return true;
    }


    /**
     * Gets operation_id
     * @return int
     */
    public function getOperationId()
    {
        return $this->container['operation_id'];
    }

    /**
     * Sets operation_id
     * @param int $operation_id
     * @return $this
     */
    public function setOperationId($operation_id)
    {
        $this->container['operation_id'] = $operation_id;

        return $this;
    }

    /**
     * Gets is_suspended
     * @return bool
     */
    public function getIsSuspended()
    {
        return $this->container['is_suspended'];
    }

    /**
     * Sets is_suspended
     * @param bool $is_suspended
     * @return $this
     */
    public function setIsSuspended($is_suspended)
    {
        $this->container['is_suspended'] = $is_suspended;

        return $this;
    }

    /**
     * Gets name
     * @return string
     */
    public function getName()
    {
        return $this->container['name'];
    }

    /**
     * Sets name
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->container['name'] = $name;

        return $this;
    }

    /**
     * Gets file_name
     * @return string
     */
    public function getFileName()
    {
        return $this->container['file_name'];
    }

    /**
     * Sets file_name
     * @param string $file_name
     * @return $this
     */
    public function setFileName($file_name)
    {
        $this->container['file_name'] = $file_name;

        return $this;
    }

    /**
     * Gets separator
     * @return string
     */
    public function getSeparator()
    {
        return $this->container['separator'];
    }

    /**
     * Sets separator
     * @param string $separator
     * @return $this
     */
    public function setSeparator($separator)
    {
        $this->container['separator'] = $separator;

        return $this;
    }

    /**
     * Gets encoding
     * @return string
     */
    public function getEncoding()
    {
        return $this->container['encoding'];
    }

    /**
     * Sets encoding
     * @param string $encoding
     * @return $this
     */
    public function setEncoding($encoding)
    {
        $this->container['encoding'] = $encoding;

        return $this;
    }

    /**
     * Gets format
     * @return string
     */
    public function getFormat()
    {
        return $this->container['format'];
    }

    /**
     * Sets format
     * @param string $format
     * @return $this
     */
    public function setFormat($format)
    {
        $this->container['format'] = $format;

        return $this;
    }

    /**
     * Gets field_name
     * @return map[string,string]
     */
    public function getFieldName()
    {
        return $this->container['field_name'];
    }

    /**
     * Sets field_name
     * @param map[string,string] $field_name
     * @return $this
     */
    public function setFieldName($field_name)
    {
        $this->container['field_name'] = $field_name;

        return $this;
    }

    /**
     * Gets options
     * @return string[]
     */
    public function getOptions()
    {
        return $this->container['options'];
    }

    /**
     * Sets options
     * @param string[] $options
     * @return $this
     */
    public function setOptions($options)
    {
        $this->container['options'] = $options;

        return $this;
    }

    /**
     * Gets filters
     * @return string[]
     */
    public function getFilters()
    {
        return $this->container['filters'];
    }

    /**
     * Sets filters
     * @param string[] $filters
     * @return $this
     */
    public function setFilters($filters)
    {
        $this->container['filters'] = $filters;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     * @param  integer $offset Offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     * @param  integer $offset Offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * Sets value based on offset.
     * @param  integer $offset Offset
     * @param  mixed   $value  Value to be set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     * @param  integer $offset Offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Gets the string presentation of the object
     * @return string
     */
    public function __toString()
    {
        if (defined('JSON_PRETTY_PRINT')) { // use JSON pretty print
            return json_encode(\Swagger\Client\Message_Business_ObjectSerializer::sanitizeForSerialization($this), JSON_PRETTY_PRINT);
        }

        return json_encode(\Swagger\Client\Message_Business_ObjectSerializer::sanitizeForSerialization($this));
    }
}

