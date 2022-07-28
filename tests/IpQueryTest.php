<?php

declare(strict_types=1);
/**
 * This file is part of WenKaiYang/ip-query.
 *
 * @link     https://github.com/WenKaiYang
 * @document https://github.com/WenKaiYang/ip-query/wiki
 * @contact  https://github.com/WenKaiYang/ip-query
 * @license  https://github.com/WenKaiYang/ip-query/issues
 */
namespace WenKaiYang;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Mockery\Matcher\AnyArgs;
use PHPUnit\Framework\TestCase;
use WenKaiYang\Exceptions\HttpException;
use WenKaiYang\Exceptions\InvalidArgumentException;
use WenKaiYang\IpQuery;

/**
 * @internal
 * @coversNothing
 */
class IpQueryTest extends TestCase
{
    // 检查 $format 参数
    public function testGetCityWithInvalidIp()
    {
        $iqc = new IpQuery('mock-key');

        // 断言会抛出此异常类
        $this->expectException(InvalidArgumentException::class);

        // 断言异常消息为 'Invalid response ip: ip4'
        $this->expectExceptionMessage('Invalid ip value(IPV4): ip4');

        // 因为支持的格式为 xml/json，所以传入 array 会抛出异常
        $iqc->getCity('ip4');

        // 如果没有抛出异常，就会运行到这行，标记当前测试没成功
        $this->fail('Failed to assert getWeather throw exception with invalid argument.');
    }

    // 检查 http client 请求超时异常
    public function testGetCityWithGuzzleRuntimeException()
    {
        $client = \Mockery::mock(Client::class);
        $client->allows()
            ->get(new AnyArgs()) // 由于上面的用例已经验证过参数传递，所以这里就不关心参数了。
            ->andThrow(new \Exception('request timeout')); // 当调用 get 方法时会抛出异常。

        $iqc = \Mockery::mock(IpQuery::class, ['mock-key'])->makePartial();
        $iqc->allows()->getHttpClient()->andReturn($client);

        // 接着需要断言调用时会产生异常。
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('request timeout');

        $iqc->getCity('127.0.0.1');
    }

    // 检查 http client 请求城市
    public function testGetCity()
    {
        // 创建模拟接口响应值。
        $response = new Response(200, [], '{"resultcode": 200,"result":{}}');
        // 创建模拟 http client。
        $client = \Mockery::mock(Client::class);
        // 指定将会产生的行为（在后续的测试中将会按下面的参数来调用）。
        $client->allows()->get('https://apis.juhe.cn/ip/ipNewV3', [
            'query' => [
                'key' => 'mock-key',
                'ip' => '127.0.0.1',
            ],
        ])->andReturn($response);
        // 将 `getHttpClient` 方法替换为上面创建的 http client 为返回值的模拟方法。
        $iqc = \Mockery::mock(IpQuery::class, ['mock-key'])->makePartial();
        // $client 为上面创建的模拟实例。
        $iqc->allows()->getHttpClient()->andReturn($client);
        // 然后调用 `getCity` 方法，并断言返回值为模拟的返回值。
        $this->assertSame([], $iqc->getCity('127.0.0.1'));
    }
}
