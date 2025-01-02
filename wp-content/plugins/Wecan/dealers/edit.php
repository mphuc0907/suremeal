<?php
global $wpdb;
include __DIR__ . "/../includes/padding.php";
require_once __DIR__ . '/../includes/function.php';

$module_pathadd = 'admin.php?page=dealer_manager';
$module_short_url = str_replace('admin.php?page=', '', $module_pathadd);

// Get dealer data by ID
$id = (int)($_GET['id']);
$myrows = $wpdb->get_row("SELECT * FROM wp_dealer WHERE id=" . $id);

if(isset($_POST['ajax_action']) && $_POST['ajax_action'] == 'edit_dealer') {
    $response = array();
    
    // Sanitize inputs
    $dealer_data = array(
        'dealer_name' => sanitize_text_field($_POST['dealer_name']),
        'phone' => sanitize_text_field($_POST['phone']),
        'open_at' => sanitize_text_field($_POST['open_at']),
        'close_at' => sanitize_text_field($_POST['close_at']),
        'state' => sanitize_text_field($_POST['state']),
        'city' => sanitize_text_field($_POST['city']),
        'address' => sanitize_text_field($_POST['address']),
        'map' => $_POST['map'],
        'zip_code' => sanitize_text_field($_POST['zip_code']),
        'latitude' => sanitize_text_field($_POST['latitude']),
        'longitude' => sanitize_text_field($_POST['longitude'])
    );

    // Validate required fields
    $errors = array();
    foreach($dealer_data as $key => $value) {
        if(empty($value)) {
            $errors[] = ucfirst(str_replace('_', ' ', $key)) . " is required";
        }
    }

    if(!empty($errors)) {
        $response['success'] = false;
        $response['message'] = implode('<br>', $errors);
    } else {
        // Update database
        $result = $wpdb->update(
            'wp_dealer', 
            $dealer_data,
            array('id' => $id)
        );
        
        if($result === false) {
            $response['success'] = false;
            $response['message'] = 'Database error: ' . $wpdb->last_error;
        } else {
            $response['success'] = true;
            $response['message'] = 'Dealer updated successfully';
        }
    }
    
    echo json_encode($response);
    exit;
}
?>
<style>
    input {
        width: 100%;
    }

    .d-none {
        display: none !important;
    }

    .roles-report .item-role .table__wrapper {
        padding-bottom: 10px;
    }

    .roles-report .item-role {
        padding-bottom: 10px;
        border-bottom: 1px solid #ccc;
        padding-top: 10px;
    }

    .role-title-1 {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 15px;
        text-transform: uppercase;
    }

    .role-title-2 {
        font-size: 16px;
        padding-bottom: 10px;
        font-weight: 600;
    }

    .br-checkbox {
        padding-bottom: 10px;
    }

    .br-checkbox label {
        font-weight: 500;
    }

    .br-checkbox input {
        margin: 0;
    }

    .checkbox-all {
        padding-left: 15px;
    }

    .title-mgg {
        font-weight: 500 !important;
        padding: 0 !important;
    }

    .red-validate {
        color: red;
    }

    .date-time {
        display: flex;
        flex-wrap: wrap;
    }

    .time-space {
        padding-left: 10px;
        padding-right: 10px;
        font-size: 20px;
    }

    .loaigiamgia {
        display: flex;
        flex-wrap: wrap;
    }

    input[type=number] {
        -webkit-appearance: none !important; /* loại bỏ giao diện mặc định */
        appearance: none !important;
    }

    .mucgiamtd .type-mgtd {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        padding-bottom: 15px;
    }

    .mucgiamtd .type-mgtd .typetd {
        padding-right: 30px;
    }

    .inputnumber {
        width: 400px !important;
    }

    .button-change {
        width: max-content;
        color: red;
        border-radius: 5px;
        border: 1px solid red;
        cursor: pointer;
    }

    .border-error {
        border: 1px solid #e91c24 !important;
    }

    .error {
        color: #e91c24;
    }

    .checkboxmg {
        margin: 0;
    }

    .modal-address {
        display: none;
        position: fixed;
        width: 960px;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #ffffff;
        border-radius: 5px;
        z-index: 1001;
        padding: 10px 15px;
    }

    .bg__overlay {
        display: none;
        position: fixed;
        width: 100%;
        height: 100%;
        background: rgba(41, 43, 46, 0.5);
        z-index: 1000;
        top: 0;
    }

    .d-block {
        display: block !important;
    }

    .iconloadgif {
        top: 0;
        right: 0;
        left: 0;
        bottom: 0;
        position: absolute;
        margin: auto;
    }

    .ov-hiden {
        overflow: hidden;
    }

    .member-search {
        display: flex;
    }

    .member-search .button.button-primary {
        width: 100px;
    }

    #memberSearch {
        width: 500px;
        margin-bottom: 10px;
    }
    .list-check{
        display: flex;
        gap: 2rem;
    }
    textarea{
        width: 100%;
    }
