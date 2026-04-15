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

        // ✅ CART ADDED HERE
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
