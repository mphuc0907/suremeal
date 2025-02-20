<?php
add_filter('wpcf7_spam', '__return_false');
function custom_menu_classes($classes, $item)
{
    // Kiểm tra nếu là trang blog hoặc single post của blog
    if ((is_post_type_archive('blog') || is_singular('blog')) && $item->title == 'Blog') {
        $classes[] = 'current-menu-item';
    }

    // Kiểm tra nếu là trang single product
    if ((is_post_type_archive('product') || is_singular('product')) && $item->title == 'Products') {
        $classes[] = 'current-menu-item';
    }

    return $classes;
}
add_filter('nav_menu_css_class', 'custom_menu_classes', 10, 2);
function generate_unique_token()
{
    return bin2hex(random_bytes(32));
}

function validate_user_token()
{
    global $wpdb;
    $table = $wpdb->prefix . 'account_users';

    // Check
    if (!isset($_COOKIE['user_token'])) {
        return false;
    }

    $user_token = sanitize_text_field($_COOKIE['user_token']);

    // Lay thong tin
    $user = $wpdb->get_row($wpdb->prepare(
        "SELECT email, phone, first_name, last_name, avatar, addresses,phone_number, password, provider, business_name, business_email, business_network, business_website, plan, bank_name, account_number, routing_number, holder_name, swift, iban, type, reason_for_knowing, ID FROM $table WHERE token = %s",
        $user_token
    )); 

    return $user ? $user : false;
}
function validate_dealer_token()
{
    global $wpdb;
    $table = $wpdb->prefix . 'account_dealers';

    // Check
    if (!isset($_COOKIE['dealer_token'])) {
        return false;
    }

    $dealer_token = sanitize_text_field($_COOKIE['dealer_token']);

    // Lay thong tin
    $dealer = $wpdb->get_row($wpdb->prepare(
        "SELECT email, phone, first_name, last_name, avatar, addresses, password, provider, business_name, business_email, business_network, business_website, plan, bank_name, account_number, routing_number, holder_name, swift, iban, status, reason_for_knowing, ID FROM $table WHERE token = %s",
        $dealer_token
    )); 

    return $dealer ? $dealer : false;
}

add_action('wp_ajax_logout', 'logout_user');
add_action('wp_ajax_nopriv_logout', 'logout_user');
function logout_user()
{
    global $wpdb;

    // Kiểm tra xem người dùng đã đăng nhập chưa
    $authenticated_user = validate_user_token();
    $authenticated_dealer = validate_dealer_token();

    if ($authenticated_user) {
        $table = $wpdb->prefix . 'account_users';

        // Xóa token trong database
        $wpdb->update(
            $table,
            ['token' => null],
            ['token' => $_COOKIE['user_token']],
            ['%s'],
            ['%s']
        );

        // Xóa cookie
        // setcookie('user_token', '', time() - 3600, '/', '', false, true);
        
        setcookie('user_token', '', time() - 3600, '/', '.' . $_SERVER['HTTP_HOST']);
    }

    if ($authenticated_dealer) {
        $table = $wpdb->prefix . 'account_dealers';

        // Xóa token trong database
        $wpdb->update(
            $table,
            ['token' => null],
            ['token' => $_COOKIE['dealer_token']],
            ['%s'],
            ['%s']
        );

        // Xóa cookie
        // setcookie('dealer_token', '', time() - 3600, '/', '', false, true);
        setcookie('dealer_token', '', time() - 3600, '/', '.' . $_SERVER['HTTP_HOST']);
    }

    // Chuyển hướng về trang chủ
    wp_redirect(home_url());
    exit;
}

// Xử lý đăng nhập Google
function google_signin_handler() {
    if (isset($_GET['code'])) {
        $is_dealer = (isset($_GET['state']) && $_GET['state'] === 'dealer');
        
        $client_id = '451202130918-qkndmpg1jorcqhugj630pnr0u7sueblb.apps.googleusercontent.com';
        $client_secret = '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';
        $redirect_uri = $is_dealer ? home_url('/sign-in-dealer') : home_url('/sign-in');

        $token_url = 'https://oauth2.googleapis.com/token';
        $people_api_url = 'https://people.googleapis.com/v1/people/me?personFields=names,emailAddresses,photos';

        // Lấy access token từ Google
        $response = wp_remote_post($token_url, array(
            'body' => array(
                'code' => $_GET['code'],
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'redirect_uri' => $redirect_uri,
                'grant_type' => 'authorization_code',
            ),
        ));

        $token_data = json_decode(wp_remote_retrieve_body($response), true);

        // Kiểm tra nếu không lấy được access token
        if (!isset($token_data['access_token'])) {
            wp_redirect($is_dealer ? 
                home_url('/sign-in-dealer?error=token_retrieval_failed') : 
                home_url('/sign-in?error=token_retrieval_failed'));
            exit;
        }

        $access_token = $token_data['access_token'];

        // Lấy thông tin người dùng từ Google API
        $response = wp_remote_get($people_api_url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $access_token,
            ),
        ));

        $user_data = json_decode(wp_remote_retrieve_body($response), true);

        // Kiểm tra nếu không lấy được dữ liệu người dùng
        if (empty($user_data) || !isset($user_data['names'], $user_data['emailAddresses'])) {
            wp_redirect($is_dealer ? 
                home_url('/sign-in-dealer?error=user_data_retrieval_failed') : 
                home_url('/sign-in?error=user_data_retrieval_failed'));
            exit;
        }

        // Lấy thông tin cần thiết từ API
        $first_name = $user_data['names'][0]['givenName'] ?? '';
        $last_name = $user_data['names'][0]['familyName'] ?? '';
        $email = $user_data['emailAddresses'][0]['value'] ?? '';
        $avatar = isset($user_data['photos'][0]['url']) ? $user_data['photos'][0]['url'] : '';
        $provider = 'google';
        $provider_id = $user_data['resourceName'] ?? '';

        if ($is_dealer) {
            // Xử lý cho dealer
            $dealer_id = save_google_account($first_name, $last_name, $email, $avatar, $provider, $provider_id, $access_token, 'dealers');
            
            if ($dealer_id === 'disabled') {
                wp_redirect(home_url('/sign-in-dealer?error=account_disabled'));
                exit;
            } else if ($dealer_id) {
                echo '<script>localStorage.removeItem("cart");</script>';
                echo '<html><head><meta http-equiv="refresh" content="0;url=' . home_url() . '"></head></html>';
                exit;
            } else {
                wp_redirect(home_url('/sign-in-dealer?error=save_failed'));
                exit;
            }
        } else {
            // Xử lý cho user thông thường
            $user_id = save_google_account($first_name, $last_name, $email, $avatar, $provider, $provider_id, $access_token, 'users');
            
            if ($user_id) {
                echo '<html><head><meta http-equiv="refresh" content="0;url=' . home_url() . '"></head></html>';
                exit;
            } else {
                wp_redirect(home_url('/sign-in?error=save_failed'));
                exit;
            }
        }
    }
}
add_action('init', 'google_signin_handler');

