<!DOCTYPE html>
<html lang="tg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="icon" href="{{ asset('favicon-molfazo.png') }}" type="image/x-icon">

    <title>Дастгирӣ | inBozor</title>

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

        .support-box a{
            color:#000;
            text-decoration:none;
        }

        .support-box a:hover{
            text-decoration:underline;
        }

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
            Маркази дастгирии inBozor
        </h1>

        <div class="support-content">

            <!-- App Info -->
            <div class="support-box">
                <h4>Маълумот дар бораи барнома</h4>

                <p>
                    Хуш омадед ба Маркази дастгирии inBozor.
                    Мо омодаем ба шумо дар ҳалли ҳар гуна савол ё мушкилот кӯмак расонем.
                </p>
            </div>

            <!-- Contact -->
            <div class="support-box">
                <h4>Маълумоти тамос</h4>

                <p>
                    <strong>Почтаи электронии дастгирӣ:</strong>
                    <a href="mailto:support@inbozor.app">
                        support@inbozor.app
                    </a>
                </p>

                <p>
                    <strong>Рақами телефон:</strong>
                    <a href="tel:+992559080800">
                        +992 55 908 0800
                    </a>
                </p>
            </div>

            <!-- Response -->
            <div class="support-box">
                <h4>Муҳлати посух</h4>

                <p>
                    Гурӯҳи мо одатан дар давоми 24–48 соат посух медиҳад.
                </p>
            </div>

            <!-- FAQ -->
            <div class="support-box">
                <h4>Саволҳои зуд-зуд пурсидашаванда</h4>

                <div class="faq-item">
                    <div class="faq-question">
                        <span>Чӣ тавр бо дастгирӣ тамос гирифтан мумкин аст?</span>
                        <span class="faq-icon">+</span>
                    </div>

                    <div class="faq-answer">
                        Шумо метавонед ҳар вақт ба мо нома бинависед:
                        support@inbozor.app
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <span>Чӣ қадар вақт лозим аст барои посух?</span>
                        <span class="faq-icon">+</span>
                    </div>

                    <div class="faq-answer">
                        Одатан дар давоми 24 то 48 соат.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <span>Оё ман метавонам тавассути телефон тамос гирам?</span>
                        <span class="faq-icon">+</span>
                    </div>

                    <div class="faq-answer">
                        Бале, шумо метавонед ба рақами
                        +992 55 908 0800 занг занед.
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