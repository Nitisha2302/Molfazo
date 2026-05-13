<!DOCTYPE html>
<html lang="tg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" href="{{ asset('favicon-molfazo.png') }}" type="image/x-icon">

    <title>{{ strip_tags($policy->title ?? 'Privacy Policy') }}</title>

    <link rel="stylesheet" href="{{ asset('assets/admin/bootstrap/bootstrap.min.css') }}">

    <style>
        body{
            background:#f5f5f5;
            font-family: Arial, sans-serif;
        }

        .policy-wrapper{
            max-width: 1000px;
            margin: 50px auto;
            background: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .policy-title{
            margin-bottom: 30px;
            font-size: 32px;
            font-weight: 700;
        }

        .policy-content{
            font-size:16px;
            line-height:1.8;
            color:#333;
        }

        .policy-content h1,
        .policy-content h2,
        .policy-content h3,
        .policy-content h4,
        .policy-content h5,
        .policy-content h6{
            margin-top:25px;
            margin-bottom:15px;
            font-weight:600;
        }

        .policy-content ul{
            padding-left:20px;
        }

        .policy-content img{
            max-width:100%;
            height:auto;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="policy-wrapper">

        <!-- Logo -->
        <div class="d-flex align-items-center justify-content-center mb-4">
            <a class="navbar-brand" href="">
                <img 
                    class="full-imgbox" 
                    src="{{ asset('assets/admin/images/molofzo_logo.png') }}" 
                    width="200" 
                    alt="logo"
                >
            </a>
        </div>

        <!-- Title -->
        <h1 class="policy-title text-center">
            {!! $policy?->title ?? 'Privacy Policy' !!}
        </h1>

        <!-- Content -->
        <div class="policy-content">
            {!! $policy?->content ?? 'No Privacy Policy Found.' !!}
        </div>

    </div>
</div>

</body>
</html>