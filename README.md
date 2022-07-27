# IP查询所属城市

# 安装
---

```shell
composer require wenkaiyang/iq-query -vvv
```

# 配置
---

在使用本扩展之前，你需要去 [聚合数据](https://dashboard.juhe.cn/data/index/my) 开放平台 注册账号，然后申请 IPA 接口，获取应用的 API Key。

# 使用
---

```php
use WenKaiYang\IpQuery;

$key = 'xxxxxxxxxxxxxxxxxxxxxxxxxxx';

$query = new IpQuery($key);

$ip = '127.0.0.1'; // IP4

$response = $query->getCity($ip);
```

# 示例

```php
// 127.0.0.1 
array(5) {
    "Country"=> ""
    "Province"=>""
    "City"=>"内网IP"
    "District"=>""
    "Isp"=>"内网IP"
}

// 183.9.87.86
array(5) {
    "Country"=>"中国"
    "Province"=>"广东"
    "City"=>"梅州"
    "District"=>""
    "Isp"=>"电信"
}
```