// Hàm chung để lưu tài khoản Google
function save_google_account($first_name, $last_name, $email, $avatar, $provider, $provider_id, $access_token, $account_type = 'users') {
    global $wpdb;
    $table_name = $wpdb->prefix . 'account_' . $account_type;
    $token_cookie_name = $account_type === 'dealers' ? 'dealer_token' : 'user_token';

    // Generate a unique token
    $token = generate_unique_token();

    // Check if account exists
    $existing = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE email = %s", $email));

    // Check status for dealers
    if ($account_type === 'dealers' && $existing && isset($existing->status) && $existing->status == 0) {
        echo '<script>alert("Account pending approval"); window.location.href="' . home_url('/sign-in-dealer') . '";</script>';
        exit;
    }

    if ($existing) {
        // Update existing account
        $update_data = array(
            'avatar' => empty($existing->avatar) ? $avatar : $existing->avatar,
            'provider' => $provider,
            'provider_id' => $provider_id,
            'provider_token' => $access_token, 
            'token' => $token
        );

        $wpdb->update(
            $table_name,
            $update_data,
            array('id' => $existing->id)
        );

        $account_id = $existing->id;
    } else {
        // Create new account
        $insert_data = array(
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'avatar' => $avatar,
            'provider' => $provider,
            'provider_id' => $provider_id,
            'provider_token' => $access_token,
            'token' => $token
        );

        $wpdb->insert($table_name, $insert_data);
        $account_id = $wpdb->insert_id;
        if($account_type === 'dealers'){
            echo '<script>alert("You have successfully registered as a dealer. Your registration request is pending approval by SureMeal."); window.location.href="' . home_url('/sign-in-dealer') . '";</script>';
            exit;
        }
    }

    if ($account_id) {
        // Set authentication cookie
        $expire = time() + (30 * 24 * 60 * 60); // 30 days
        setcookie($token_cookie_name, $token, $expire, '/', '.' . $_SERVER['HTTP_HOST']);
        if($account_type === 'dealers'){
            setcookie('user_token', '', time() - 3600, '/', '.' . $_SERVER['HTTP_HOST']);
        }elseif($account_type === 'users'){
            setcookie('dealer_token', '', time() - 3600, '/', '.' . $_SERVER['HTTP_HOST']);
        }
        
        return $account_id;
    }

    return false;
}

// Đăng nhập fb
function handle_facebook_login()
{
    // Verify NONCE and check if it's a Facebook login action
    check_ajax_referer('facebook_login_nonce', 'nonce');

    // Get Facebook user data from POST
    $facebook_id = sanitize_text_field($_POST['facebook_id']);
    $name = sanitize_text_field($_POST['first_name']);
    $email = !empty($_POST['email']) ? sanitize_email($_POST['email']) : null;
    $avatar = esc_url($_POST['avatar']);
    $access_token = sanitize_text_field($_POST['access_token']);

    // Prepare data to be saved
    global $wpdb;
    $table = $wpdb->prefix . 'account_users';

    // Check if user already exists
    $existing_user = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table WHERE provider = 'facebook' AND provider_id = %s",
        $facebook_id
    ));

    // Prepare user data update array
    $user_data_update = [];

    // Only update first_name if it's empty in the existing record
    if ($existing_user && empty($existing_user->first_name)) {
        $user_data_update['first_name'] = $name;
    }

    // Only update avatar if it's empty in the existing record
    if ($existing_user && empty($existing_user->avatar)) {
        $user_data_update['avatar'] = $avatar;
    }

    // Always update provider token and generate a new token
    $user_data_update['provider_token'] = $access_token;
    $user_data_update['token'] = generate_unique_token();

    if ($existing_user) {
        // Update existing user only if there are changes
        if (!empty($user_data_update)) {
            $result = $wpdb->update(
                $table,
                $user_data_update,
                ['id' => $existing_user->id],
                // Use appropriate format based on updated fields
                array_fill(0, count($user_data_update), '%s'),
                ['%d']
            );
        } else {
            // No updates needed, but still use existing user's token
            $result = true;
            $user_data_update['token'] = $existing_user->token;
        }
    } else {
        // Insert new user with available data
        $user_data_update['provider'] = 'facebook';
        $user_data_update['provider_id'] = $facebook_id;
        $user_data_update['first_name'] = $name;
        $user_data_update['avatar'] = $avatar;

        // Only add email if it exists
        if ($email) {
            $user_data_update['email'] = $email;
        }

        $result = $wpdb->insert(
            $table,
            $user_data_update,
            // Dynamically set format based on which fields are present
            array_map(function () {
                return '%s';
            }, $user_data_update)
        );
    }

    // Set user token cookie
    if ($result !== false) {
        $expire = time() + (30 * 24 * 60 * 60); // 30 days
        setcookie('user_token', $user_data_update['token'], $expire, '/', '.' . $_SERVER['HTTP_HOST']);
        setcookie('dealer_token', '', time() - 3600, '/', '.' . $_SERVER['HTTP_HOST']);

        wp_send_json_success(['redirect' => home_url()]);
    } else {
        wp_send_json_error(['message' => 'Failed to save user data']);
    }

    wp_die();
}
add_action('wp_ajax_facebook_login', 'handle_facebook_login');
add_action('wp_ajax_nopriv_facebook_login', 'handle_facebook_login');

