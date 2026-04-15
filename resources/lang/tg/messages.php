<?php

return [

    'language' => [
        'updated' => 'Язык успешно обновлен.',
        'validation' => [
            'required' => 'Выберите язык.',
            'in' => 'Выбран неверный язык.',
        ],
    ],

    'logout' => [
        'logout_success' => 'Шумо муваффақона аз система баромадед.',
         'user_not_authenticated' => 'Шумо тасдиқ нашудаед. Лутфан ворид шавед.',
    ],

    'getProfile' => [
        'success' => 'Профил бор карда шуд.',
        'user_not_authenticated' => 'Шумо тасдиқ нашудаед. Лутфан ворид шавед.',
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

      









    ],

    'customer' => [

        'login' => [

            'vendor_exists' => 'Ин рақам аллакай ҳамчун фурӯшанда сабт шудааст.',
            'blocked'       => 'Ҳисоби шумо баста шудааст.',
            'deleted'       => 'Ҳисоби шумо ҳазф шудааст.',
            'otp_sent'      => 'OTP бомуваффақият фиристода шуд.',

            'validation' => [
                'phone_required' => 'Рақами телефон лозим аст.',
                'phone_invalid'  => 'Рақами телефон нодуруст аст.',
            ],

        ],

        'sms' => [
            'otp' => 'Рамзи тасдиқ: :otp барои ворид шудан ба inBozor',
        ],

        'verify_otp' => [

            'invalid' => 'OTP нодуруст аст.',
            'expired' => 'Мӯҳлати OTP гузаштааст.',
            'success' => 'OTP бомуваффақият тасдиқ шуд.',

            'validation' => [
                'phone_required' => 'Рақами телефон лозим аст.',
                'otp_required'   => 'OTP лозим аст.',
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

            'saved' => 'Address saved successfully.',
            'list_success' => 'Address list fetched successfully.',
            'deleted'   => 'Address removed successfully.',
            'not_found' => 'Address not found.',

            'not_belongs'      => 'This address does not belong to your account.',
            'already_default'  => 'This address is already set as default.',
            'default_updated'  => 'Default address updated successfully.',

            'validation' => [
                'name_required'      => 'Address type is required (Home / Office).',
                'full_name_required' => 'Full name is required.',
                'mobile_required'    => 'Mobile number is required.',
                'mobile_digits'      => 'Mobile number must be 10 digits.',
                'address_required'   => 'Address field cannot be empty.',
                'city_required'      => 'City is required.',
                'state_required'     => 'State is required.',
                'pincode_required'   => 'Pincode is required.',
                'pincode_digits'     => 'Pincode must be 6 digits.',

                'address_required' => 'Please select an address.tj',
                'address_exists'   => 'Selected address does not exist.',
            ],

        ],


        'cart' => [
            'unauthorized' => 'Unauthorized access.',
            'product_not_available' => 'Product not available.',
            'invalid_combination' => 'Invalid product combination.',
            'insufficient_stock' => 'Insufficient stock available.',
            'added_successfully' => 'Product added to cart successfully.',

            'list_success' => 'Cart fetched successfully.',
           'empty'        => 'Cart is empty.',
            'updated'      => 'Cart updated successfully.',
             'removed'      => 'Item removed from cart.',
              'not_found' => 'Cart item not found.',

           'validation' => [
                'unauthorized' => 'User not authenticated.',
                 'cart_id_required' => 'Cart ID is required.',
                'cart_id_invalid'  => 'Invalid cart item.',
                'quantity_required'=> 'Quantity is required.',
                'quantity_integer' => 'Quantity must be a number.',
                'quantity_min'     => 'Quantity must be at least 1.',
            ],

        ],


        'category' => [

            'list_success' => 'Categories retrieved successfully.',
            'sub_list_success'    => 'Subcategories retrieved successfully.',
            'child_list_success'  => 'Child categories retrieved successfully.',

        ],


        'chat' => [

            'unauthorized' => 'User not authenticated.',
            'conversation_started' => 'Conversation started successfully.',
            'conversation_list' => 'Conversation list fetched successfully.',
            'messages_fetched' => 'Messages fetched successfully.',
            'message_sent' => 'Message sent successfully.',

            'validation' => [
                'other_user_required' => 'Other user id is required.',
                'other_user_invalid'  => 'Other user does not exist.',
                'self_chat'           => 'You cannot chat with yourself.',
                'conversation_required'=> 'Conversation id is required.',
                'conversation_invalid' => 'Conversation not found.',
                'message_required'     => 'Message is required.',
                'image_invalid'       => 'Invalid image file.',
            ],

            'not_participant' => 'You are not a participant in this conversation.',
            'not_found'       => 'Conversation not found.',
        ],




    ],


];