</style>
<div class="wrap">
    <input type="hidden" id="urlAjax" value="<?= admin_url() ?>admin-ajax.php">
    <h1 style="margin-bottom:15px;">Edit <?php echo $mdlconf['title']; ?>
        <a class="page-title-action" href="admin.php?page=dealer_manager">Back to list</a>
    </h1>
    <form id="editform" method="post">
        <input type="hidden" id="dealer_id" name="dealer_id" value="<?php echo esc_attr($id); ?>">
        <div id="poststuff">
            <div class="metabox-holder columns-2" id="post-body">
                <div id="post-body-content" class="pos1">
                    <div class="postbox">
                        <h2 class="hndle ui-sortable-handle api-title">Basic information</h2>
                        <div class="inside">
                            <table class="form-table ft_metabox leftform">
                                <tr>
                                    <td style="width: 250px;">Dealer name<span class="red-validate">*</span></td>
                                    <td>
                                        <input type="text" class="validate-input" id="dealer_name" name="dealer_name" 
                                               value="<?php echo esc_attr($myrows->dealer_name); ?>" size="50" placeholder="Enter">
                                        <label id="dealer_name-error" class="error d-none">This field is required</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Phone <span class="red-validate">*</span></td>
                                    <td>
                                        <input type="text" class="validate-input" id="phone" name="phone" 
                                               value="<?php echo esc_attr($myrows->phone); ?>" size="50" placeholder="Enter">
                                        <label id="phone-error" class="error d-none">This field is required</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Open | Close <span class="red-validate">*</span></td>
                                    <td>
                                        <div class="date-time">
                                            <div class="time-start">
                                                <input type="time" id="open_at" name="open_at" 
                                                       value="<?php echo esc_attr($myrows->open_at); ?>">
                                            </div>
                                            <div class="time-space">-</div>
                                            <div class="time-end">
                                                <input type="time" id="close_at" name="close_at" 
                                                       value="<?php echo esc_attr($myrows->close_at); ?>">
                                            </div>
                                        </div>
                                        <label id="time-error" class="error d-none">Please select both open and close time</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        State | City<span class="red-validate">*</span>
                                    </td>
                                    <td>
                                        <div class="loaigiamgia">
                                            <div class="type-gg">
                                                <select name="state" id="state">
                                                    <option value="">Select State</option>
                                                </select>
                                            </div>
                                            <div class="time-space">-</div>
                                            <div class="type-gg">
                                                <select name="city" id="city" disabled>
                                                    <option value="">Select City</option>
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Address <span class="red-validate">*</span>
                                    </td>
                                    <td>
                                        <div class="">
                                            <input type="text" class="validate-input" id="address" name="address" 
                                                   value="<?php echo esc_attr($myrows->address); ?>" placeholder="Enter">
                                            <label id="address-error" class="error d-none">This field cannot be empty</label>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Map <span class="red-validate">*</span>
                                    </td>
                                    <td>
                                        <div class="">
                                            <textarea class="validate-input" name="map" id="map" placeholder="Enter" rows="5"><?php echo $myrows->map; ?></textarea>
                                            <label id="map-error" class="error d-none">This field cannot be empty</label>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Zip code <span class="red-validate">*</span>
                                    </td>
                                    <td>
                                        <div class="">
                                            <input type="text" class="validate-input" id="zip_code" name="zip_code" 
                                                   value="<?php echo esc_attr($myrows->zip_code); ?>" placeholder="Enter">
                                            <label id="zip_code-error" class="error d-none">This field cannot be empty</label>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Longitude <span class="red-validate">*</span>
                                    </td>
                                    <td>
                                        <div class="">
                                            <input type="text" class="validate-input" id="longitude" name="longitude" 
                                                   value="<?php echo esc_attr($myrows->longitude); ?>" placeholder="Enter">
                                            <label id="longitude-error" class="error d-none">This field cannot be empty</label>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Latitude <span class="red-validate">*</span>
                                    </td>
                                    <td>
                                        <div class="">
                                            <input type="text" class="validate-input" id="latitude" name="latitude" 
                                                   value="<?php echo esc_attr($myrows->latitude); ?>" placeholder="Enter">
                                            <label id="latitude-error" class="error d-none">This field cannot be empty</label>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="postbox-container" id="postbox-container-1">
                    <div class="meta-box-sortables ui-sortable" id="side-sortables">
                        <div class="postbox" id="submitdiv">
                            <h2 class="hndle ui-sortable-handle"><span>Update</span></h2>
                            <div class="inside">
                                <div id="submitpost" class="submitbox">
                                    <div id="major-publishing-actions">
                                        <div id="publishing-action">
                                            <input type="submit" value="Update" id="publish" class="button button-primary button-large">
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const stateSelect = document.getElementById('state');
    const citySelect = document.getElementById('city');
    const jsonUrl = "<?php echo get_template_directory_uri(); ?>/assets/state-and-city.json";
    
    // Lấy giá trị state và city từ database đã được render vào PHP
    const currentState = "<?php echo esc_js($myrows->state); ?>";
    const currentCity = "<?php echo esc_js($myrows->city); ?>";

    // Fetch the JSON file and populate the states
    fetch(jsonUrl)
        .then(response => response.json())
        .then(data => {
            // Populate the state dropdown
            for (const state in data) {
                const option = document.createElement('option');
                option.value = state;
                option.textContent = state;
                // Set selected cho state hiện tại
                if (state === currentState) {
                    option.selected = true;
                }
                stateSelect.appendChild(option);
            }

            // Nếu có state, populate cities ngay lập tức
            if (currentState) {
                const cities = data[currentState];
                if (cities) {
                    citySelect.innerHTML = '<option value="">Select City</option>';
                    cities.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city;
                        option.textContent = city;
                        // Set selected cho city hiện tại
                        if (city === currentCity) {
                            option.selected = true;
                        }
                        citySelect.appendChild(option);
                    });
                    citySelect.disabled = false;
                }
            }
        })
        .catch(error => console.error('Error fetching JSON:', error));

    // Handle state selection change
    stateSelect.addEventListener('change', function () {
        const selectedState = stateSelect.value;

        // Clear and reset city dropdown
        citySelect.innerHTML = '<option value="">Select City</option>';
        citySelect.disabled = true;

        if (selectedState) {
            fetch(jsonUrl)
                .then(response => response.json())
                .then(data => {
                    const cities = data[selectedState];
                    if (cities) {
                        cities.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city;
                            option.textContent = city;
                            citySelect.appendChild(option);
                        });
                        citySelect.disabled = false;
                    }
                })
                .catch(error => console.error('Error fetching JSON:', error));
        }
    });
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    function validateForm() {
        let isValid = true;
        const inputs = document.querySelectorAll('.validate-input');
        
        inputs.forEach(function (input) {
            const error = input.nextElementSibling;
            if (!input.value) {
                isValid = false;
                error.classList.remove('d-none');
            } else {
                error.classList.add('d-none');
            }
        });

        const openAt = document.getElementById('open_at').value;
        const closeAt = document.getElementById('close_at').value;
        const timeError = document.getElementById('time-error');

        if (!openAt || !closeAt) {
            isValid = false;
            timeError.classList.remove('d-none');
        } else {
            timeError.classList.add('d-none');
        }

        return isValid;
    }

    document.getElementById('editform').addEventListener('submit', function (e) {
        e.preventDefault();

        if (!validateForm()) {
            return false;
        }

        const formData = new FormData();
        formData.append('action', 'edit_dealer'); // WordPress AJAX action key
        formData.append('ajax_action', 'edit_dealer');
        formData.append('dealer_id', document.getElementById('dealer_id').value); 
        formData.append('dealer_name', document.getElementById('dealer_name').value);
        formData.append('phone', document.getElementById('phone').value);
        formData.append('open_at', document.getElementById('open_at').value);
        formData.append('close_at', document.getElementById('close_at').value);
        formData.append('state', document.getElementById('state').value);
        formData.append('city', document.getElementById('city').value);
        formData.append('address', document.getElementById('address').value);
        formData.append('map', document.getElementById('map').value);
        formData.append('zip_code', document.getElementById('zip_code').value);
        formData.append('latitude', document.getElementById('latitude').value);
        formData.append('longitude', document.getElementById('longitude').value);

        const ajaxUrl = document.getElementById('urlAjax').value;

        fetch(ajaxUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(response => {
            try {
                const result = JSON.parse(response);

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: result.message
                    }).then(() => {
                        window.location.href = 'admin.php?page=dealer_manager';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: result.message
                    });
                }
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Invalid server response'
                });
            }
        })
        .catch(() => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Server error occurred'
            });
        });
    });
});
</script>