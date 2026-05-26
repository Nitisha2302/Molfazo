<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" href="{{ asset('favicon-molfazo.png') }}" type="image/x-icon">

    <title>Support | inBozor</title>

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

        .support-title{
            margin-bottom: 30px;
            font-size: 32px;
            font-weight: 700;
            text-align:center;
        }

        .support-content{
            font-size:16px;
            line-height:1.8;
            color:#333;
        }

        .support-box{
            background:#f8f8f8;
            padding:20px;
            border-radius:10px;
            margin-bottom:20px;
        }

        .support-box h4{
            font-size:22px;
            font-weight:600;
            margin-bottom:15px;
        }

        /* FAQ */

        .faq-item{
            background:#fff;
            border-radius:10px;
            margin-bottom:15px;
            overflow:hidden;
            border:1px solid #ececec;
        }

        .faq-question{
            padding:20px;
            cursor:pointer;
            display:flex;
            justify-content:space-between;
            align-items:center;
            font-size:18px;
            font-weight:600;
            transition:0.3s;
        }

        .faq-question:hover{
            background:#f3f3f3;
        }

        .faq-icon{
            font-size:28px;
            font-weight:300;
            transition:0.3s;
        }

        .faq-answer{
            display:none;
            padding:0 20px 20px;
            color:#555;
            font-size:16px;
            border-top:1px solid #eee;
        }

        .faq-item.active .faq-answer{
            display:block;
        }

        .faq-item.active .faq-icon{
            transform:rotate(180deg);
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
                    alt="logo">
            </a>
        </div>

        <!-- Title -->
        <h1 class="support-title">
            inBozor Support
        </h1>

        <div class="support-content">

            <!-- App Info -->
            <div class="support-box">

                <h4>App Information</h4>

                <p>
                    Welcome to inBozor Support Center.
                    We are here to help you with any issue or question.
                </p>

            </div>

            <!-- Contact -->
            <div class="support-box">

                <h4>Contact Information</h4>

                <p>
                    <strong>Support Email:</strong>
                    support@inBozor.com
                </p>

            </div>

            <!-- Response -->
            <div class="support-box">

                <h4>Response Time</h4>

                <p>
                    Our team usually responds within 24–48 hours.
                </p>

            </div>

            <!-- FAQ -->
            <div class="support-box">

                <h4>Frequently Asked Questions</h4>

                <!-- FAQ Item -->
                <!-- <div class="faq-item">

                    <div class="faq-question">

                        <span>How do I reset my password?</span>

                        <span class="faq-icon">+</span>

                    </div>

                    <div class="faq-answer">

                        Use the "Forgot Password" option on the login screen.

                    </div>

                </div> -->

                <!-- FAQ Item -->
                <div class="faq-item">

                    <div class="faq-question">

                        <span>How can I contact support?</span>

                        <span class="faq-icon">+</span>

                    </div>

                    <div class="faq-answer">

                        You can email us anytime at support@inBozor.com

                    </div>

                </div>

                <!-- FAQ Item -->
                <div class="faq-item">

                    <div class="faq-question">

                        <span>How long does support take?</span>

                        <span class="faq-icon">+</span>

                    </div>

                    <div class="faq-answer">

                        Usually within 24 to 48 hours.

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script>

    const faqItems = document.querySelectorAll('.faq-item');

    faqItems.forEach(item => {

        const question = item.querySelector('.faq-question');
        const icon = item.querySelector('.faq-icon');

        question.addEventListener('click', () => {

            faqItems.forEach(otherItem => {

                if(otherItem !== item){
                    otherItem.classList.remove('active');
                    otherItem.querySelector('.faq-icon').innerText = '+';
                }

            });

            item.classList.toggle('active');

            if(item.classList.contains('active')){
                icon.innerText = '−';
            }else{
                icon.innerText = '+';
            }

        });

    });

</script>

</body>
</html>