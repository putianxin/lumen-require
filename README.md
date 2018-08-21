# Lumen 生产环境集成

## 类库
- helper.php：laravel公共方法（自动加载）
    - config_path
    - public_path
    - assert
    - cache
    - request
    - logger

## 集成模块
- guzzlehttp/guzzle：用于发起外部请求
- illuminate/redis：laravel redis 封装
- predis/predis：predis 封装

## Env 根据配置判断环境
- Env.php: 
    - isDev
    - isTest
    - isDevOrTest
    - isProd
    - isStaging
    