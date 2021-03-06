<?php

namespace ReallySimpleHttpRequests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Webmozart\Assert\Assert;

class Request implements RequestInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $body;

    /**
     * @var array
     */
    private $headers;

    public function __construct($url, $method, $body = null, $headers = [])
    {
        $this->client = new Client();
        $this->setUrl($url);
        $this->setMethod($method);
        $this->setBody($body);

        Assert::isArray($headers);
        foreach ($headers as $key => $value) {
            $this->addHeader($key, $value);
        }
    }

    /**
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function send()
    {
        $reply = $this->client->request(
            $this->method,
            $this->url,
            [
                'body' => $this->body,
                'headers' => $this->headers
            ]
        );

        $response = new Response(
            $reply->getBody()->getContents(),
            $reply->getStatusCode(),
            $reply->getHeaders()
        );

        return $response;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        Assert::string($url);
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        Assert::string($method);
        $method = strtolower($method);
        Assert::oneOf($method, ['get', 'post', 'put', 'patch', 'delete']);
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        Assert::nullOrString($body);
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function addHeader($key, $value)
    {
        Assert::string($key);
        Assert::string($value);
        $this->headers[$key] = $value;
    }

    /**
     * @param $key
     */
    public function removeHeader($key)
    {
        Assert::string($key);
        Assert::keyExists($this->headers, $key);
        unset($this->headers[$key]);
    }

    /**
     * @return array
     */
    public function getAllHeaders()
    {
        return $this->headers;
    }

    public function getHeader($key)
    {
        Assert::string($key);
        Assert::keyExists($this->headers, $key);
        return $this->headers[$key];
    }
}