function handle_facebook_login_dealer() {
    // Verify NONCE and check if it's a Facebook login action
    check_ajax_referer('facebook_login_dealer_nonce', 'nonce');
    
    // Get Facebook user data from POST
    $facebook_id = sanitize_text_field($_POST['facebook_id']);
    $name = sanitize_text_field($_POST['first_name']);
    $email = !empty($_POST['email']) ? sanitize_email($_POST['email']) : null;
    $avatar = esc_url($_POST['avatar']);
    $access_token = sanitize_text_field($_POST['access_token']);
    
    // Prepare data to be saved
    global $wpdb;
    $table = $wpdb->prefix . 'account_dealers';
    
    // Check if user already exists
    $existing_user = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table WHERE provider = 'facebook' AND provider_id = %s",
        $facebook_id
    ));
    
    // Prepare user data update array
    $user_data_update = [];
    
    // Only update first_name if it's empty in the existing record
    if ($existing_user && empty($existing_user->first_name)) {
        $user_data_update['first_name'] = $name;
    }
    
    // Only update avatar if it's empty in the existing record
    if ($existing_user && empty($existing_user->avatar)) {
        $user_data_update['avatar'] = $avatar;
    }
    
    // Always update provider token and generate a new token
    $user_data_update['provider_token'] = $access_token;
    $user_data_update['token'] = generate_unique_token();
    
    if ($existing_user) {
        // Check status for existing user
        if ($existing_user->status == 0) {
            echo '<script>alert("Account pending approval"); window.location.href="' . home_url('/sign-in-dealer') . '";</script>';
            exit;
        }
        
        // Update existing user only if there are changes and status is 1
        if (!empty($user_data_update)) {
            $result = $wpdb->update(
                $table,
                $user_data_update,
                ['id' => $existing_user->id],
                array_fill(0, count($user_data_update), '%s'),
                ['%d']
            );
        } else {
            // No updates needed, but still use existing user's token
            $result = true;
            $user_data_update['token'] = $existing_user->token;
        }
    } else {
        // Insert new user with available data and status = 0
        $user_data_update['provider'] = 'facebook';
        $user_data_update['provider_id'] = $facebook_id;
        $user_data_update['first_name'] = $name;
        $user_data_update['avatar'] = $avatar;
        $user_data_update['status'] = 0;  // Set initial status to pending
        
        // Only add email if it exists
        if ($email) {
            $user_data_update['email'] = $email;
        }
        
        $result = $wpdb->insert(
            $table,
            $user_data_update,
            array_map(function () {
                return '%s';
            }, $user_data_update)
        );
        
        if ($result !== false) {
            echo '<script>alert("You have successfully registered as a dealer. Your registration request is pending approval by SureMeal."); window.location.href="' . home_url('/sign-in-dealer') . '";</script>';
            exit;
        }
    }
    
    // Set user token cookie only if status is 1 and operation was successful
    if ($result !== false && ($existing_user && $existing_user->status == 1)) {
        $expire = time() + (30 * 24 * 60 * 60); // 30 days
        setcookie('dealer_token', $user_data_update['token'], $expire, '/', '.' . $_SERVER['HTTP_HOST']);
        setcookie('user_token', '', time() - 3600, '/', '.' . $_SERVER['HTTP_HOST']);
        
        wp_send_json_success(['redirect' => home_url()]);
    } else if ($result === false) {
        wp_send_json_error(['message' => 'Failed to save user data']);
    }
    
    wp_die();
}

add_action('wp_ajax_facebook_login_dealer', 'handle_facebook_login_dealer');
add_action('wp_ajax_nopriv_facebook_login_dealer', 'handle_facebook_login_dealer');

function add_custom_menu_classes($classes, $item, $args)
{
    // Thêm class chung
    $classes[] = 'px-3 2xl:px-6 py-2 2xl:py-3 hover:bg-white hover:text-secondary text-body-md-semibold text-white';

    // Thêm class dựa trên điều kiện
    if ($item->current) {
        $classes[] = 'active-menu-item';
    }
    return $classes;
}

add_filter('nav_menu_css_class', 'add_custom_menu_classes', 10, 3);

// Phân trang san phẩm
function product_rewrite_rules()
{
    add_rewrite_rule(
        'product/page/([0-9]+)/?$',
        'index.php?pagename=product&paged=$matches[1]',
        'top'
    );
}
add_action('init', 'product_rewrite_rules');

// Tìm kiếm faq
function ajax_faq_search()
{
    check_ajax_referer('faq_search_nonce', 'nonce');

    $search_term = sanitize_text_field($_POST['search_term']);
    $faq_type = sanitize_text_field($_POST['faq_type']);
    $page_id = intval($_POST['page_id']);

    $faqs = get_field($faq_type, $page_id);

    $results = [];
    $search_term_lower = mb_strtolower($search_term);

    if ($faqs) {
        foreach ($faqs as $faq) {
            $question_lower = mb_strtolower($faq['question']);
            $answer_lower = mb_strtolower($faq['answer']);

            if (
                mb_strpos($question_lower, $search_term_lower) !== false ||
                mb_strpos($answer_lower, $search_term_lower) !== false
            ) {
                // Tạo bản sao để highlight
                $highlighted_faq = $faq;

                // Highlight từ khóa trong câu hỏi
                $highlighted_faq['question'] = preg_replace(
                    "/(" . preg_quote($search_term, '/') . ")/i",
                    '<mark class="bg-yellow-200">$1</mark>',
                    $faq['question']
                );

                // Highlight từ khóa trong câu trả lời
                $highlighted_faq['answer'] = preg_replace(
                    "/(" . preg_quote($search_term, '/') . ")/i",
                    '<mark class="bg-yellow-200">$1</mark>',
                    $faq['answer']
                );

                $results[] = $highlighted_faq;
            }
        }
    }

    wp_send_json_success($results);
}

add_action('wp_ajax_faq_search', 'ajax_faq_search');
add_action('wp_ajax_nopriv_faq_search', 'ajax_faq_search');

// Reset kq tìm kiếm
function ajax_faq_reset()
{
    check_ajax_referer('faq_reset_nonce', 'nonce');

    $faq_type = sanitize_text_field($_POST['faq_type']);
    $page_id = intval($_POST['page_id']);

    $faqs = get_field($faq_type, $page_id);

    wp_send_json_success($faqs ?: []);
}

add_action('wp_ajax_faq_reset', 'ajax_faq_reset');
add_action('wp_ajax_nopriv_faq_reset', 'ajax_faq_reset');

add_action('wp_ajax_update_comment_status', 'update_comment_status_callback');
// Update status comment
function update_comment_status_callback()
{
    global $wpdb;

    // Kiểm tra quyền
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Bạn không có quyền thực hiện');
    }

    $comment_id = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : 0;
    $status = isset($_POST['status']) ? intval($_POST['status']) : 0;

    if ($comment_id <= 0) {
        wp_send_json_error('ID bình luận không hợp lệ');
    }

    // Cập nhật status trong database
    $result = $wpdb->update(
        'wp_review',
        ['status' => $status],
        ['id' => $comment_id],
        ['%d'],
        ['%d']
    );

    if ($result !== false) {
        wp_send_json_success('Cập nhật thành công');
    } else {
        wp_send_json_error('Không thể cập nhật');
    }
}

