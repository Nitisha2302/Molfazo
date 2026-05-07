<?php

return [

    'language' => [
        'updated' => 'Язык успешно обновлён.',
        'validation' => [
            'required' => 'Пожалуйста, выберите язык',
            'in' => 'Выбран неверный язык',
        ],
    ],

    
    'logout' => [
        'logout_success' => 'Вы успешно вышли из системы.',
         'user_not_authenticated' => 'Пользователь не авторизован',
    ],

    'getProfile' => [
        'success' => 'Данные профиля получены.',
        'user_not_authenticated' => 'User not authenticated.',
    ],

    'enquiry' => [

        'unauthorized' => 'Доступ запрещён.',

        'store' => [
            'success' => 'Запрос успешно отправлен.',

            'validation' => [
                'title_required'        => 'Пожалуйста, введите заголовок запроса.',
                'title_string'          => 'Заголовок должен быть корректным текстом.',
                'description_required'  => 'Пожалуйста, введите описание запроса.',
                'description_string'    => 'Описание должно быть корректным текстом.',
            ],
        ],

        'list' => [
            'success' => 'Запросы успешно получены.',
            'empty'   => 'Запросы не найдены.',
        ],
    ],


    'vendor' => [

        'completeprofile' => [

            'user_not_authenticated' => 'Пользователь не авторизован.',
            'mobile_not_verified' => 'Пожалуйста, подтвердите номер телефона.',
            'register_success' => 'Поставщик успешно зарегистрирован. Ожидается подтверждение администратора.',

            'validation' => [
                'name_required'       => 'Полное имя обязательно.',
                'email_required'     => 'Email обязателен.',
                'email_unique'       => 'Этот email уже зарегистрирован.',
                'mobile_required'    => 'Номер телефона обязателен.',
                'mobile_unique'      => 'Этот номер телефона уже зарегистрирован.',
                'password_confirmed' => 'Пароли не совпадают.',
                'terms_required'     => 'Вы должны принять условия.',
            ],
        ],

 

        'send_otp' => [

            'mobile_exists' => 'Этот номер уже зарегистрирован.',
            'otp_sent'      => 'OTP отправлен на ваш номер.',
            'otp_test'      => 'OTP сгенерирован автоматически (тестовый режим)',

            'validation' => [
                'mobile_required' => 'Номер телефона обязателен.',
                'mobile_invalid'  => 'Неверный номер телефона.',
            ],

            'sms' => [
                'otp_message' => 'Ваш код подтверждения: :otp для входа в inBozor',
            ],

        ],


        'verify_otp' => [

            'invalid_otp'   => 'Неверный OTP.',
            'otp_expired'   => 'Срок действия OTP истек.',
            'otp_verified'  => 'OTP успешно подтвержден.',

            'validation' => [
                'otp_required'   => 'OTP обязателен.',
                'otp_digits'     => 'OTP должен содержать 6 цифр.',
                'mobile_invalid' => 'Неверный номер телефона.',
                'email_invalid'  => 'Неверный email.',
            ],

        ],


       

        'login' => [
            'account_not_found' => 'Аккаунт не найден. Пожалуйста, зарегистрируйтесь.',
            'invalid_credentials' => 'Неверные данные для входа.',
            'only_vendor' => 'Только продавцы могут войти здесь.',
            'rejected' => 'Ваш аккаунт продавца отклонен.',
            'blocked' => 'Ваш аккаунт заблокирован. Свяжитесь с поддержкой.',
            'login_success' => 'Вход выполнен успешно.',

            'validation' => [
                'login_required'    => 'Введите email или номер телефона.',
                'password_required' => 'Введите пароль.',
            ],
        ],

       

        'login_otp' => [

            'account_not_found' => 'Account not found. Please register first.',
            'only_vendor'       => 'Only vendor accounts can login here.',
            'not_active'        => 'Your account is not active.',
            'otp_sent'          => 'OTP sent to your mobile number.',
            'otp_test'          => 'Auto OTP generated (testing mode).',

            'validation' => [
                'mobile_required' => 'Please enter your mobile number.',
                'mobile_invalid'  => 'Mobile number must be between 8 and 15 digits.',
            ],

            'sms' => [
                'otp_message' => 'Ваш код подтверждения: :otp для входа в inBozor',
            ],

        ],

        'verify_login_otp' => [

            'invalid' => 'Неверный OTP или номер телефона.',
            'expired' => 'Срок действия OTP истек. Запросите новый.',
            'login_success' => 'Вход выполнен успешно.',

            'validation' => [
                'mobile_required' => 'Введите номер телефона.',
                'mobile_invalid'  => 'Неверный номер телефона.',
                'otp_required'    => 'Введите OTP.',
                'otp_digits'      => 'OTP должен содержать 6 цифр.',
            ],

        ],


        'forgot_password' => [

            'success' => 'Новый пароль отправлен на ваш email.',
            'email_failed' => 'Не удалось отправить email. Попробуйте позже.',

            'validation' => [
                'email_required' => 'Email обязателен.',
                'email_invalid'  => 'Введите корректный email.',
                'email_exists'   => 'Email не зарегистрирован.',
            ],

        ],


        'reset_password' => [

            'invalid_password' => 'Пароль не совпадает с отправленным.',
            'expired'          => 'Срок действия пароля истек. Запросите снова.',
            'success'          => 'Пароль успешно обновлен.',

            'validation' => [
                'email_required'    => 'Email обязателен.',
                'email_invalid'     => 'Введите корректный email.',
                'email_exists'      => 'Email не найден.',

                'password_required'  => 'Пароль обязателен.',
                'password_digits'    => 'Пароль должен содержать 6 цифр.',
                'password_confirmed' => 'Пароли не совпадают.',
            ],

        ],


        'apple_login' => [

            'invalid_token' => 'Неверный Apple токен.',
            'blocked'       => 'Аккаунт заблокирован администратором.',
            'success'       => 'Вход через Apple выполнен успешно.',
            'failed'        => 'Ошибка аутентификации Apple.',

            'validation' => [
                'identity_token_required' => 'Требуется identity token.',
                'apple_id_required'       => 'Требуется Apple ID.',
                'email_invalid'           => 'Введите корректный email.',
                'name_string'             => 'Имя должно быть строкой.',
            ],

        ],


        'category' => [

            'list' => [
                'success' => 'Категории успешно получены.',
                'empty'   => 'Категории не найдены.',
            ],

            'subcategory' => [
                'success' => 'Подкатегории успешно получены.',
                'empty'   => 'Подкатегории не найдены.',
            ],

            'child_category' => [
                'success' => 'Дочерние категории успешно получены.',
                'empty'   => 'Дочерние категории не найдены.',
            ],

            'attributes' => [
                'success' => 'Атрибуты успешно получены.',
                'empty'   => 'Для этой категории атрибуты не найдены.',
            ],
        ],

        'banner' => [
            'success' => 'Баннеры успешно получены.',
            'empty_city' => 'Для этого города баннеры не найдены.',
        ],

        'city' => [
            'success' => 'Список городов успешно получен.',
        ],

        'chat' => [

            'list' => [
                'success' => 'Чаты успешно получены.',
                'empty'   => 'Чаты не найдены.',
            ],

            'send' => [
                'success' => 'Сообщение успешно отправлено.',
            ],

            'validation' => [
                'receiver_required' => 'Получатель обязателен.',
                'receiver_exists'   => 'Выбранный пользователь не существует.',
                'message_string'    => 'Сообщение должно быть корректным текстом.',
            ],

            'unauthorized' => 'Пользователь не авторизован.',
        ],

        'notification' => [
            'delete' => [
                'success' => 'Успешно удалено.',
                'not_found' => 'Уведомление не найдено.',
                'unauthorized' => 'Пользователь не авторизован.',
            ],
        ],

        'order' => [

            'unauthorized' => 'Аккаунт продавца не подтверждён или пользователь не авторизован.',

            'list' => [
                'success' => 'Список заказов успешно получен.',
                'empty'   => 'Заказы не найдены.',
            ],

            'details' => [
                'not_found' => 'Заказ не найден.',
            ],

            'update_status' => [

                'success' => 'Статус заказа успешно обновлён на :status.',

                'already_completed' => 'Заказ уже завершён. Статус нельзя изменить.',
                'already_cancelled' => 'Заказ уже отменён. Статус нельзя изменить.',
                'accept_first'      => 'Сначала необходимо принять заказ.',

                'validation' => [
                    'status_required' => 'Статус обязателен.',
                    'status_invalid'  => 'Неверный статус. Допустимые значения: 2=Принят, 3=Завершён, 4=Отменён.',
                ],

                'status_text' => [
                    'accepted'  => 'Принят',
                    'completed' => 'Завершён',
                    'cancelled' => 'Отменён',
                    'unknown'   => 'Неизвестно',
                ],
            ],

            'notification' => [
                'title' => '📦 Обновление статуса заказа',

                'accepted'  => '✅ Ваш заказ на :products принят.',
                'completed' => '🎉 Ваш заказ на :products успешно завершён.',
                'cancelled' => '❌ Ваш заказ на :products отменён.',
                'default'   => 'Статус вашего заказа на :products обновлён.',
            ],
        ],

        'product' => [

            'copy' => [
                'only_approved' => 'Копировать можно только одобренные товары.',
                'success' => 'Товар успешно скопирован и обновлён.',
            ],

            'create' => [
                'unauthorized' => 'Аккаунт продавца не подтверждён или пользователь не авторизован.',
                'invalid_store' => 'Неверный или неутверждённый магазин.',
                'child_category_invalid' => 'Дочерняя категория не относится к выбранной подкатегории.',

                'success' => 'Товар успешно добавлен.',

                'validation' => [
                    'store_required' => 'Пожалуйста, выберите магазин.',
                    'store_exists'   => 'Выбранный магазин не существует.',

                    'name_required'  => 'Название товара обязательно.',

                    'price_required' => 'Цена товара обязательна.',
                    'price_numeric'  => 'Цена должна быть числом.',

                    'discount_numeric' => 'Цена со скидкой должна быть числом.',

                    'quantity_required' => 'Количество обязательно.',
                    'quantity_integer'  => 'Количество должно быть целым числом.',

                    'images_required' => 'Необходимо добавить хотя бы одно изображение.',
                    'images_array'    => 'Изображения должны быть переданы в виде массива.',
                    'images_mimes'    => 'Каждое изображение должно быть в формате JPEG, JPG, PNG или GIF.',
                ],
            ],

            'list' => [
                'unauthorized' => 'Аккаунт продавца не подтверждён или пользователь не авторизован.',
                'success'      => 'Товары успешно получены.',
                'empty'        => 'Товары не найдены.',
            ],

            'details' => [
                'unauthorized' => 'Аккаунт продавца не подтверждён или пользователь не авторизован.',
                'not_found'    => 'Товар не найден.',
                'success'      => 'Детали товара успешно получены.',
            ],

            'update' => [
                'unauthorized' => 'Продавец не авторизован.',
                'not_found'    => 'Товар не найден.',
                'success'      => 'Товар успешно обновлён.',
            ],
        ],


       'combination' => [
            'combination_updated' => 'Комбинация успешно обновлена.',
            'combination_deleted' => 'Комбинация успешно удалена.',
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

            'unauthorized' => 'Доступ запрещён.',

            'validation' => [
                'title_required'    => 'Заголовок обязателен.',
                'review_required'   => 'Отзыв обязателен.',
                'rating_required'   => 'Оценка обязательна.',
                'username_required' => 'Имя пользователя обязательно.',
            ],

            'promotion' => [
                'not_approved' => 'Продвижение не одобрено.',
            ],

            'limit' => [
                'reached' => 'Достигнут лимит отзывов.',
            ],

            'store' => [
                'success' => 'Отзыв успешно отправлен.',
            ],

        ],

        'store' => [

            // ================= AUTH =================
            'auth' => [
                'not_vendor'   => 'Вы не являетесь продавцом.',
                'not_approved' => 'Ваш аккаунт продавца ещё не подтверждён. Пожалуйста, дождитесь одобрения администратора.',
                'unauthorized' => 'Пользователь не авторизован.',
                'not_found'    => 'Магазин не найден.',
                'not_owner'    => 'У вас нет прав доступа к этому магазину.',
            ],

            'create' => [
                'success' => 'Магазин успешно создан. Ожидайте подтверждения администратора.',
            ],

            'list' => [
                'success' => 'Магазин успешно получен.',
            ],

            'details' => [
                'success' => 'Данные магазина успешно получены.',
            ],

            'update' => [
                'success' => 'Магазин успешно обновлён. Ожидайте подтверждения администратора.',
            ],

            // ================= VALIDATION =================
            'validation' => [

                'name_required'    => 'Название магазина обязательно.',
                'mobile_required'  => 'Номер телефона магазина обязателен.',
                'email_required'   => 'Email магазина обязателен.',
                'email_invalid'    => 'Email должен быть корректным.',
                'country_required' => 'Страна обязательна.',
                'city_required'    => 'Город обязателен.',
                'address_required' => 'Полный адрес обязателен.',

                'type_required' => 'Тип магазина обязателен.',
                'type_array'    => 'Тип магазина должен быть массивом.',
                'type_invalid'  => 'Тип магазина должен быть одним из: Retail, Online, Wholesale, Offline.',

                'logo_image' => 'Логотип должен быть изображением.',
                'logo_mimes' => 'Логотип должен быть в формате jpeg, png, jpg, gif или webp.',
                'logo_max'   => 'Размер логотипа не должен превышать 2MB.',

                'background_image' => 'Фон магазина должен быть изображением.',
                'background_mimes' => 'Фон должен быть в формате jpeg, png, jpg, gif или webp.',
                'background_max'   => 'Размер фонового изображения не должен превышать 4MB.',

                'document_array' => 'Документы должны быть массивом.',
                'document_mimes' => 'Документы должны быть в формате jpg, png или pdf.',
                'document_max'   => 'Каждый документ не должен превышать 4MB.',

                'social_invalid' => 'Неверный формат ссылки.',
                'color_string'   => 'Цвет фона должен быть текстом.',
            ],

            // ================= VIDEO =================
            'video' => [

                'auth' => [
                    'unauthorized' => 'Пользователь не авторизован.',
                    'invalid_store' => 'Магазин не найден или не принадлежит пользователю.',
                ],

                'plans' => [
                    'success' => 'Планы успешно получены.',
                    'with_status_success' => 'Планы со статусом успешно получены.',
                ],

                'request' => [
                    'success' => 'Запрос отправлен администратору.',
                    'duplicate' => 'У вас уже есть ожидающий запрос для этого плана.',
                    'invalid_store' => 'Неверный магазин или не принадлежит пользователю.',
                ],

                'upload' => [
                    'unauthorized'      => 'Пользователь не авторизован.',
                    'permission_denied' => 'У вас нет прав для загрузки видео в этот магазин.',
                    'no_plan'           => 'Нет одобренного видеоплана. Пожалуйста, сначала приобретите и получите одобрение.',
                    'expired'           => 'Ваш предыдущий видеоплан истёк. Пожалуйста, продлите его.',
                    'success'           => 'Видео успешно загружено.',
                    'failed'            => 'Ошибка загрузки.',
                    'missing_chunk'     => 'Отсутствует часть с индексом :index',
                ],

                // ================= VALIDATION =================
                'validation' => [

                    // store
                    'store_required' => 'ID магазина обязателен.',
                    'store_exists'   => 'Магазин не найден.',

                    // plan
                    'plan_required' => 'Пожалуйста, выберите план.',
                    'plan_exists'   => 'Выбранный план недействителен.',

                    // payment
                    'payment_required' => 'Скриншот оплаты обязателен.',
                    'payment_image'    => 'Файл должен быть изображением.',
                    'payment_mimes'    => 'Разрешены только JPG, JPEG, PNG.',
                    'payment_max'      => 'Размер изображения должен быть меньше 2MB.',

                    // chunk upload
                    'chunk_required' => 'Часть видео (chunk) обязательна.',
                    'chunk_file'     => 'Неверный файл chunk.',
                    'chunk_mimes'    => 'Разрешены только MP4, MOV, AVI.',

                    'chunk_index_required' => 'Индекс chunk обязателен.',
                    'chunk_index_integer'  => 'Индекс chunk должен быть числом.',
                    'chunk_index_min'      => 'Индекс chunk должен быть 0 или больше.',

                    'total_chunks_required' => 'Общее количество chunk обязательно.',
                    'total_chunks_integer'  => 'Количество chunk должно быть числом.',
                    'total_chunks_min'      => 'Количество chunk должно быть не менее 1.',

                    'upload_id_required' => 'Upload ID обязателен.',
                ],

                // ================= LOGIC ERRORS =================
                'error' => [
                    'store_not_found' => 'Магазин не найден или не принадлежит пользователю.',
                    'plan_invalid'    => 'Неверная конфигурация плана.',
                    'no_approved_plan'=> 'Нет одобренного видеоплана. Пожалуйста, сначала приобретите и получите одобрение.',
                    'expired_plan'    => 'Ваш предыдущий видеоплан истёк. Пожалуйста, продлите его.',
                ],

            ],
        ],

        'bank' => [

            // ================= AUTH =================
            'auth' => [
                'unauthorized' => 'Продавец не авторизован.',
            ],

            // ================= SUCCESS =================
            'success' => [
                'updated' => 'Способы оплаты успешно обновлены.',
                'fetched' => 'Данные оплаты успешно получены.',
            ],

            // ================= VALIDATION =================
            'validation' => [

                'payment_modes_required' => 'Способ оплаты обязателен.',
                'payment_modes_array'    => 'Способ оплаты должен быть массивом.',
                'payment_modes_invalid'  => 'Способ оплаты должен быть COD или Bank.',

                'banks_required' => 'Банковские данные обязательны, если выбран способ Bank.',

                'bank_id_required' => 'ID банка обязателен.',
                'bank_id_exists'   => 'Выбранный банк не существует.',

                'account_holder_required' => 'Имя владельца счета обязательно.',
                'account_number_required' => 'Номер счета обязателен.',
            ],
        ],






    ],

    'customer' => [


        'login' => [

            'vendor_exists' => 'Этот номер уже зарегистрирован как продавец.',
            'blocked'       => 'Ваш аккаунт заблокирован',
            'deleted'       => 'Ваш аккаунт удалён.',
            'otp_sent'      => 'Код подтверждения отправлен',

            'validation' => [
                'phone_required' => 'Номер телефона обязателен',
                'phone_invalid'  => 'Неверный номер телефона.',
            ],

        ],


        'sms' => [
            'otp' => 'Ваш код подтверждения для входа в inBozor: :otp',
        ],

        'verify_otp' => [

            'invalid' => 'Неверный код.',
            'expired' => 'Срок действия кода истёк',
            'success' => 'Код успешно подтверждён',

            'validation' => [
                'phone_required' => 'Номер телефона обязателен.',
                'otp_required'   => 'Код обязателен.',
                'otp_digits'     => 'Неверный OTP.',
            ],

        ],

        'update_profile' => [

            'unauthenticated' => 'Пользователь не авторизован.',
            'success'         => 'Профиль успешно обновлен.',

            'validation' => [
                'name_string'        => 'Имя должно быть строкой.',
                'name_max'           => 'Имя не должно превышать 255 символов.',
                'email_invalid'      => 'Введите корректный email.',
                'email_unique'       => 'Этот email уже используется.',
                'mobile_unique'      => 'Этот номер уже используется.',
                'alt_mobile_string'  => 'Дополнительный номер должен быть строкой.',
                'country_string'     => 'Страна должна быть строкой.',
                'city_string'        => 'Город должен быть строкой.',
                'photo_image'        => 'Фото должно быть изображением.',
                'photo_mimes'        => 'Фото должно быть jpeg, png, jpg или gif.',
                'photo_max'          => 'Фото не должно превышать 2MB.',
            ],

        ],


        'address' => [

            'saved' => 'Адрес успешно сохранён',
            'list_success' => 'Список адресов успешно получен.',
            'deleted'   => 'Address removed successfully.',
            'not_found' => 'Адрес удалён.',

            'not_belongs'      => 'Этот адрес не принадлежит вашему аккаунту.',
            'already_default'  => 'Этот адрес уже установлен по умолчанию',
            'default_updated'  => 'Адрес по умолчанию обновлён',

            'validation' => [
                'name_required'      => 'Выберите тип адреса (дом / офис)',
                'full_name_required' => 'ФИО обязательно',
                'mobile_required'    => 'Номер телефона обязателен',
                'mobile_digits'      => 'Номер должен состоять из 10 цифр',
                'address_required'   => 'Поле адреса не может быть пустым',
                'city_required'      => 'Введите город',
                'state_required'     => 'Введите область',
                'pincode_required'   => 'Введите почтовый индекс',
                'pincode_digits'     => 'Индекс должен состоять из 6 цифр',

                'address_required' => 'Пожалуйста, выберите адрес',
                'address_exists'   => 'Выбранный адрес не существует',
            ],

        ],


        'cart' => [
            'unauthorized' => 'Доступ запрещён.',
            'product_not_available' => 'Товар недоступен.',
            'invalid_combination' => 'Неверная комбинация товара.',
            'insufficient_stock' => 'Недостаточно товара на складе.',
            'added_successfully' => 'Товар успешно добавлен в корзину.',

            'list_success' => 'Корзина успешно получена.',
            'empty' => 'Корзина пуста.',
            'updated' => 'Корзина успешно обновлена.',
            'removed' => 'Товар удалён из корзины.',
            'not_found' => 'Товар в корзине не найден.',

            'validation' => [
                'unauthorized' => 'Пользователь не авторизован.',
                'cart_id_required' => 'ID корзины обязателен.',
                'cart_id_invalid' => 'Неверный элемент корзины.',
                'quantity_required' => 'Количество обязательно.',
                'quantity_integer' => 'Количество должно быть числом.',
                'quantity_min' => 'Количество должно быть не меньше 1.',
            ],
        ],

        'category' => [

            'list_success' => 'Категории получены',
            'sub_list_success'    => 'Подкатегории получены',
            'child_list_success'  => 'Дочерние категории получены',

        ],


        'chat' => [

            'unauthorized' => 'User not authenticated.',
            'conversation_started' => 'Чат успешно создан',
            'conversation_list' => 'Список чатов получен',
            'messages_fetched' => 'Сообщения получены.',
            'message_sent' => 'Сообщение отправлено',

             'new_message_title' => 'Новое сообщение',
            'image_sent' => '📷 Отправил(а) изображение',
            'new_message' => 'Получено новое сообщение',

            'validation' => [
                'other_user_required' => 'Требуется ID другого пользователя',
                'other_user_invalid'  => 'Пользователь не найден',
                'self_chat'           => 'Вы не можете писать сами себе',
                'conversation_required'=> 'Требуется ID чата',
                'conversation_invalid' => 'Чат не найден',
                'message_required'     => 'Введите сообщение',
                'image_invalid'       => 'Неверный файл изображения',
            ],

            'not_participant' => 'Вы не участник этого чата',
            'not_found'       => 'Чат не найден',
        ],

        'order' => [

            // ✅ PLACE ORDER
            'place' => [

                // ✅ GENERAL
                'unauthorized' => 'Пользователь не авторизован.',
                'success'      => 'Заказ успешно оформлен',
                'failed'       => 'Failed to place order.',

                // ✅ CART
                'empty_cart' => 'Ваша корзина пуста.',
                'multi_store' => 'Нельзя оформлять заказ из разных магазинов',

                // ✅ ADDRESS
                'invalid_address' => 'Выбран неверный адрес',

                // ✅ PAYMENT
                'bank_not_supported' => 'Этот продавец не поддерживает оплату через банк',
                'bank_not_available' => 'Этот банк недоступен для данного продавца',

                // ✅ STOCK
                'stock' => [
                     'variant_out' => 'Выбранный вариант товара ":product" отсутствует на складе.',
                    'product_out' => 'Товара ":product" недостаточно на складе.',
                ],

                // ✅ VALIDATION
                'validation' => [
                    'address_required' => 'Для доставки требуется адрес.',
                    'address_invalid'  => 'Выбранный адрес недействителен.',
                    'payment_required' => 'Выберите способ оплаты',
                    'payment_invalid'  => 'Оплата должна быть наличными или онлайн',
                    'bank_required'    => 'Выберите банк для онлайн-оплаты',
                    'bank_invalid'     => 'Выбранный банк недействителен',
                ],

                // ✅ NOTIFICATION
                'notification' => [
                    'title' => '🛒 Новый заказ',
                    'body'  => ':user оформил заказ на :product',
                ],
            ],


            // ✅ ORDER LIST
            'list' => [
                'success' => 'Список заказов успешно получен.',
                'empty'   => 'Заказы не найдены.',
            ],

            'details' => [
                'not_found' => 'Заказ не найден.',
                'success'   => 'Детали заказа успешно получены.',
            ],

            // ✅ STATUS TEXTS
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
                'success' => 'Товары получены',
                'empty'   =>  'Товары недоступны.',
            ],

            'details' => [
                'success'   => 'Информация о товаре получена',
                'not_found' => 'Товары не найдены',
            ],

            'search' => [
                'success' => 'Результаты поиска получены',
                'empty'   => 'Товары не найдены',
            ],
        ],

        'review' => [

            'unauthorized' => 'Пользователь не авторизован',

            'submitted' => 'Отзыв отправлен.',
            'validation_error' => 'Validation Error',

            'not_found' => 'Товар не найден.',

            'list_success' => 'Отзывы успешно получены.',

            'validation' => [
                'product_required' => 'ID товара обязателен.',
                'product_exists'   => 'Выбранный товар не существует.',
                'rating_required'  => 'Оценка обязательна.',
                'rating_integer'   => 'Оценка должна быть числом.',
                'rating_min'       => 'Оценка должна быть не менее 1 звезды.',
                'rating_max'       => 'Оценка не может превышать 5 звёзд.',
                'review_string'    => 'Отзыв должен быть корректным текстом.',
                'review_max'       => 'Отзыв не должен превышать 1000 символов.',
                'image_invalid'    => 'Каждый файл должен быть изображением.',
                'image_mimes'      => 'Изображения должны быть в формате JPG, JPEG или PNG.',
                'image_max'        => 'Размер каждого изображения не должен превышать 2MB.',
            ],

        ],

        'store' => [

            'list' => [
                'success' => 'Магазины получены',
                'empty'   => 'Магазин не найден',
            ],

            'details' => [
                'success' => 'Данные магазина успешно получены.',
                'not_found' => 'Магазин не найден.',
                'no_products' => 'Для этого магазина товары не найдены.',
            ],

        ],

        



        

    ],




];
