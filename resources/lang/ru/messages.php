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
                'success' => 'Category successfully fetched.',
                'empty'   => 'No categories found.',
            ],

            'subcategory' => [
                'success' => 'Sub categories fetched successfully.',
                'empty'   => 'No subcategories found.',
            ],

            'child_category' => [
                'success' => 'Child categories fetched successfully.',
                'empty'   => 'No child categories found.',
            ],

            'attributes' => [
                'success' => 'Attributes fetched successfully.',
                'empty'   => 'No attributes found for this category.',
            ],
        ],

        'banner' => [
            'success' => 'Banners fetched successfully',
            'empty_city' => 'No banners found for this city',
        ],

        'city' => [
            'success' => 'Cities fetched successfully',
        ],

        'chat' => [

            'list' => [
                'success' => 'Chats fetched successfully.',
                'empty'   => 'No chats found.',
            ],

            'send' => [
                'success' => 'Message sent successfully.',
            ],

            'validation' => [
                'receiver_required' => 'Receiver is required.',
                'receiver_exists'   => 'Selected user does not exist.',
                'message_string'    => 'Message must be valid text.',
            ],

            'unauthorized' => 'User not authenticated.',
        ],

        'notification' => [
            'delete' => [
                'success' => 'Deleted successfully',
                'not_found' => 'Notification not found',
                'unauthorized' => 'User not authenticated.',
            ],
        ],

        'order' => [

            'unauthorized' => 'Vendor account not approved or unauthenticated.',

            'list' => [
                'success' => 'Orders fetched successfully.',
                'empty'   => 'No orders found.',
            ],

            'details' => [
                'not_found' => 'Order not found.',
            ],

            'update_status' => [

                'success' => 'Order status updated successfully to :status.',

                'already_completed' => 'Order is already completed. Status cannot be changed.',
                'already_cancelled' => 'Order is already cancelled. Status cannot be changed.',
                'accept_first'      => 'Order must be accepted before completing.',

                'validation' => [
                    'status_required' => 'Status is required.',
                    'status_invalid'  => 'Invalid status. Allowed values: 2=Accepted, 3=Completed, 4=Cancelled.',
                ],

                'status_text' => [
                    'accepted'  => 'Accepted',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                    'unknown'   => 'Unknown',
                ],
            ],

            'notification' => [
                'title' => '📦 Order Status Update',

                'accepted'  => '✅ Your order for :products has been accepted.',
                'completed' => '🎉 Your order for :products has been completed successfully.',
                'cancelled' => '❌ Your order for :products has been cancelled.',
                'default'   => 'Your order for :products status updated.',
            ],
        ],

         'product' => [

            'copy' => [
                 'only_approved' => 'Only approved products can be copied.',
                'success' => 'Product copied & updated successfully.',
            ],

            /* ===============================
            CREATE PRODUCT
            =============================== */
            'create' => [

                'unauthorized' => 'Vendor account is not approved or authenticated.',
                'invalid_store' => 'Invalid or unapproved store.',
                'child_category_invalid' => 'Child category does not belong to selected sub-category.',

                'success' => 'Product added successfully.',

                'validation' => [
                    'store_required' => 'Please select a store.',
                    'store_exists'   => 'The selected store does not exist.',

                    'name_required'  => 'Product name is required.',

                    'price_required' => 'Product price is required.',
                    'price_numeric'  => 'Price must be a valid number.',

                    'discount_numeric' => 'Discount price must be a valid number.',

                    'quantity_required' => 'Available quantity is required.',
                    'quantity_integer'  => 'Available quantity must be an integer.',

                    'images_required' => 'At least one image is required.',
                    'images_array'    => 'Images must be sent as an array.',
                    'images_mimes'    => 'Each image must be jpeg, jpg, png, or gif.',
                ],
            ],

            /* ===============================
            LIST PRODUCTS
            =============================== */
            'list' => [

                'unauthorized' => 'Vendor account is not approved or authenticated.',
                'success'      => 'Products fetched successfully.',
                'empty'        => 'No products found.',
            ],

            'details' => [

                'unauthorized' => 'Vendor account is not approved or authenticated.',
                'not_found'    => 'Product not found.',
                'success'      => 'Product details fetched successfully.',

            ],


            'update' => [

                'unauthorized' => 'Vendor not authenticated.',
                'not_found' => 'Product not found.',
                'success' => 'Product updated successfully.',

            ],

        ],

        'bank' => [
            'list_success' => 'Bank list fetched successfully.',
        ],

        'combination' => [
            'combination_upadted' => 'Combination updated successfully.',
            'combination_delete' => 'Combination deleted successfully.',
        ],

        'promotion' => [

            'unauthorized' => 'Unauthorized',

            'packages' => [
                'success' => 'Packages fetched successfully',
                'with_status_success' => 'Packages with status fetched successfully',
            ],

            'payment' => [
                'success' => 'Payment details fetched successfully',
            ],

            'store' => [
                'success' => 'Promotion request submitted successfully',
                'duplicate' => 'Request already pending for this product',
            ],

            'validation' => [
                'product_required' => 'Product id is required',
                'package_required' => 'Package id is required',
                'image_required' => 'Payment screenshot is required',
            ],

        ],

        'review' => [

            'unauthorized' => 'Unauthorized',

            'validation' => [
                'title_required' => 'Title is required',
                'review_required' => 'Review is required',
                'rating_required' => 'Rating is required',
                'username_required' => 'Username is required',
            ],

            'promotion' => [
                'not_approved' => 'Promotion not approved',
            ],

            'limit' => [
                'reached' => 'Review limit reached',
            ],

            'store' => [
                'success' => 'Review submitted successfully',
            ],

        ],

        'store' => [

            // ================= AUTH =================
            'auth' => [
                'not_vendor' => 'You are not a vendor.',
                'not_approved' => 'Your vendor account is not approved yet. Please wait for admin approval.',
                'unauthorized' => 'User is not authenticated.',
                'not_found' => 'Store not found.',
                'not_owner' => 'You do not have permission to access this store.',
            ],

            // ================= CREATE =================
            'create' => [
                'success' => 'Store created successfully. Waiting for admin approval.',
            ],

            // ================= LIST =================
            'list' => [
                'success' => 'Store fetched successfully.',
            ],

            // ================= DETAILS =================
            'details' => [
                'success' => 'Store details fetched successfully.',
            ],

            // ================= UPDATE =================
            'update' => [
                'success' => 'Store updated successfully. Waiting for admin approval.',
            ],

            // ================= VALIDATION =================
            'validation' => [

                'name_required' => 'Store Name is required.',
                'mobile_required' => 'Store Mobile Number is required.',
                'email_required' => 'Store Email Address is required.',
                'email_invalid' => 'Store Email must be a valid email address.',
                'country_required' => 'Country is required.',
                'city_required' => 'City is required.',
                'address_required' => 'Complete Address is required.',

                'type_required' => 'Store Type is required.',
                'type_array' => 'Store Type must be an array.',
                'type_invalid' => 'Store Type must be one of: Retail, Online, Wholesale, Offline.',

                'logo_image' => 'Logo must be an image file.',
                'logo_mimes' => 'Logo must be jpeg, png, jpg, gif, or webp.',
                'logo_max' => 'Logo size cannot exceed 2MB.',

                'background_image' => 'Store background must be an image file.',
                'background_mimes' => 'Store background must be jpeg, png, jpg, gif, or webp.',
                'background_max' => 'Store background image size cannot exceed 4MB.',

                'document_array' => 'Documents must be an array.',
                'document_mimes' => 'Documents must be jpg, png or pdf.',
                'document_max' => 'Each document must not exceed 4MB.',

                'social_invalid' => 'Invalid social link format.',
                'color_string' => 'Background color must be text.',
            ],

            // ================= VIDEO =================
            'video' => [

                // ================= AUTH =================
                'auth' => [
                    'unauthorized' => 'Unauthorized user.',
                    'invalid_store' => 'Store not found or not owned by user.',
                ],

                // ================= PLANS =================
                'plans' => [
                    'success' => 'Plans fetched successfully.',
                    'with_status_success' => 'Plans with status fetched successfully.',
                ],

                // ================= VIDEO REQUEST =================
                'request' => [
                    'success' => 'Request sent to admin.',
                    'duplicate' => 'You already have a pending request for this plan.',
                    'invalid_store' => 'Invalid store or not owned by user.',
                ],

                // ================= UPLOAD =================
                'upload' => [
                    'unauthorized' => 'Unauthorized user.',
                    'permission_denied' => 'You do not have permission to upload video for this store.',
                    'no_plan' => 'No approved video plan found. Please purchase and get approval first.',
                    'expired' => 'Your previous video plan has expired. Please renew your plan.',
                    'success' => 'Video uploaded successfully.',
                    'failed' => 'Upload failed.',
                    'missing_chunk' => 'Missing chunk at index :index',
                ],

                // ================= VALIDATION =================
                'validation' => [

                    // store
                    'store_required' => 'Store ID is required.',
                    'store_exists' => 'Store not found.',

                    // plan
                    'plan_required' => 'Please select a plan.',
                    'plan_exists' => 'Selected plan is invalid.',

                    // payment
                    'payment_required' => 'Payment screenshot is required.',
                    'payment_image' => 'File must be an image.',
                    'payment_mimes' => 'Only JPG, JPEG, PNG allowed.',
                    'payment_max' => 'Image size must be less than 2MB.',

                    // chunk upload
                    'chunk_required' => 'Video chunk is required.',
                    'chunk_file' => 'Invalid chunk file.',
                    'chunk_mimes' => 'Only MP4, MOV, AVI formats allowed.',

                    'chunk_index_required' => 'Chunk index is required.',
                    'chunk_index_integer' => 'Chunk index must be a number.',
                    'chunk_index_min' => 'Chunk index must be 0 or greater.',

                    'total_chunks_required' => 'Total chunks is required.',
                    'total_chunks_integer' => 'Total chunks must be a number.',
                    'total_chunks_min' => 'Total chunks must be at least 1.',

                    'upload_id_required' => 'Upload ID is required.',
                ],

                // ================= LOGIC ERRORS =================
                'error' => [
                    'store_not_found' => 'Store not found or not owned by user.',
                    'plan_invalid' => 'Invalid plan configuration.',
                    'no_approved_plan' => 'No approved video plan found. Please purchase and get approval first.',
                    'expired_plan' => 'Your previous video plan has expired. Please renew your plan.',
                ],
            ],
        ],

        'bank' => [

            // ================= AUTH =================
            'auth' => [
                'unauthorized' => 'Unauthorized vendor.',
            ],

            // ================= SUCCESS =================
            'success' => [
                'updated' => 'Payment modes updated successfully.',
                'fetched' => 'Payment details fetched successfully.',
            ],

            // ================= VALIDATION =================
            'validation' => [

                'payment_modes_required' => 'Payment mode is required.',
                'payment_modes_array' => 'Payment mode must be an array.',
                'payment_modes_invalid' => 'Payment mode must be COD or Bank.',

                'banks_required' => 'Bank details are required when payment mode includes Bank.',

                'bank_id_required' => 'Bank ID is required.',
                'bank_id_exists' => 'Selected bank does not exist.',

                'account_holder_required' => 'Account holder name is required.',
                'account_number_required' => 'Account number is required.',
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