// Change password user
add_action('wp_ajax_reset_password', 'custom_reset_password');
add_action('wp_ajax_nopriv_reset_password', 'custom_reset_password');

function custom_reset_password()
{
    // Check nonce for security
    check_ajax_referer('reset_password_nonce', 'security');

    // Sanitize and validate input
    $token = sanitize_text_field($_POST['token']);
    $email = sanitize_email($_POST['email']);
    $new_password = $_POST['new_password'];

    // Validate input
    if (empty($token) || empty($email) || empty($new_password)) {
        wp_send_json_error(['message' => 'Invalid input parameters.']);
        exit;
    }

    global $wpdb;

    // Begin database transaction for reliability
    $wpdb->query('START TRANSACTION');

    try {
        // Verify the reset token
        $reset_request = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM wp_password_reset_tokens 
            WHERE token = %s 
            AND expiry > %d 
            AND email = %s",
            $token,
            time(),
            $email
        ));

        // Check if token is valid
        if (!$reset_request) {
            wp_send_json_error(['message' => 'Invalid or expired reset token.']);
            $wpdb->query('ROLLBACK');
            exit;
        }

        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password in wp_account_users table
        $update_result = $wpdb->update(
            'wp_account_users',
            ['password' => $hashed_password],
            ['email' => $email],
            ['%s'],
            ['%s']
        );

        // Check if password update was successful
        if ($update_result === false) {
            wp_send_json_error(['message' => 'Failed to update password.']);
            $wpdb->query('ROLLBACK');
            exit;
        }

        // Delete the used reset token
        $wpdb->delete(
            'wp_password_reset_tokens',
            ['token' => $token],
            ['%s']
        );

        // Commit the transaction
        $wpdb->query('COMMIT');

        // Send success response
        wp_send_json_success(['message' => 'Password successfully reset. Please log in with your new password.']);
        exit;
    } catch (Exception $e) {
        // Rollback in case of any error
        $wpdb->query('ROLLBACK');
        wp_send_json_error(['message' => 'An unexpected error occurred: ' . $e->getMessage()]);
        exit;
    }
}

// Change password dealer
add_action('wp_ajax_dealer_reset_password', 'custom_dealer_reset_password');
add_action('wp_ajax_nopriv_dealer_reset_password', 'custom_dealer_reset_password');

function custom_dealer_reset_password()
{
    // Check nonce for security
    check_ajax_referer('dealer_reset_password_nonce', 'security');

    // Sanitize and validate input
    $token = sanitize_text_field($_POST['token']);
    $email = sanitize_email($_POST['email']);
    $new_password = $_POST['new_password'];

    // Validate input
    if (empty($token) || empty($email) || empty($new_password)) {
        wp_send_json_error(['message' => 'Invalid input parameters.']);
        exit;
    }

    global $wpdb;

    // Begin database transaction for reliability
    $wpdb->query('START TRANSACTION');

    try {
        // Verify the reset token
        $reset_request = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM wp_dealer_password_reset_tokens 
            WHERE token = %s 
            AND expiry > %d 
            AND email = %s",
            $token,
            time(),
            $email
        ));

        // Check if token is valid
        if (!$reset_request) {
            wp_send_json_error(['message' => 'Invalid or expired reset token.']);
            $wpdb->query('ROLLBACK');
            exit;
        }

        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password in wp_account_users table
        $update_result = $wpdb->update(
            'wp_account_dealers',
            ['password' => $hashed_password],
            ['email' => $email],
            ['%s'],
            ['%s']
        );

        // Check if password update was successful
        if ($update_result === false) {
            wp_send_json_error(['message' => 'Failed to update password.']);
            $wpdb->query('ROLLBACK');
            exit;
        }

        // Delete the used reset token
        $wpdb->delete(
            'wp_dealer_password_reset_tokens',
            ['token' => $token],
            ['%s']
        );

        // Commit the transaction
        $wpdb->query('COMMIT');

        // Send success response
        wp_send_json_success(['message' => 'Password successfully reset. Please log in with your new password.']);
        exit;
    } catch (Exception $e) {
        // Rollback in case of any error
        $wpdb->query('ROLLBACK');
        wp_send_json_error(['message' => 'An unexpected error occurred: ' . $e->getMessage()]);
        exit;
    }
}

// validate
function validate_password_strength($password)
{
    // Minimum 6 characters
    if (strlen($password) < 6) {
        return false;
    }

    return true;
}

add_filter('wpcf7_validate_email*', 'validate_email_format', 20, 2);
function validate_email_format($result, $tag)
{
    $email = isset($_POST[$tag['name']]) ? trim($_POST[$tag['name']]) : '';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $result->invalidate($tag, "Please enter a valid email address.");
    }
    return $result;
}

add_filter('wpcf7_validate_text*', 'validate_phone_format', 20, 2);
function validate_phone_format($result, $tag)
{
    $name = $tag['name'];
    if ($name === 'your_phone') {
        $phone = isset($_POST[$name]) ? trim($_POST[$name]) : '';
        if (!preg_match('/^[0-9]{10,15}$/', $phone)) {
            $result->invalidate($tag, "Please enter a valid phone number.");
        }
    }
    return $result;
}

add_action('wp_ajax_nopriv_submitVoucher', 'submitVoucher');
add_action('wp_ajax_submitVoucher', 'submitVoucher');

