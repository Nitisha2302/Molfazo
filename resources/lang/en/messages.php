<?php

return [

    'language' => [
        'updated' => 'Language updated successfully.',
        'validation' => [
            'required' => 'Please select a language.',
            'in' => 'Invalid language selected.',
        ],
    ],

    
    'logout' => [
        'logout_success' => 'Logout successful..',
         'user_not_authenticated' => 'User not authenticated.',
    ],

    'getProfile' => [
        'success' => 'Profile fetched successfully.',
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

            'user_not_authenticated' => 'User not authenticated.',
            'mobile_not_verified' => 'Please verify mobile number.',
            'register_success' => 'Vendor registered successfully. Waiting for admin approval.',

            'validation' => [
                'name_required'       => 'Full name is required.',
                'email_required'     => 'Email address is required.',
                'email_unique'       => 'This email is already registered.',
                'mobile_required'    => 'Mobile number is required.',
                'mobile_unique'      => 'This mobile number is already registered.',
                'password_confirmed' => 'Password and confirm password do not match.',
                'terms_required'     => 'You must accept terms & conditions.',
            ],
        ],

        'send_otp' => [

            'mobile_exists' => 'This mobile number already exists.',
            'otp_sent'      => 'OTP sent to your mobile number.',
            'otp_test'      => 'Auto OTP generated (testing mode)',

            'validation' => [
                'mobile_required' => 'Mobile number is required.',
                'mobile_invalid'  => 'Invalid mobile number.',
            ],

            'sms' => [
                'otp_message' => 'Your verification code is :otp for login to inBozor.',
            ],

        ],

        
        'verify_otp' => [

            'invalid_otp'   => 'Invalid OTP.',
            'otp_expired'   => 'OTP expired.',
            'otp_verified'  => 'OTP verified successfully.',

            'validation' => [
                'otp_required'   => 'OTP is required.',
                'otp_digits'     => 'OTP must be 6 digits.',
                'mobile_invalid' => 'Invalid mobile number.',
                'email_invalid'  => 'Invalid email address.',
            ],

        ],


        'login' => [

            'account_not_found' => 'Account not found. Please register first.',
            'invalid_credentials' => 'Invalid login credentials.',
            'only_vendor' => 'Only vendor accounts are allowed to login here.',
            'rejected' => 'Your vendor account has been rejected.',
            'blocked' => 'Your vendor account has been blocked. Contact support.',
            'login_success' => 'Login successful.',

            'validation' => [
                'login_required'    => 'Email or mobile is required.',
                'password_required' => 'Password is required.',
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
                'otp_message' => 'Your verification code is :otp for login to inBozor',
            ],

        ],


        'verify_login_otp' => [
            'invalid' => 'Invalid OTP or phone number.',
            'expired' => 'OTP expired. Please request a new one.',
            'login_success' => 'Login successful.',

            'validation' => [
                'mobile_required' => 'Mobile number is required.',
                'mobile_invalid'  => 'Invalid mobile number.',
                'otp_required'    => 'OTP is required.',
                'otp_digits'      => 'OTP must be 6 digits.',
            ],
        ],


        'forgot_password' => [

            'success' => 'New password has been sent to your email address.',
            'email_failed' => 'Failed to send email. Please try again later.',

            'validation' => [
                'email_required' => 'Email is required.',
                'email_invalid'  => 'Enter a valid email address.',
                'email_exists'   => 'Email not registered.',
            ],

        ],


        'reset_password' => [

            'invalid_password' => 'Password does not match the sent password.',
            'expired'          => 'Password expired, please request again.',
            'success'          => 'Password updated successfully.',

            'validation' => [
                'email_required'    => 'Email is required.',
                'email_invalid'     => 'Enter valid email.',
                'email_exists'      => 'Email not found.',

                'password_required'  => 'Password is required.',
                'password_digits'    => 'Password must be 6 digits.',
                'password_confirmed' => 'Password and confirm password must match.',
            ],

        ],


        'apple_login' => [

            'invalid_token' => 'Invalid Apple token.',
            'blocked'       => 'Account blocked by admin.',
            'success'       => 'Apple login successful.',
            'failed'        => 'Apple authentication failed.',

            'validation' => [
                'identity_token_required' => 'Identity token is required.',
                'apple_id_required'       => 'Apple ID is required.',
                'email_invalid'           => 'Enter a valid email address.',
                'name_string'             => 'Name must be a string.',
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

            'vendor_exists' => 'This number is already registered as a Vendor.',
            'blocked'       => 'Your account is blocked.',
            'deleted'       => 'Your account is deleted.',
            'otp_sent'      => 'OTP sent successfully.',

            'validation' => [
                'phone_required' => 'Mobile number is required.',
                'phone_invalid'  => 'Invalid mobile number.',
            ],

        ],

        'sms' => [
            'otp' => 'Your verification code is :otp for login to inBozor',
        ],

        'verify_otp' => [

            'invalid' => 'Invalid OTP.',
            'expired' => 'OTP expired.',
            'success' => 'OTP verified successfully.',

            'validation' => [
                'phone_required' => 'Mobile number is required.',
                'otp_required'   => 'OTP is required.',
                'otp_digits'     => 'Invalid OTP.',
            ],

        ],

        'update_profile' => [

            'unauthenticated' => 'User not authenticated.',
            'success'         => 'Profile updated successfully.',

            'validation' => [
                'name_string'        => 'Name must be a valid string.',
                'name_max'           => 'Name cannot exceed 255 characters.',
                'email_invalid'      => 'Please enter a valid email address.',
                'email_unique'       => 'This email is already taken.',
                'mobile_unique'      => 'This mobile number is already taken.',
                'alt_mobile_string'  => 'Alternate mobile must be a valid string.',
                'country_string'     => 'Country must be a valid string.',
                'city_string'        => 'City must be a valid string.',
                'photo_image'        => 'Profile photo must be an image file.',
                'photo_mimes'        => 'Profile photo must be jpeg, png, jpg, or gif.',
                'photo_max'          => 'Profile photo cannot exceed 2MB.',
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

        //  CART ADDED HERE
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
