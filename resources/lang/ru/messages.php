<?php

return [

    'language' => [
        'updated' => 'Забон бо муваффақият навсозӣ шуд.',
        'validation' => [
            'required' => 'Лутфан забонро интихоб кунед.',
            'in' => 'Забони интихобшуда нодуруст аст.',
        ],
    ],

    'logout' => [
        'logout_success' => 'LВы успешно вышли из системы..',
         'user_not_authenticated' => 'Вы не авторизованы. Пожалуйста, войдите в систему.',
    ],

    'getProfile' => [
        'success' => 'Профиль загружен.',
        'user_not_authenticated' => 'User not authenticated.',
    ],

    'enquiry' => [

        'unauthorized' => 'Unauthorized',

        'store' => [
            'success' => 'Query submitted successfully',

            'validation' => [
                'title_required'       => 'Please enter enquiry title.',
                'title_string'        => 'Title must be valid text.',
                'description_required'=> 'Please enter enquiry description.',
                'description_string'  => 'Description must be valid text.',
            ],
        ],

        'list' => [
            'success' => 'Query fetched successfully',
            'empty'   => 'No enquiries found',
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












    ],

    'customer' => [


        'login' => [

            'vendor_exists' => 'Этот номер уже зарегистрирован как продавец.',
            'blocked'       => 'Ваш аккаунт заблокирован.',
            'deleted'       => 'Ваш аккаунт удален.',
            'otp_sent'      => 'OTP успешно отправлен.',

            'validation' => [
                'phone_required' => 'Требуется номер телефона.',
                'phone_invalid'  => 'Неверный номер телефона.',
            ],

        ],


        'sms' => [
            'otp' => 'Ваш код подтверждения: :otp для входа в inBozor',
        ],

        'verify_otp' => [

            'invalid' => 'Неверный OTP.',
            'expired' => 'Срок OTP истек.',
            'success' => 'OTP успешно подтвержден.',

            'validation' => [
                'phone_required' => 'Требуется номер телефона.',
                'otp_required'   => 'Требуется OTP.',
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

                'address_required' => 'Please select an address.',
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

        'order' => [

            // ✅ PLACE ORDER
            'place' => [

                // ✅ GENERAL
                'unauthorized' => 'User not authenticated.',
                'success'      => 'Order placed successfully.',
                'failed'       => 'Failed to place order.',

                // ✅ CART
                'empty_cart' => 'Your cart is empty.',
                'multi_store' => 'Multiple store products not allowed in one order.',

                // ✅ ADDRESS
                'invalid_address' => 'Invalid address selected.',

                // ✅ PAYMENT
                'bank_not_supported' => 'This vendor does not support bank payment.',
                'bank_not_available' => 'Selected bank is not available for this vendor.',

                // ✅ STOCK
                'stock' => [
                    'variant_out' => 'Variant of :product is out of stock.',
                    'product_out' => 'Product :product does not have enough stock.',
                ],

                // ✅ VALIDATION
                'validation' => [
                    'address_required' => 'Delivery address is required for home delivery.',
                    'address_invalid'  => 'Selected delivery address is invalid.',
                    'payment_required' => 'Payment type is required.',
                    'payment_invalid'  => 'Payment type must be COD or Online.',
                    'bank_required'    => 'Please select a bank for online payment.',
                    'bank_invalid'     => 'Selected bank is invalid.',
                ],

                // ✅ NOTIFICATION
                'notification' => [
                    'title' => '🛒 New Order',
                    'body'  => ':user placed a new order for :product',
                ],

            ],


            // ✅ ORDER LIST
            'list' => [
                'success' => 'Orders fetched successfully.',
                'empty'   => 'No orders found.',
            ],

            // ✅ ORDER DETAILS
            'details' => [
                'not_found' => 'Order not found.',
                'success'   => 'Order details fetched successfully.',
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
                'success' => 'Products fetched successfully',
                'empty'   => 'No products available',
            ],

            'details' => [
                'success'   => 'Product details fetched successfully',
                'not_found' => 'Product not found',
            ],

            'search' => [
                'success' => 'Search results fetched successfully',
                'empty'   => 'No products found',
            ],
        ],

        'review' => [

            'unauthorized' => 'User not authenticated.',

            'submitted' => 'Review submitted successfully.',
            'validation_error' => 'Validation Error',

            'not_found' => 'Product not found.',

            'list_success' => 'Reviews fetched successfully.',

            'validation' => [
                'product_required' => 'Product ID is required.',
                'product_exists'   => 'The selected product does not exist.',
                'rating_required'  => 'Rating is required.',
                'rating_integer'   => 'Rating must be a number.',
                'rating_min'       => 'Rating must be at least 1 star.',
                'rating_max'       => 'Rating cannot be more than 5 stars.',
                'review_string'    => 'Review must be valid text.',
                'review_max'       => 'Review cannot exceed 1000 characters.',
                'image_invalid'    => 'Each file must be an image.',
                'image_mimes'      => 'Images must be jpg, jpeg or png.',
                'image_max'        => 'Each image must not exceed 2MB.',
            ],

        ],

        'store' => [

            'list' => [
                'success' => 'Stores fetched successfully',
                'empty'   => 'No stores found',
            ],

            'details' => [
                'success' => 'Store details fetched successfully',
                'not_found' => 'Store not found',
                'no_products' => 'No products found for this store',
            ],

        ],

        



        

    ],




];
