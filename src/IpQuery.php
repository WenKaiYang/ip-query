<?php

namespace WenKaiYang;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use WenKaiYang\Exceptions\HttpException;
use WenKaiYang\Exceptions\InvalidArgumentException;

class IpQuery
{

    protected string $key;

    protected array $guzzleOptions = [];

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function getHttpClient(): Client
    {
        return new Client($this->guzzleOptions);
    }

    public function setGuzzleOptions(array $options): void
    {
        $this->guzzleOptions = $options;
    }

    /**
     * @param string $ip
     * @return array
     * @throws HttpException
     * @throws InvalidArgumentException
     */
    public function getCity(string $ip): array
    {
        if (!\filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new InvalidArgumentException('Invalid ip value(ip4/ip6): ' . $ip);
        }

        try {
            $response = $this->getHttpClient()
                ->get('https://apis.juhe.cn/ip/ipNewV3', [
                    'query' => ['ip' => $ip, 'key' => $this->key,]
                ])
                ->getBody()
                ->getContents();

            return \json_decode($response, true);
        } catch (GuzzleException $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }
}