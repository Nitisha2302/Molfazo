<?php

return [

    'language' => [
        'updated' => 'Забон бомуваффақият иваз шуд',
        'validation' => [
            'required' => 'Лутфан забонро интихоб кунед',
            'in' => 'Забони интихобшуда нодуруст аст',
        ],
    ],

    'logout' => [
        'logout_success' => 'Шумо бомуваффақият хориҷ шудед',
         'user_not_authenticated' => 'Корбар тасдиқ нашудааст',
    ],

    'getProfile' => [
        'success' => 'Маълумоти профил гирифта шуд.',
        'user_not_authenticated' => 'Шумо тасдиқ нашудаед. Лутфан ворид шавед.',
    ],

    'block_user' => [

        'unauthorized' => 'Корбар иҷозат надорад',

        'cannot_block_self' => 'Шумо худатонро блок карда наметавонед',

        'blocked_success' => 'Корбар бомуваффақият блок карда шуд',

        'unblocked_success' => 'Корбар бомуваффақият аз блок бароварда шуд',
    ],


    'delete_account' => [

        'user_not_authenticated' => 'User not authenticated',

        'account_deleted_successfully' => 'Account deleted successfully',

        'account_deleted' => 'Your account has been deleted',
    ],

    'report' => [

        'success' => 'Шикоят бомуваффақият фиристода шуд.',
        'user_not_authenticated' => 'Корбар тасдиқ нашудааст.',
        'cannot_report_self' => 'Шумо наметавонед худро шикоят кунед.',

        'validation' => [

            'reported_user_required' => 'Интихоби корбар ҳатмист.',
            'reported_user_invalid'  => 'Корбар ёфт нашуд.',

            'description_required' => 'Тавсиф ҳатмист.',
            'description_string'   => 'Тавсиф бояд матн бошад.',
            'description_max'      => 'Тавсиф набояд аз 2000 аломат зиёд бошад.',
        ],
    ],

    'enquiry' => [

        'unauthorized' => 'Дастрасӣ иҷозат дода нашудааст.',

        'store' => [
            'success' => 'Дархост бомуваффақият ирсол шуд.',

            'validation' => [
                'title_required'        => 'Лутфан унвони дархостро ворид кунед.',
                'title_string'          => 'Унвон бояд матни дуруст бошад.',
                'description_required'  => 'Лутфан тавсифи дархостро ворид кунед.',
                'description_string'    => 'Тавсиф бояд матни дуруст бошад.',
            ],
        ],

        'list' => [
            'success' => 'Дархостҳо бомуваффақият гирифта шуданд.',
            'empty'   => 'Ягон дархост ёфт нашуд.',
        ],
    ],


    'vendor' => [

        'completeprofile' => [

            'user_not_authenticated' => 'Корбар тасдиқ нашудааст.',
            'mobile_not_verified' => 'Лутфан рақами телефони худро тасдиқ кунед.',
            'register_success' => 'Фурӯшанда бо муваффақият сабт шуд. Интизори тасдиқи администратор.',

            'validation' => [
                'name_required'       => 'Номи пурра ҳатмист.',
                'email_required'     => 'Email ҳатмист.',
                'email_unique'       => 'Ин email аллакай сабт шудааст.',
                'mobile_required'    => 'Рақами телефон ҳатмист.',
                'mobile_unique'      => 'Ин рақами телефон аллакай сабт шудааст.',
                'password_confirmed' => 'Паролҳо мувофиқат намекунанд.',
                'terms_required'     => 'Шумо бояд шартҳоро қабул кунед.',
            ],
        ],


        'send_otp' => [

            'mobile_exists' => 'Ин рақам аллакай сабт шудааст.',
            'otp_sent'      => 'OTP ба рақами шумо фиристода шуд.',
            'otp_test'      => 'OTP худкор тавлид шуд (режими тестӣ)',

            'validation' => [
                'mobile_required' => 'Рақами телефон ҳатмист.',
                'mobile_invalid'  => 'Рақами телефон нодуруст аст.',
            ],

            'sms' => [
                'otp_message' => 'Рамзи тасдиқ: :otp барои ворид шудан ба inBozor',
                //  'otp_message' => 'Ваш код подтверждения: :otp для входа в inBozor',
                
            ],

        ],


        'verify_otp' => [

            'invalid_otp'   => 'OTP нодуруст аст.',
            'otp_expired'   => 'Муҳлати OTP ба анҷом расид.',
            'otp_verified'  => 'OTP бо муваффақият тасдиқ шуд.',

            'validation' => [
                'otp_required'   => 'OTP ҳатмист.',
                'otp_digits'     => 'OTP бояд 6 рақам бошад.',
                'mobile_invalid' => 'Рақами телефон нодуруст аст.',
                'email_invalid'  => 'Email нодуруст аст.',
            ],

        ],



        'login' => [

            'account_not_found' => 'Ҳисоб ёфт нашуд. Лутфан аввал сабти ном кунед.',
            'invalid_credentials' => 'Маълумоти воридшавӣ нодуруст аст.',
            'only_vendor' => 'Танҳо фурӯшандагон метавонанд ворид шаванд.',
            'rejected' => 'Ҳисоби фурӯшандаи шумо рад карда шудааст.',
            'blocked' => 'Ҳисоби шумо баста шудааст. Ба дастгирӣ муроҷиат кунед.',
            'login_success' => 'Воридшавӣ бо муваффақият анҷом шуд.',

            'validation' => [
                'login_required'    => 'Email ё рақами телефон лозим аст.',
                'password_required' => 'Парол лозим аст.',
            ],

        ],

        'login_otp' => [

            'account_not_found' => 'Аккаунт не найден. Пожалуйста, зарегистрируйтесь.',
            'only_vendor'       => 'Только продавцы могут войти здесь.',
            'not_active'        => 'Ваш аккаунт не активен.',
            'otp_sent'          => 'OTP отправлен на ваш номер.',
            'otp_test'          => 'OTP сгенерирован автоматически (тестовый режим).',

            'validation' => [
                'mobile_required' => 'Введите номер телефона.',
                'mobile_invalid'  => 'Номер должен быть от 8 до 15 цифр.',
            ],

            'sms' => [
                'otp_message' => 'Рамзи тасдиқ: :otp барои ворид шудан ба inBozor',
                //  'otp_message' =>  'Ваш код подтверждения: :otp для входа в inBozor',
               
            ],

        ],


        'verify_login_otp' => [

            'invalid' => 'OTP ё рақами телефон нодуруст аст.',
            'expired' => 'Мӯҳлати OTP гузаштааст. Лутфан дубора дархост кунед.',
            'login_success' => 'Воридшавӣ бомуваффақият анҷом шуд.',

            'validation' => [
                'mobile_required' => 'Рақами телефон лозим аст.',
                'mobile_invalid'  => 'Рақами телефон нодуруст аст.',
                'otp_required'    => 'OTP лозим аст.',
                'otp_digits'      => 'OTP бояд 6 рақам бошад.',
            ],

        ],


        'forgot_password' => [

            'success' => 'Пароли нав ба почтаи электронии шумо фиристода шуд.',
            'email_failed' => 'Фиристодани email ноком шуд. Баъдтар кӯшиш кунед.',

            'validation' => [
                'email_required' => 'Email лозим аст.',
                'email_invalid'  => 'Email дуруст ворид кунед.',
                'email_exists'   => 'Email сабт нашудааст.',
            ],

        ],


        'reset_password' => [

            'invalid_password' => 'Парол бо пароли фиристодашуда мувофиқат намекунад.',
            'expired'          => 'Мӯҳлати парол гузаштааст. Лутфан дубора дархост кунед.',
            'success'          => 'Парол бо муваффақият навсозӣ шуд.',

            'validation' => [
                'email_required'    => 'Email лозим аст.',
                'email_invalid'     => 'Email дуруст ворид кунед.',
                'email_exists'      => 'Email ёфт нашуд.',

                'password_required'  => 'Парол лозим аст.',
                'password_digits'    => 'Парол бояд 6 рақам бошад.',
                'password_confirmed' => 'Паролҳо мувофиқат намекунанд.',
            ],

        ],


        'apple_login' => [

            'invalid_token' => 'Токени Apple нодуруст аст.',
            'blocked'       => 'Ҳисоб аз тарафи админ баста шудааст.',
            'success'       => 'Воридшавӣ бо Apple бомуваффақият анҷом шуд.',
            'failed'        => 'Аутентификатсияи Apple ноком шуд.',

            'validation' => [
                'identity_token_required' => 'Identity token лозим аст.',
                'apple_id_required'       => 'Apple ID лозим аст.',
                'email_invalid'           => 'Email дуруст ворид кунед.',
                'name_string'             => 'Ном бояд матн бошад.',
            ],

        ],


        'category' => [

            'list' => [
                'success' => 'Категорияҳо бомуваффақият гирифта шуданд.',
                'empty'   => 'Ягон категория ёфт нашуд.',
            ],

            'subcategory' => [
                'success' => 'Зеркатегорияҳо бомуваффақият гирифта шуданд.',
                'empty'   => 'Ягон зеркатегория ёфт нашуд.',
            ],

            'child_category' => [
                'success' => 'Категорияҳои поёнӣ бомуваффақият гирифта шуданд.',
                'empty'   => 'Ягон категорияи поёнӣ ёфт нашуд.',
            ],

            'attributes' => [
                'success' => 'Хусусиятҳо бомуваффақият гирифта шуданд.',
                'empty'   => 'Барои ин категория ягон хусусият ёфт нашуд.',
            ],
        ],

        'banner' => [
            'success' => 'Баннерҳо бомуваффақият гирифта шуданд.',
            'empty_city' => 'Барои ин шаҳр ягон баннер ёфт нашуд.',
        ],

        'city' => [
            'success' => 'Шаҳрҳо бомуваффақият гирифта шуданд.',
        ],

        'chat' => [

            'list' => [
                'success' => 'Чатҳо бомуваффақият гирифта шуданд.',
                'empty'   => 'Ягон чат ёфт нашуд.',
            ],

            'send' => [
                'success' => 'Паём бомуваффақият фиристода шуд.',
            ],

            'validation' => [
                'receiver_required' => 'Қабулкунанда ҳатмист.',
                'receiver_exists'   => 'Корбари интихобшуда вуҷуд надорад.',
                'message_string'    => 'Паём бояд матни дуруст бошад.',
            ],

            'unauthorized' => 'Корбар тасдиқ нашудааст.',
        ],

        'notification' => [
            'delete' => [
                'success' => 'Бомуваффақият нест карда шуд.',
                'not_found' => 'Огоҳинома ёфт нашуд.',
                'unauthorized' => 'Корбар тасдиқ нашудааст.',
            ],
        ],

        'order' => [

            'unauthorized' => 'Ҳисоби фурӯшанда тасдиқ нашудааст ё дастрасӣ вуҷуд надорад.',

            'list' => [
                'success' => 'Фармоишҳо бомуваффақият гирифта шуданд.',
                'empty'   => 'Ягон фармоиш ёфт нашуд.',
            ],

            'details' => [
                'not_found' => 'Фармоиш ёфт нашуд.',
            ],

            'update_status' => [

                'success' => 'Статуси фармоиш бомуваффақият ба :status тағйир дода шуд.',

                'already_completed' => 'Фармоиш аллакай анҷом шудааст. Статусро тағйир додан мумкин нест.',
                'already_cancelled' => 'Фармоиш аллакай бекор шудааст. Статусро тағйир додан мумкин нест.',
                'accept_first'      => 'Фармоиш бояд аввал қабул карда шавад.',

                'validation' => [
                    'status_required' => 'Статус ҳатмист.',
                    'status_invalid'  => 'Статуси нодуруст. Арзишҳои иҷозатшуда: 2=Қабул шуд, 3=Анҷом шуд, 4=Бекор шуд.',
                ],

                'status_text' => [
                    'accepted'  => 'Қабул шуд',
                    'completed' => 'Анҷом шуд',
                    'cancelled' => 'Бекор шуд',
                    'unknown'   => 'Номаълум',
                ],
            ],

            'notification' => [
                'title' => '📦 Навсозии статуси фармоиш',

                'accepted'  => '✅ Фармоиши шумо барои :products қабул шуд.',
                'completed' => '🎉 Фармоиши шумо барои :products бомуваффақият анҷом шуд.',
                'cancelled' => '❌ Фармоиши шумо барои :products бекор карда шуд.',
                'default'   => 'Статуси фармоиши шумо барои :products навсозӣ шуд.',
            ],
        ],

        'product' => [

            'copy' => [
                'only_approved' => 'Танҳо маҳсулоти тасдиқшуда метавонанд нусхабардорӣ шаванд.',
                'success' => 'Маҳсулот бомуваффақият нусхабардорӣ ва навсозӣ шуд.',
            ],

            'create' => [
                'unauthorized' => 'Ҳисоби фурӯшанда тасдиқ нашудааст ё дастрасӣ вуҷуд надорад.',
                'invalid_store' => 'Мағоза нодуруст ё тасдиқ нашудааст.',
                'child_category_invalid' => 'Категорияи поёнӣ ба зеркатегорияи интихобшуда тааллуқ надорад.',

                'success' => 'Маҳсулот бомуваффақият илова шуд.',

                'validation' => [
                    'store_required' => 'Лутфан мағозаро интихоб кунед.',
                    'store_exists'   => 'Мағозаи интихобшуда вуҷуд надорад.',

                    'name_required'  => 'Номи маҳсулот ҳатмист.',

                    'price_required' => 'Нархи маҳсулот ҳатмист.',
                    'price_numeric'  => 'Нарх бояд рақами дуруст бошад.',

                    'discount_numeric' => 'Нархи тахфиф бояд рақами дуруст бошад.',

                    'quantity_required' => 'Миқдори дастрас ҳатмист.',
                    'quantity_integer'  => 'Миқдор бояд адади бутун бошад.',

                    'images_required' => 'Ҳадди ақал як тасвир лозим аст.',
                    'images_array'    => 'Тасвирҳо бояд дар шакли массив бошанд.',
                    'images_mimes'    => 'Ҳар тасвир бояд JPEG, JPG, PNG ё GIF бошад.',
                ],
            ],

            'list' => [
                'unauthorized' => 'Ҳисоби фурӯшанда тасдиқ нашудааст ё дастрасӣ вуҷуд надорад.',
                'success'      => 'Маҳсулотҳо бомуваффақият гирифта шуданд.',
                'empty'        => 'Ягон маҳсулот ёфт нашуд.',
            ],

            'details' => [
                'unauthorized' => 'Ҳисоби фурӯшанда тасдиқ нашудааст ё дастрасӣ вуҷуд надорад.',
                'not_found'    => 'Маҳсулот ёфт нашуд.',
                'success'      => 'Тафсилоти маҳсулот бомуваффақият гирифта шуд.',
            ],

            'update' => [
                'unauthorized' => 'Фурӯшанда тасдиқ нашудааст.',
                'not_found'    => 'Маҳсулот ёфт нашуд.',
                'success'      => 'Маҳсулот бомуваффақият навсозӣ шуд.',
            ],
        ],

        'combination' => [
            'combination_updated' => 'Комбинатсия бомуваффақият навсозӣ шуд.',
            'combination_deleted' => 'Комбинатсия бомуваффақият нест карда шуд.',
        ],

        'promotion' => [

            'unauthorized' => 'Дастрасӣ иҷозат дода нашудааст.',

            'packages' => [
                'success' => 'Пакетҳо бомуваффақият гирифта шуданд.',
                'with_status_success' => 'Пакетҳо бо статус бомуваффақият гирифта шуданд.',
            ],

            'payment' => [
                'success' => 'Маълумоти пардохт бомуваффақият гирифта шуд.',
            ],

            'store' => [
                'success' => 'Дархости таблиғ бомуваффақият ирсол шуд.',
                'duplicate' => 'Барои ин маҳсулот аллакай дархост дар интизор аст.',
            ],

            'validation' => [
                'product_required' => 'ID-и маҳсулот ҳатмист.',
                'package_required' => 'ID-и пакет ҳатмист.',
                'image_required' => 'Скриншоти пардохт ҳатмист.',
            ],

        ],

        'review' => [

            'unauthorized' => 'Дастрасӣ иҷозат дода нашудааст.',

            'validation' => [
                'title_required'    => 'Сарлавҳа ҳатмист.',
                'review_required'   => 'Шарҳ ҳатмист.',
                'rating_required'   => 'Баҳо ҳатмист.',
                'username_required' => 'Номи корбар ҳатмист.',
            ],

            'promotion' => [
                'not_approved' => 'Таблиғ тасдиқ нашудааст.',
            ],

            'limit' => [
                'reached' => 'Ҳадди ниҳоии баррасиҳо расидааст.',
            ],

            'store' => [
                'success' => 'Барраси бомуваффақият ирсол шуд.',
            ],

        ],

        'store' => [

            // ================= AUTH =================
            'auth' => [
                'not_vendor'   => 'Шумо фурӯшанда нестед.',
                'not_approved' => 'Ҳисоби фурӯшандаи шумо ҳанӯз тасдиқ нашудааст. Лутфан интизори тасдиқи админ бошед.',
                'unauthorized' => 'Корбар тасдиқ нашудааст.',
                'not_found'    => 'Мағоза ёфт нашуд.',
                'not_owner'    => 'Шумо иҷозати дастрасӣ ба ин мағозаро надоред.',
            ],

            'create' => [
                'success' => 'Мағоза бомуваффақият сохта шуд. Интизори тасдиқи админ.',
            ],

            'list' => [
                'success' => 'Мағоза бомуваффақият гирифта шуд.',
            ],

            'details' => [
                'success' => 'Тафсилоти мағоза бомуваффақият гирифта шуд.',
            ],

            'update' => [
                'success' => 'Мағоза бомуваффақият навсозӣ шуд. Интизори тасдиқи админ.',
            ],

            // ================= VALIDATION =================
            'validation' => [

                'name_required'    => 'Номи мағоза ҳатмист.',
                'mobile_required'  => 'Рақами телефони мағоза ҳатмист.',
                'email_required'   => 'Email-и мағоза ҳатмист.',
                'email_invalid'    => 'Email бояд дуруст бошад.',
                'country_required' => 'Кишвар ҳатмист.',
                'city_required'    => 'Шаҳр ҳатмист.',
                'address_required' => 'Суроғаи пурра ҳатмист.',

                'type_required' => 'Навъи мағоза ҳатмист.',
                'type_array'    => 'Навъи мағоза бояд массив бошад.',
                'type_invalid'  => 'Навъи мағоза бояд яке аз инҳо бошад: Retail, Online, Wholesale, Offline.',

                'logo_image' => 'Лого бояд файл тасвир бошад.',
                'logo_mimes' => 'Лого бояд jpeg, png, jpg, gif ё webp бошад.',
                'logo_max'   => 'Андозаи лого набояд аз 2MB зиёд бошад.',

                'background_image' => 'Заминаи мағоза бояд тасвир бошад.',
                'background_mimes' => 'Замина бояд jpeg, png, jpg, gif ё webp бошад.',
                'background_max'   => 'Андозаи замина набояд аз 4MB зиёд бошад.',

                'document_array' => 'Ҳуҷҷатҳо бояд массив бошанд.',
                'document_mimes' => 'Ҳуҷҷатҳо бояд jpg, png ё pdf бошанд.',
                'document_max'   => 'Ҳар ҳуҷҷат набояд аз 4MB зиёд бошад.',

                'social_invalid' => 'Формати линк нодуруст аст.',
                'color_string'   => 'Ранги замина бояд матн бошад.',
            ],

            // ================= VIDEO =================
            'video' => [

                // ================= AUTH =================
               'auth' => [
                    'unauthorized' => 'Корбари тасдиқнашуда.',
                    'invalid_store' => 'Мағоза ёфт нашуд ё ба корбар тааллуқ надорад.',
                ],

                'plans' => [
                    'success' => 'Планҳо бомуваффақият гирифта шуданд.',
                    'with_status_success' => 'Планҳо бо статус бомуваффақият гирифта шуданд.',
                ],

                'request' => [
                    'success' => 'Дархост ба админ фиристода шуд.',
                    'duplicate' => 'Шумо аллакай барои ин план дархости интизор доред.',
                    'invalid_store' => 'Мағозаи нодуруст ё ба корбар тааллуқ надорад.',
                ],

                'upload' => [
                    'unauthorized'      => 'Корбари тасдиқнашуда.',
                    'permission_denied' => 'Шумо иҷозати боргузории видео барои ин мағозаро надоред.',
                    'no_plan'           => 'Плани тасдиқшуда ёфт нашуд. Лутфан аввал харидорӣ ва тасдиқ гиред.',
                    'expired'           => 'Плани қаблии шумо ба охир расидааст. Лутфан нав кунед.',
                    'success'           => 'Видео бомуваффақият боргузорӣ шуд.',
                    'failed'            => 'Боргузорӣ ноком шуд.',
                    'missing_chunk'     => 'Қисм (chunk) бо индекси :index ёфт нашуд',
                ],

                // ================= VALIDATION =================
                'validation' => [

                    // store
                    'store_required' => 'ID-и мағоза ҳатмист.',
                    'store_exists'   => 'Мағоза ёфт нашуд.',

                    // plan
                    'plan_required' => 'Лутфан планро интихоб кунед.',
                    'plan_exists'   => 'Плани интихобшуда нодуруст аст.',

                    // payment
                    'payment_required' => 'Скриншоти пардохт ҳатмист.',
                    'payment_image'    => 'Файл бояд тасвир бошад.',
                    'payment_mimes'    => 'Танҳо JPG, JPEG, PNG иҷозат дода мешавад.',
                    'payment_max'      => 'Андозаи тасвир бояд аз 2MB кам бошад.',

                    // chunk upload
                    'chunk_required' => 'Қисми видео (chunk) ҳатмист.',
                    'chunk_file'     => 'Файли chunk нодуруст аст.',
                    'chunk_mimes'    => 'Танҳо MP4, MOV, AVI иҷозат дода мешавад.',

                    'chunk_index_required' => 'Индекси chunk ҳатмист.',
                    'chunk_index_integer'  => 'Индекси chunk бояд рақам бошад.',
                    'chunk_index_min'      => 'Индекси chunk бояд 0 ё бештар бошад.',

                    'total_chunks_required' => 'Шумораи умумии chunkҳо ҳатмист.',
                    'total_chunks_integer'  => 'Шумораи chunkҳо бояд рақам бошад.',
                    'total_chunks_min'      => 'Шумораи chunkҳо бояд камаш 1 бошад.',

                    'upload_id_required' => 'Upload ID ҳатмист.',
                ],

                // ================= LOGIC ERRORS =================
                'error' => [
                    'store_not_found' => 'Мағоза ёфт нашуд ё ба корбар тааллуқ надорад.',
                    'plan_invalid'    => 'Танзимоти план нодуруст аст.',
                    'no_approved_plan'=> 'Плани видеоии тасдиқшуда ёфт нашуд. Лутфан аввал харидорӣ ва тасдиқ гиред.',
                    'expired_plan'    => 'Плани қаблии видеоии шумо ба охир расидааст. Лутфан онро нав кунед.',
                ],
            ],
        ],

        'bank' => [

            // ================= AUTH =================
            'auth' => [
                'unauthorized' => 'Фурӯшандаи тасдиқнашуда.',
            ],

            // ================= SUCCESS =================
            'success' => [
                'updated' => 'Усулҳои пардохт бомуваффақият навсозӣ шуданд.',
                'fetched' => 'Маълумоти пардохт бомуваффақият гирифта шуд.',
            ],

            // ================= VALIDATION =================
            'validation' => [

                'payment_modes_required' => 'Усули пардохт ҳатмист.',
                'payment_modes_array'    => 'Усули пардохт бояд массив бошад.',
                'payment_modes_invalid'  => 'Усули пардохт бояд COD ё Bank бошад.',

                'banks_required' => 'Маълумоти бонкӣ ҳангоми интихоби Bank ҳатмист.',

                'bank_id_required' => 'ID-и бонк ҳатмист.',
                'bank_id_exists'   => 'Бонки интихобшуда вуҷуд надорад.',

                'account_holder_required' => 'Номи соҳиби ҳисоб ҳатмист.',
                'account_number_required' => 'Рақами ҳисоб ҳатмист.',
            ],
        ],


      









    ],

    'customer' => [

        'login' => [

            'vendor_exists' => 'Ин рақам аллакай ҳамчун фурӯшанда сабт шудааст',
            'blocked'       => 'Ҳисоби шумо баста шудааст.',
            'deleted'       => 'Ҳисоби шумо нест карда шудааст',
            'otp_sent'      => 'Рамзи тасдиқ ба шумо фиристода шуд.',

            'validation' => [
                'phone_required' => 'Рақами телефон ҳатмист',
                'phone_invalid'  => 'Рақами телефон нодуруст аст',
            ],

        ],

        'sms' => [
            // 'otp' => 'Рамзи тасдиқи шумо барои ворид шудан ба inBozor: :otp',
           'otp' => 'Рамзи тасдиқ: :otp барои ворид шудан ба inBozor',

             
        ],

        'verify_otp' => [

            'invalid' => 'Рамз нодуруст аст',
            'expired' => 'Муҳлати рамзи тасдиқ ба анҷом расид',
            'success' => 'Рамз бомуваффақият тасдиқ шуд',

            'validation' => [
                'phone_required' => 'Рақами телефон ҳатмист',
                'otp_required'   => 'Рамз ҳатмист',
                'otp_digits'     => 'OTP нодуруст аст.',
            ],

        ],

        'update_profile' => [

            'unauthenticated' => 'Корбар тасдиқ нашудааст.',
            'success'         => 'Профил бомуваффақият навсозӣ шуд.',

            'validation' => [
                'name_string'        => 'Ном бояд матн бошад.',
                'name_max'           => 'Ном набояд аз 255 ҳарф зиёд бошад.',
                'email_invalid'      => 'Email дуруст ворид кунед.',
                'email_unique'       => 'Ин email аллакай истифода шудааст.',
                'mobile_unique'      => 'Ин рақам аллакай истифода шудааст.',
                'alt_mobile_string'  => 'Рақами иловагӣ бояд матн бошад.',
                'country_string'     => 'Кишвар бояд матн бошад.',
                'city_string'        => 'Шаҳр бояд матн бошад.',
                'photo_image'        => 'Акс бояд файл-расм бошад.',
                'photo_mimes'        => 'Акс бояд jpeg, png, jpg ё gif бошад.',
                'photo_max'          => 'Акс набояд аз 2MB зиёд бошад.',
            ],

        ],

        'address' => [

            'saved' => 'Адрес бомуваффақият сабт шуд',
            'list_success' => 'Рӯйхати суроғаҳо бо муваффақият гирифта шуд.',
            'deleted'   => 'Адрес ҳазф шуд',
            'not_found' => 'Address not found.',

            'not_belongs'      => 'Ин адрес ба ҳисоби шумо тааллуқ надорад',
            'already_default'  => 'Ин адрес аллакай асосӣ аст.',
            'default_updated'  => 'Адреси асосӣ навсозӣ шуд',

            'validation' => [
                'name_required'      => 'Навъи адресро интихоб кунед (хона / коргоҳ)',
                'full_name_required' => 'Ному насаб ҳатмист',
                'mobile_required'    => 'Рақами телефон ҳатмист',
                'mobile_digits'      => 'Рақам бояд аз 10 рақам иборат бошад',
                'address_required'   => 'Адрес холӣ буда наметавонад',
                'city_required'      => 'Шаҳрро ворид кунед',
                'state_required'     => 'Вилоятро ворид кунед',
                'pincode_required'   => 'Индекси почта лозим аст',
                'pincode_digits'     => 'Индекс бояд 6 рақам бошад',

                'address_required' => 'Лутфан адресро интихоб кунед',
                'address_exists'   => 'Адреси интихобшуда вуҷуд надорад',
            ],

        ],


        'cart' => [
            'unauthorized' => 'Дастрасӣ иҷозат дода нашудааст.',
            'product_not_available' => 'Маҳсулот дастрас нест.',
            'invalid_combination' => 'Комбинатсияи маҳсулот нодуруст аст.',
            'insufficient_stock' => 'Миқдори кофии маҳсулот дастрас нест.',
            'added_successfully' => 'Маҳсулот ба сабад бомуваффақият илова шуд.',

            'list_success' => 'Сабад бомуваффақият гирифта шуд.',
            'empty' => 'Сабад холӣ аст.',
            'updated' => 'Сабад бомуваффақият навсозӣ шуд.',
            'removed' => 'Маҳсулот аз сабад хориҷ карда шуд.',
            'not_found' => 'Маҳсулот дар сабад ёфт нашуд.',

            'validation' => [
                'unauthorized' => 'Корбар тасдиқ нашудааст.',
                'cart_id_required' => 'ID-и сабад ҳатмист.',
                'cart_id_invalid' => 'Сабади интихобшуда нодуруст аст.',
                'quantity_required' => 'Миқдор ҳатмист.',
                'quantity_integer' => 'Миқдор бояд рақам бошад.',
                'quantity_min' => 'Миқдор бояд на камтар аз 1 бошад.',
            ],
        ],


        'category' => [

            'list_success' => 'Категорияҳо гирифта шуданд.',
            'sub_list_success'    => 'Зеркатегорияҳо гирифта шуданд',
            'child_list_success'  => 'Категорияҳои иловагӣ гирифта шуданд',

        ],


        'chat' => [

            'unauthorized' => 'User not authenticated.',
            'conversation_started' => 'Сӯҳбат оғоз шуд',
            'conversation_list' => 'Рӯйхати сӯҳбатҳо гирифта шуд',
            'messages_fetched' => 'Паёмҳо гирифта шуданд',
            'message_sent' => 'Паём фиристода шуд',

             // ✅ Notifications
            'new_message_title' => 'Паёми нав',
            'image_sent' => '📷 Сурат фиристод',
            'new_message' => 'Паёми нав гирифта шуд',

            'validation' => [
                'other_user_required' => 'ID-и корбари дигар лозим аст',
                'other_user_invalid'  => 'Корбари дигар ёфт нашуд',
                'self_chat'           => 'Шумо наметавонед бо худ сӯҳбат кунед.',
                'conversation_required'=> 'ID-и сӯҳбат лозим аст.',
                'conversation_invalid' => 'Сӯҳбат ёфт нашуд',
                'message_required'     => 'Паёмро ворид кунед',
                'image_invalid'       => 'Файли тасвир нодуруст аст.',
            ],

            'not_participant' => 'Шумо иштирокчии ин сӯҳбат нестед',
            'not_found'       => 'Сӯҳбат ёфт нашуд',
        ],

        'order' => [

            // ✅ PLACE ORDER
            'place' => [

                // ✅ GENERAL
                'unauthorized' => 'Корбар тасдиқ нашудааст',
                'success'      => 'Фармоиш бомуваффақият қабул шуд.',
                'failed'       => 'Failed to place order.',

                // ✅ CART
                'empty_cart' => 'Сабади шумо холӣ аст',
                'multi_store' => 'Дар як фармоиш маҳсулоти аз якчанд мағоза иҷозат нест.',

                // ✅ ADDRESS
                'invalid_address' => 'Адреси интихобшуда нодуруст аст',

                // ✅ PAYMENT
                'bank_not_supported' => 'Ин фурӯшанда пардохти бонкиро қабул намекунад',
                'bank_not_available' => 'Ин бонк барои ин фурӯшанда дастрас нест',

                //  STOCK
                'stock' => [
                    'variant_out' => 'Варианти интихобшудаи :product дар анбор нест.',
                    'product_out' => 'Маҳсулоти ":product" миқдори кофӣ надорад.',
                ],

                //  VALIDATION
                'validation' => [
                    'address_required' => 'Барои расонидан адрес лозим аст.',
                    'address_invalid'  => 'Адреси интихобшуда нодуруст аст',
                    'payment_required' => 'Навъи пардохтро интихоб кунед',
                    'payment_invalid'  => 'Пардохт бояд нақдӣ ё онлайн бошад',
                    'bank_required'    => 'Барои пардохти онлайн бонкро интихоб кунед',
                    'bank_invalid'     => 'Бонки интихобшуда нодуруст аст',
                ],

                //  NOTIFICATION
                'notification' => [
                    'title' => '🛒 Фармоиши нав',
                    'body'  => ':user фармоиши нав барои :product гузошт',
                ],

            ],


            //  ORDER LIST
            'list' => [
                'success' => 'Фармоишҳо бомуваффақият гирифта шуданд.',
                'empty'   => 'Ягон фармоиш ёфт нашуд.',
            ],

            'details' => [
                'not_found' => 'Фармоиш ёфт нашуд.',
                'success'   => 'Тафсилоти фармоиш бомуваффақият гирифта шуд.',
            ],

            //  STATUS TEXTS
            'status' => [
                'new'       => 'New',
                'accepted'  => 'Accepted',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
                'unknown'   => 'Unknown',
            ],

        ],

        'product' => [
            'list' => [
                'success' => 'Маҳсулот гирифта шуданд',
                'empty'   =>'Ягон маҳсулот дастрас нест.',
            ],

            'details' => [
                'success'   => 'Маълумоти маҳсулот гирифта шуд',
                'not_found' => 'Маҳсулот ёфт нашудa',
            ],

            'search' => [
                'success' => 'Натиҷаҳои ҷустуҷӯ гирифта шуданд',
                'empty'   => 'Маҳсулот ёфт нашудa',
            ],
        ],


        'review' => [

            'unauthorized' => 'Корбар тасдиқ нашудааст.',

            'submitted' => 'Шарҳ фиристода шуд.',
            'validation_error' => 'Validation Error',

            'not_found' => 'Маҳсулот ёфт нашуд',

            'list_success' => 'Баррасиҳо бомуваффақият гирифта шуданд.',

            'validation' => [
                'product_required' => 'ID-и маҳсулот ҳатмист.',
                'product_exists'   => 'Маҳсулоти интихобшуда вуҷуд надорад.',
                'rating_required'  => 'Баҳо (rating) ҳатмист.',
                'rating_integer'   => 'Баҳо бояд рақам бошад.',
                'rating_min'       => 'Баҳо бояд ҳадди ақал 1 ситора бошад.',
                'rating_max'       => 'Баҳо набояд аз 5 ситора зиёд бошад.',
                'review_string'    => 'Шарҳ бояд матни дуруст бошад.',
                'review_max'       => 'Шарҳ набояд аз 1000 аломат зиёд бошад.',
                'image_invalid'    => 'Ҳар файл бояд тасвир бошад.',
                'image_mimes'      => 'Тасвирҳо бояд JPG, JPEG ё PNG бошанд.',
                'image_max'        => 'Ҳар тасвир набояд аз 2MB зиёд бошад.',
            ],

        ],

        'store' => [

            'list' => [
                'success' => 'Мағозаҳо гирифта шуданд',
                'empty'   => 'Мағоза ёфт нашуд',
            ],

            'details' => [
                'success' => 'Тафсилоти мағоза бомуваффақият гирифта шуд.',
                'not_found' => 'Мағоза ёфт нашуд.',
                'no_products' => 'Барои ин мағоза ягон маҳсулот ёфт нашуд.',
            ],

        ],




    ],


];