function submitVoucher()
{
    global $wpdb;

    $voucher_code = $_POST['voucherCode'];
    $dataProduct = $_POST['dataProduct'];
    $subtotal = isset($_POST['subtotal']) ? floatval($_POST['subtotal']) : 0;

    $voucher = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_voucher WHERE voucher_code = %s", $voucher_code));

    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $date_now = strtotime(date('d-m-Y H:i'));
    $date_start = strtotime(str_replace('/', '-', $voucher->start_date));
    $date_end = strtotime(str_replace('/', '-', $voucher->end_date));

    if ($voucher) {
        // Check minimum order value first
        if ($subtotal < floatval($voucher->min_order)) {
            if (ICL_LANGUAGE_CODE == 'en') {
                $response = ['status' => 0, 'message' => sprintf('Minimum order value required is $%s', number_format($voucher->min_order, 2))];
            } else {
                $response = ['status' => 0, 'message' => sprintf('Minimum order value required is $%s', number_format($voucher->min_order, 2))];
            }
            returnajax($response);
            wp_die();
        }

        $product_accept = $voucher->products;

        $applicable_products = array_intersect($dataProduct, json_decode($product_accept));
        if ($voucher->number_of_vouchers <= 0) {
            if (ICL_LANGUAGE_CODE == 'en') {
                $response = ['status' => 0, 'message' => 'Discount code has been fully used'];
            } else {
                $response = ['status' => 0, 'message' => 'Discount code has been fully used'];
            }
        } elseif ($date_now < $date_start) {
            $response = ['status' => 0, 'message' => 'Discount code has not been activated yet'];
        } elseif ($date_now > $date_end) {
            $response = ['status' => 0, 'message' => 'Discount code has expired'];
        } else {
            if ($voucher->type == 0) {
                if (!empty($dataProduct)) {
                    $product_accept = json_decode($voucher->products, true);
                    $applicable_products = array_intersect($dataProduct, $product_accept);

                    if (!empty($applicable_products)) {
                        $product_ids = [];
                        $product_names = [];
                        foreach ($applicable_products as $value) {
                            $product = get_post($value);
                            if ($product) {
                                $product_names[] = $product->post_title;
                                $product_ids[] = $value;
                            }
                        }
                        $voucher->product_id = $product_ids;
                        $response = ['status' => 3, 'message' => 'Discount code applies to products: ' . implode(', ', $product_names), 'voucher' => $voucher];
                    } else {
                        $response = ['status' => 0, 'message' => 'Discount code does not apply to the selected products'];
                    }
                } else {
                    $response = ['status' => 0, 'message' => 'No products selected'];
                }
            } elseif ($voucher->type == 1) {
                if ($voucher->status == 0) {
                    $response = ['status' => 0, 'message' => 'Discount code is invalid'];
                } else {
                    if (ICL_LANGUAGE_CODE == 'en') {
                        $response = ['status' => 1, 'message' => 'Discount code applied successfully', 'voucher' => $voucher];
                    } else {
                        $response = ['status' => 1, 'message' => 'Discount code is valid', 'voucher' => $voucher];
                    }
                }
            }
        }
    } else {
        $response = ['status' => 0, 'message' => 'Discount code is invalid'];
    }

    returnajax($response);
    wp_die();
}

add_action('wp_ajax_verify_current_password', 'verify_current_password_callback');
add_action('wp_ajax_nopriv_verify_current_password', 'verify_current_password_callback');

function verify_current_password_callback()
{
    // Kiểm tra đăng nhập
    $authenticated_user = validate_user_token();
    if (!$authenticated_user) {
        wp_send_json_error('Unauthorized');
        wp_die();
    }

    // Lấy mật khẩu hiện tại từ request
    $current_password = $_POST['current_password'] ?? '';

    // Kiểm tra mật khẩu
    $is_password_correct = password_verify($current_password, $authenticated_user->password);

    wp_send_json([
        'success' => $is_password_correct
    ]);
    wp_die();
}

function add_dealer_function()
{
    global $wpdb;

    // Kiểm tra nếu đúng yêu cầu AJAX
    if (isset($_POST['ajax_action']) && $_POST['ajax_action'] === 'add_dealer') {
        // Chuẩn bị phản hồi
        $response = array();

        // Lấy và làm sạch dữ liệu từ form
        $dealer_data = array(
            'dealer_name' => sanitize_text_field($_POST['dealer_name']),
            'phone'       => sanitize_text_field($_POST['phone']),
            'open_at'     => sanitize_text_field($_POST['open_at']),
            'close_at'    => sanitize_text_field($_POST['close_at']),
            'state'       => sanitize_text_field($_POST['state']),
            'city'        => sanitize_text_field($_POST['city']),
            'address'     => sanitize_text_field($_POST['address']),
            'map'         => $_POST['map'],
            'zip_code'    => sanitize_text_field($_POST['zip_code']),
            'latitude'    => sanitize_text_field($_POST['latitude']),
            'longitude'   => sanitize_text_field($_POST['longitude']),
        );

        // Kiểm tra các trường bắt buộc
        $errors = array();
        foreach ($dealer_data as $key => $value) {
            if (empty($value)) {
                $errors[] = ucfirst(str_replace('_', ' ', $key)) . " is required";
            }
        }

        if (!empty($errors)) {
            $response['success'] = false;
            $response['message'] = implode('<br>', $errors);
        } else {
            // Thêm dữ liệu vào bảng wp_dealer
            $table_name = $wpdb->prefix . 'dealer';
            $result = $wpdb->insert($table_name, $dealer_data);

            if ($result === false) {
                $response['success'] = false;
                $response['message'] = 'Database error: ' . $wpdb->last_error;
            } else {
                $response['success'] = true;
                $response['message'] = 'Dealer added successfully';
            }
        }

        // Trả về phản hồi dạng JSON
        echo json_encode($response);
        wp_die(); // Dừng xử lý
    }
}
add_action('wp_ajax_add_dealer', 'add_dealer_function');
add_action('wp_ajax_nopriv_add_dealer', 'add_dealer_function');

function edit_dealer_function()
{
    global $wpdb;

    if (isset($_POST['ajax_action']) && $_POST['ajax_action'] === 'edit_dealer') {
        $response = array();

        // Lấy dealer_id từ POST data thay vì URL
        $id = (int)($_POST['dealer_id']);

        // Sanitize và chuẩn bị dealer data
        $dealer_data = array(
            'dealer_name' => sanitize_text_field($_POST['dealer_name']),
            'phone'       => sanitize_text_field($_POST['phone']),
            'open_at'     => sanitize_text_field($_POST['open_at']),
            'close_at'    => sanitize_text_field($_POST['close_at']),
            'state'       => sanitize_text_field($_POST['state']),
            'city'        => sanitize_text_field($_POST['city']),
            'address'     => sanitize_text_field($_POST['address']),
            'map'         => $_POST['map'],
            'zip_code'    => sanitize_text_field($_POST['zip_code']),
            'latitude'    => sanitize_text_field($_POST['latitude']),
            'longitude'   => sanitize_text_field($_POST['longitude']),
        );

        // Validate required fields
        $errors = array();
        foreach ($dealer_data as $key => $value) {
            if (empty($value)) {
                $errors[] = ucfirst(str_replace('_', ' ', $key)) . " is required";
            }
        }

        if (!empty($errors)) {
            $response['success'] = false;
            $response['message'] = implode('<br>', $errors);
        } else {
            // Update dealer record
            $table_name = $wpdb->prefix . 'dealer';
            $where = array('id' => $id);
            $result = $wpdb->update($table_name, $dealer_data, $where);

            if ($result === false) {
                $response['success'] = false;
                $response['message'] = 'Database error: ' . $wpdb->last_error;
            } else {
                $response['success'] = true;
                $response['message'] = 'Dealer updated successfully';
            }
        }

        echo json_encode($response);
        wp_die();
    }
}

