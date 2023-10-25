API
    - register
    - login
    
create helpers
    in autoload composer.json
    - add 
        }
            "files": [
                "app/helpers.php"
            ]
        }
    - composer dump-autoload
    - 

Middleware
    - php artisan make:middleware JwtMiddleware
        - JwtMiddleware
            add 
            . use Tymon\JWTAuth\Facades\JWTAuth;
            . use Tymon\JWTAuth\Exceptions\JWTException;  

midtrans
    - instal midtrans
        . composer require midtrans/midtrans-php
    - set up configurate to connet midtrans in .env
        . MIDTRANS_SERVER_KEY=SB-Mid-server-YlbXAIx4PQN8czILCHNyXc60
        . MIDTRANS_IS_PRODUCTION=false
        . MIDTRANS_IS_SANITIZED=true
        . MIDTRANS_IS_3DS=true

WebHook for midtrans
    - create controller webhook
    - 
