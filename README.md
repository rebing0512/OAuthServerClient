# rebing0512/OAuthServerClient
应用/用户客户端 API 鉴权

 这是一个 laravel 的 应用/用户客户端 API 鉴权包， `rebing0512/OAuthServerClient` 采用密钥加密的鉴权方式，只要客户端不被反编译从而泄露密钥，该鉴权方式理论上来说是安全的。
应用对外部端的请求提供接口，非内部系统之间请求鉴权。

## 安装  
```bash
composer require rebing0512/OAuthServerClient