add_action('wp_ajax_edit_dealer', 'edit_dealer_function');
add_action('wp_ajax_nopriv_edit_dealer', 'edit_dealer_function');

// Xoa dealer
add_action('wp_ajax_delete_dealer', 'handle_delete_dealer');
function handle_delete_dealer() {
    global $wpdb;
    
    // Verify nonce
    check_ajax_referer('my_ajax_nonce', 'security');
    
    // Get dealer ID
    $dealer_id = intval($_POST['dealer_id']);
    
    // Delete the dealer
    $result = $wpdb->delete(
        'wp_dealer',
        array('id' => $dealer_id),
        array('%d')
    );
    
    if ($result !== false) {
        wp_send_json_success(array(
            'message' => 'Dealer deleted successfully'
        ));
    } else {
        wp_send_json_error(array(
            'message' => 'Failed to delete dealer'
        ));
    }
    
    wp_die();
}

function enqueue_delete_dealer_scripts() {
    wp_localize_script('jquery', 'ajax_object', array(
        'nonce' => wp_create_nonce('my_ajax_nonce')
    ));
}
add_action('admin_enqueue_scripts', 'enqueue_delete_dealer_scripts');

// Xoa discount
add_action('wp_ajax_delete_discount', 'handle_delete_discount');
function handle_delete_discount() {
    global $wpdb;
    
    // Verify nonce
    check_ajax_referer('my_ajax_nonce', 'security');
    
    // Get discount ID
    $discount_id = intval($_POST['discount_id']);
    
    // Delete the discount
    $result = $wpdb->delete(
        'wp_discount_dealer',
        array('id' => $discount_id),
        array('%d')
    );
    
    if ($result !== false) {
        wp_send_json_success(array(
            'message' => 'Discount deleted successfully'
        ));
    } else {
        wp_send_json_error(array(
            'message' => 'Failed to delete discount'
        ));
    }
    
    wp_die();
}

function enqueue_delete_discount_scripts() {
    wp_localize_script('jquery', 'ajax_object', array(
        'nonce' => wp_create_nonce('my_ajax_nonce')
    ));
}
add_action('admin_enqueue_scripts', 'enqueue_delete_discount_scripts');

add_action('wp_ajax_save_dealer_discount', 'handle_save_dealer_discount');
add_action('wp_ajax_nopriv_save_dealer_discount', 'handle_save_dealer_discount');

function handle_save_dealer_discount() {
    global $wpdb;
    
    $data = json_decode(stripslashes($_POST['data']), true);
    $id_dealer = $data[0]['id_dealer'];
    
    try {
        $wpdb->query('START TRANSACTION');
        
        // 1. Lấy tất cả discount hiện tại của dealer từ DB
        $current_discounts = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM wp_discount_dealer WHERE id_dealer = %d",
                $id_dealer
            ),
            ARRAY_A
        );
        
        // 2. Tạo mảng product ID từ form data và DB để dễ so sánh
        $form_products = array_column($data, 'product');
        $db_products = array_column($current_discounts, 'product');
        
        // 3. Tìm các product cần xóa (có trong DB nhưng không có trong form)
        $products_to_delete = array_diff($db_products, $form_products);
        if (!empty($products_to_delete)) {
            foreach ($products_to_delete as $product) {
                $wpdb->delete(
                    'wp_discount_dealer',
                    array(
                        'id_dealer' => $id_dealer,
                        'product' => $product
                    )
                );
            }
        }
        
        // 4. Xử lý từng item trong form data
        foreach ($data as $item) {
            // Kiểm tra xem product này đã tồn tại trong DB chưa
            $existing_discount = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM wp_discount_dealer WHERE id_dealer = %d AND product = %s",
                    $id_dealer,
                    $item['product']
                )
            );
            
            if ($existing_discount) {
                // 4a. Nếu đã tồn tại thì update
                $wpdb->update(
                    'wp_discount_dealer',
                    array(
                        'discount_type' => $item['discount_type'],
                        'discount_amount' => $item['discount_amount']
                    ),
                    array(
                        'id_dealer' => $id_dealer,
                        'product' => $item['product']
                    )
                );
            } else {
                // 4b. Nếu chưa tồn tại thì insert
                $wpdb->insert(
                    'wp_discount_dealer',
                    array(
                        'id_dealer' => $id_dealer,
                        'product' => $item['product'],
                        'discount_type' => $item['discount_type'],
                        'discount_amount' => $item['discount_amount']
                    )
                );
            }
        }
        
        $wpdb->query('COMMIT');
        
        wp_send_json_success(array(
            'message' => 'Discounts saved successfully',
            'deleted' => $products_to_delete,
            'updated' => array_intersect($db_products, $form_products),
            'inserted' => array_diff($form_products, $db_products)
        ));
        
    } catch(Exception $e) {
        $wpdb->query('ROLLBACK');
        wp_send_json_error($e->getMessage());
    }
    
    die();
}

// Xuất file csv
add_action('admin_init', 'handle_csv_export');
function handle_csv_export() {
    if (!isset($_POST['export_csv'])) return;

    // Thiết lập header và BOM
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="users_export_' . date('Y-m-d') . '.csv"');
    echo "\xEF\xBB\xBF"; // UTF-8 BOM

    // Lấy dữ liệu từ database
    global $wpdb;
    $wpdb->set_charset('utf8mb4'); // Đảm bảo charset
    
    $myrows = $wpdb->get_results("SELECT * FROM wp_account_users");

    // Ghi CSV
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Tên', 'Họ', 'Email', 'Provider']);

    foreach ($myrows as $customer) {
        fputcsv($output, [
            $customer->id,
            $customer->first_name,
            $customer->last_name,
            $customer->email,
            $customer->provider
        ]);
    }

    fclose($output);
    exit();
}

