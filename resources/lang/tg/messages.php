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
            'otp' => 'Рамзи тасдиқи шумо барои ворид шудан ба inBozor: :otp',
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