// // Dịch
add_action('init', function () {
    pll_register_string('theme', 'Category');
    pll_register_string('theme', 'All');
    pll_register_string('theme', 'Log out');
    pll_register_string('theme', 'Search');
    pll_register_string('theme', 'Sort by');
    pll_register_string('theme', 'Latest');
    pll_register_string('theme', 'Most popular');
    pll_register_string('theme', 'Read more');
    pll_register_string('theme', 'Home');
    pll_register_string('theme', 'NEW');
    pll_register_string('theme', 'Back to cart');
    pll_register_string('theme', 'Product list');
    pll_register_string('theme', 'Blog list');
    pll_register_string('theme', 'Search result');
    pll_register_string('theme', 'Quantity');
    pll_register_string('theme', 'Type');
    pll_register_string('theme', 'Pack');
    pll_register_string('theme', 'Delivery information');
    pll_register_string('theme', 'Enter delivery information');
    pll_register_string('theme', 'Enter other delivery information');
    pll_register_string('theme', 'Select payment method');
    pll_register_string('theme', 'Add a credit or debit');
    pll_register_string('theme', 'Select or enter an offer');
    pll_register_string('theme', 'Discount code');
    pll_register_string('theme', 'Enter your discount code here');
    pll_register_string('theme', 'Apply code');
    pll_register_string('theme', 'Promotion');
    pll_register_string('theme', 'Order information');
    pll_register_string('theme', 'Sub total');
    pll_register_string('theme', 'Total');
    pll_register_string('theme', 'Total payment');
    pll_register_string('theme', 'Shipping fee');
    pll_register_string('theme', 'Discount');
    pll_register_string('theme', `By proceeding with your purchase, you agree to SureMeal's`);
    pll_register_string('theme', 'Terms of Service');
    pll_register_string('theme', 'and');
    pll_register_string('theme', 'Personal Data Processing Policy.');
    pll_register_string('theme', 'Check out');
    pll_register_string('theme', 'Create account');
    pll_register_string('theme', 'First name');
    pll_register_string('theme', 'Last name');
    pll_register_string('theme', 'Enter your first name');
    pll_register_string('theme', 'Enter your last name');
    pll_register_string('theme', 'First name cannot be empty');
    pll_register_string('theme', 'Last name cannot be empty');
    pll_register_string('theme', 'Email');
    pll_register_string('theme', 'Invalid email');
    pll_register_string('theme', 'Enter your email');
    pll_register_string('theme', 'Password');
    pll_register_string('theme', 'Enter your password');
    pll_register_string('theme', 'Password must have at least 6 characters');
    pll_register_string('theme', 'New Password');
    pll_register_string('theme', 'Please re-enter your password');
    pll_register_string('theme', 'Passwords do not match');
    pll_register_string('theme', 'Email already exists. Please use a different email.');
    pll_register_string('theme', 'An error occurred while registering. Please try again.');
    pll_register_string('theme', 'Enter your new password');
    pll_register_string('theme', 'Confirm Password');
    pll_register_string('theme', 'Confirm your new password');
    pll_register_string('theme', 'Re-enter password');
    pll_register_string('theme', 'Enter your password again');
    pll_register_string('theme', `By creating an account, you agree to SureMeal's`);
    pll_register_string('theme', 'Conditions of Use');
    pll_register_string('theme', 'Enter your email');
    pll_register_string('theme', 'Privacy Notice');
    pll_register_string('theme', 'Sign up');
    pll_register_string('theme', 'Sign in');
    pll_register_string('theme', 'For Sale Partners');
    pll_register_string('theme', 'For Dealer');
    pll_register_string('theme', 'Sign in');
    pll_register_string('theme', 'Loading...');
    pll_register_string('theme', 'Already have an account?');
    pll_register_string('theme', 'Or connect with:');
    pll_register_string('theme', 'Continue with Google');
    pll_register_string('theme', 'Continue with Facebook');
    pll_register_string('theme', 'Continue with Apple');
    pll_register_string('theme', 'Login cancelled. Please try again.');
    pll_register_string('theme', 'Forgot password?');
    pll_register_string('theme', 'Keep me sign in');
    pll_register_string('theme', 'Check this box only when on a private device.');
    pll_register_string('theme', 'New to SureMeal?');
    pll_register_string('theme', 'We will send you an email to reset your password.');
    pll_register_string('theme', 'Send email');
    pll_register_string('theme', 'Cancel');
    pll_register_string('theme', 'A valid password reset link has already been sent to your email.');
    pll_register_string('theme', 'Failed to send reset email.');
    pll_register_string('theme', 'Failed to generate a reset token. Please try again.');
    pll_register_string('theme', 'No user found with this email address');
    pll_register_string('theme', 'Please enter a valid email address');
    pll_register_string('theme', 'Please enter a valid phone number');
    pll_register_string('theme', 'Create New Password');
    pll_register_string('theme', 'Reset Password');
    pll_register_string('theme', 'Password successfully reset. Please log in with your new password.');
    pll_register_string('theme', 'Password reset link sent to your email.');
    pll_register_string('theme', 'FAQs');
    pll_register_string('theme', 'No results found');
    pll_register_string('theme', 'General');
    pll_register_string('theme', 'Health');
    pll_register_string('theme', 'Search by keywords');
    pll_register_string('theme', 'Any question?');
    pll_register_string('theme', 'Our support agents are ready with the answer.');
    pll_register_string('theme', 'Cart');
    pll_register_string('theme', 'Search everything');
    pll_register_string('theme', 'You have not selected the checkbox yet!');
    pll_register_string('theme', 'There are no products left in the cart, please select a product to purchase');
    pll_register_string('theme', 'Select all');
    pll_register_string('theme', 'On sale now');
    pll_register_string('theme', 'Exclusive promotion');
    pll_register_string('theme', 'Ending in:');
    pll_register_string('theme', 'Tomorrow');
    pll_register_string('theme', 'From');
    pll_register_string('theme', 'Buy Now');
    pll_register_string('theme', 'Promotional products');
    pll_register_string('theme', 'Reviews');
    pll_register_string('theme', 'Contact Us');
    pll_register_string('theme', 'Contact information');
    pll_register_string('theme', 'Business information');
    pll_register_string('theme', 'Private Label');
    pll_register_string('theme', 'Personal');
    pll_register_string('theme', 'Order details');
    pll_register_string('theme', 'Order code');
    pll_register_string('theme', 'Copied the text');
    pll_register_string('theme', 'Deliver');
    pll_register_string('theme', 'products');
    pll_register_string('theme', 'Receipt');
    pll_register_string('theme', 'Processing');
    pll_register_string('theme', 'Canceled');
    pll_register_string('theme', 'In progress');
    pll_register_string('theme', 'Completed');
    pll_register_string('theme', 'Order Placed');
    pll_register_string('theme', 'Packaging');
    pll_register_string('theme', 'On The Road');
    pll_register_string('theme', 'Recipient information');
    pll_register_string('theme', 'Payment method');
    pll_register_string('theme', 'Delivered');
    pll_register_string('theme', 'Delivered');
    pll_register_string('theme', 'Address');
    pll_register_string('theme', 'Phone');
    pll_register_string('theme', 'Payment information');
    pll_register_string('theme', 'My orders');
    pll_register_string('theme', 'Order infomation');
    pll_register_string('theme', 'Search by order code');
    pll_register_string('theme', 'View other products');
    pll_register_string('theme', 'Products');
    pll_register_string('theme', 'Views details');
    pll_register_string('theme', 'Sort by');
    pll_register_string('theme', 'Best seller');
    pll_register_string('theme', 'Low price');
    pll_register_string('theme', 'High price');
    pll_register_string('theme', 'Filter');
    pll_register_string('theme', 'Target user');
    pll_register_string('theme', 'Search by name');
    pll_register_string('theme', 'View more');
    pll_register_string('theme', 'View less');
    pll_register_string('theme', 'View more products');
    pll_register_string('theme', 'View less products');
    pll_register_string('theme', 'Price');
    pll_register_string('theme', 'Brand');
    pll_register_string('theme', 'Needs');
    pll_register_string('theme', 'Personal information');
    pll_register_string('theme', 'Full name');
    pll_register_string('theme', 'Change information');
    pll_register_string('theme', 'Add new address');
    pll_register_string('theme', 'Shop now');
    pll_register_string('theme', 'Customer Support');
    pll_register_string('theme', 'Services');
    pll_register_string('theme', 'Join our Mailing List');
    pll_register_string('theme', 'Your email');
    pll_register_string('theme', 'Subcribe');
    pll_register_string('theme', 'Find us on Google Maps');
    pll_register_string('theme', 'Find a Dealer');
    pll_register_string('theme', 'Data is being updated');
    pll_register_string('theme', 'Dealer near your location');
    pll_register_string('theme', 'Search dealer name, zip code, or address');
    pll_register_string('theme', 'Step');
    pll_register_string('theme', 'Processing');
    pll_register_string('theme', 'Or');
    pll_register_string('theme', 'Showing all dealerships');
    pll_register_string('theme', 'View more detail');
    pll_register_string('theme', 'Could not find your location');
    pll_register_string('theme', 'You denied location access. Please enable permissions in your browser settings.');
    pll_register_string('theme', 'Open');
    pll_register_string('theme', 'Open at');
    pll_register_string('theme', 'Close');
    pll_register_string('theme', 'Closes at');
    pll_register_string('theme', 'See direction');
    pll_register_string('theme', 'Phone number');
    pll_register_string('theme', 'Name');
    pll_register_string('theme', 'Country/Region');
    pll_register_string('theme', 'City');
    pll_register_string('theme', 'State');
    pll_register_string('theme', 'ZIP Code');
    pll_register_string('theme', 'Your name');
    pll_register_string('theme', 'Street address');
    pll_register_string('theme', 'optianal');
    pll_register_string('theme', 'Enter your city');
    pll_register_string('theme', 'Choose State');
    pll_register_string('theme', 'No provinces found.');
    pll_register_string('theme', 'Unable to fetch provinces. Please try again later.');
    pll_register_string('theme', 'Please select a product before proceeding to checkout!');
    pll_register_string('theme', 'Voucher cannot be applied because there are no products selected!');
    pll_register_string('theme', 'Please wait...');
    pll_register_string('theme', 'An error occurred. Please try again.');
    pll_register_string('theme', 'Free of charge');
    pll_register_string('theme', 'Cart data is invalid or empty.');
    pll_register_string('theme', 'Country not supported for shipping!');
    pll_register_string('theme', 'Please enter an order code.');
    pll_register_string('theme', 'Please enter your full name');
    pll_register_string('theme', 'Please enter your phone number');
    pll_register_string('theme', 'Please enter your country');
    pll_register_string('theme', 'Please enter your email');
    pll_register_string('theme', 'Invalid email address');
    pll_register_string('theme', 'Please enter your city');
    pll_register_string('theme', 'Please enter your postal code');
    pll_register_string('theme', 'Please enter your province');
    pll_register_string('theme', 'Please enter your state or province');
    pll_register_string('theme', 'Please enter your address');
    pll_register_string('theme', 'Cart data is invalid or empty.');
    pll_register_string('theme', 'Form not found.');
    pll_register_string('theme', 'Please enter your address');
    pll_register_string('theme', 'Postal code');
    pll_register_string('theme', 'Enter your Postal code');
    pll_register_string('theme', 'Enter your ZIP Code');
    pll_register_string('theme', 'Save this information for next time');
    pll_register_string('theme', 'No orders found.');
    pll_register_string('theme', 'Recently viewed products');
    pll_register_string('theme', 'Please fill in the appropriate price range');
    pll_register_string('theme', 'Please fill in all password fields');
    pll_register_string('theme', 'Please confirm new password');
    pll_register_string('theme', 'Current password is required');
    pll_register_string('theme', 'Current password is incorrect');
    pll_register_string('theme', 'File is too large. Maxium size is 5MB.');
    pll_register_string('theme', 'Invalid file type. Only JPG, PNG and GIF are allowed.');
    pll_register_string('theme', 'This email is already in use');
    pll_register_string('theme', 'Are you sure?');
    pll_register_string('theme', 'You will be logged out!');
    pll_register_string('theme', 'This email is already in use');
    pll_register_string('theme', 'Yes, log me out!');
    pll_register_string('theme', 'Could not find your location');
    pll_register_string('theme', 'Cancel');
    pll_register_string('theme', 'You denied location access. Please enable permissions in your browser settings.');
    pll_register_string('theme', 'Location information is unavailable.');
    pll_register_string('theme', 'The request to get user location timed out.');
    pll_register_string('theme', 'Locating your position...');
    pll_register_string('theme', 'Geolocation is not supported by your browser');
    pll_register_string('theme', 'Showing');
    pll_register_string('theme', 'dealers near you');
    pll_register_string('theme', 'Registered to receive information successfully!');
    pll_register_string('theme', 'About Us');
    pll_register_string('theme', 'Send message');
});
