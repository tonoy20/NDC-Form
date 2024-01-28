<?php

session_start();
$c_roll = '';
$form_errors = array();
// function trz_submit_ndc_form()
// {
if (isset($_POST['submit_form'])) {
    $f_name = $_POST['fname'];
    $l_name = $_POST['lname'];
    $n_name = $_POST['nickname'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $blood_group = $_POST['bloodgroup'];
    $batch = $_POST['batch'];
    $c_roll = $_POST['c_roll_number_disabled'] . $_POST['c_roll_number'];
    $medium = $_POST['medium'];
    $group = '';
    if (isset($_POST['science'])) {
        $group = $_POST['science'];
    }
    if (isset($_POST['arts'])) {
        $group = $_POST['arts'];
    }
    if (isset($_POST['commerce'])) {
        $group = $_POST['commerce'];
    }
    $current_address = $_POST['current_address'];
    $permanent_address = $_POST['permanent_address'];
    $employ_type = $_POST['employ_type'];
    $current_position = $_POST['current_position'];
    $company_name_details = $_POST['company_n_details'];
    $linkedIn_profile_link = $_POST['linkedIn_profile_link'];
    // Email validation
    global $wpdb;
    $table = $wpdb->prefix . 'ndc_form_details';
    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $table ;"
        )
    );
    $red_url = get_bloginfo('url');

    foreach ($results as $row) {
        if ($row->email == $email) {
            $form_errors[] = "This Email is Used!!";
            $_SESSION['err_e'] = "Email already exists!";
        }
        if ($row->phone == $phone) {
            $form_errors[] = "This phone number is used!!";
            $_SESSION['err_p'] = "Phone number already exists!";
        }
        if ($row->college_roll == $c_roll) {
            $form_errors[] = "This Roll number is used!!";
            $_SESSION['err_cr'] = "College Roll number already exists!!";
        }
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $form_errors[] = "Invalid email format";
        $_SESSION['err_ie'] = "Invalid email format";
    }

    // Display errors in the div

    // if (!empty($form_errors)) {
    //     echo "<ul class='container errorClass'>";
    //     foreach ($form_errors as $error) {
    //         echo "<li>$error</li>";
    //     }
    //     echo "</ul>";
    // }

    // Image and data insert
    if (empty($form_errors)) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ndc_form_details';
        $imagename = $_FILES["s_image"]["name"];
        $file_upload_dir = wp_upload_dir();
        $file_dirname = $file_upload_dir['basedir'] . '/photos/';
        $file_baseurl = $file_upload_dir['baseurl'] . '/photos/';
        if (!file_exists($file_dirname)) {
            wp_mkdir_p($file_dirname);
        }
        $filename = $imagename;
        $file_destination = $file_dirname . '' . $filename;
        move_uploaded_file($_FILES['s_image']['tmp_name'], $file_destination);
        $file_url = $file_baseurl . '' . $filename;

        // upload image for crop
        if (isset($_SESSION['crop_data'])) {
            file_put_contents($_SESSION['crop_img_path'], $_SESSION['crop_data']);
            $file_url = $_SESSION['crop_img_url'];
        }


        $generate_number = uniqid();

        $sql = "INSERT INTO $table_name (`f_name`, `l_name`, `n_name`, `phone`, `email`, `status`, `blood_grp`, `image`, `batch`, `college_roll`, `medium`, `group`, `current_address`, `permanent_address`, `employment_type`, `current_position`, `company_name_details`,`linkedIn_profile_link`,`generate_number`) VALUES ('$f_name','$l_name','$n_name','$phone','$email','0','$blood_group','$file_url','$batch','$c_roll','$medium','$group','$current_address','$permanent_address','$employ_type','$current_position','$company_name_details','$linkedIn_profile_link','$generate_number')";
        $wpdb->query($sql);

        $_SESSION['su'] = "Form Submitted Successfully!!";
        // echo "<div class='container display-4 p-3 bg-light text-success text-center'>Form Submitted Successfully!!</div>";


        // verify Email 
        //  settings for with verification
        $v_link = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        //echo 'settings_ad--'.$_SESSION['settings_ad'];
        $settings_verification = get_option('settings_verification');
        if ($settings_verification == 1) {
            $verification_link = $v_link . "?eee=" . $generate_number;

            $name = $f_name;
            $to = $email;
            $subject = 'Verify Your Email';
            $message = 'Click the following link to verify your email: <a href="' . $verification_link . '">Click Here</a>';

            $headers   = [
                'MIME-Version' => 'MIME-Version: 1.0',
                'Content-type' => 'text/html; charset=UTF-8',
                'X-Mailer' => 'PHP/' . phpversion(),
            ];

            /*mail($to, $subject, $message); */
            //$headers = array('Content-Type: text/html; charset=UTF-8');

            wp_mail($to, $subject, $message, $headers);
        }
    }
    
}
// }
// add_action('wp_loaded', 'trz_submit_ndc_form');

if (isset($_GET['eee'])) {
    global $wpdb;
    $generate = $_GET['eee'];

    $wpdb->update(
        'wp_ndc_form_details',
        array(
            'status' => '1',
        ),
        array('generate_number' => $generate)
    );

    $_SESSION['submit_file'] = 1;
}
?>
<?php

add_shortcode('ndc_form_shortcode', 'ndc_form_shortcode_function');

function ndc_form_shortcode_function()
{
    /* echo '<pre>';
    print_r($_SESSION);
    echo '</pre>'; */
    if (isset($_SESSION['submit_file']) && $_SESSION['submit_file'] == 1) {
?>
        <div class="container thank_you_page">
            <div class="text-center display-1 pt-5 text-info">Thank You</div>
            <div class="text-center display-3 pt-3 pb-5 text-info">Your Email Has been verified!</div>
        </div>
        <?php
    } else {
        if (isset($_SESSION['err_e']) || isset($_SESSION['err_p']) || isset($_SESSION['err_cr']) || isset($_SESSION['err_ie'])) {
        ?>
            <ul class="container errorClass">
                <p class="pt-3 text-center">Sorry! We couldn't enlist your information due to the following error(s), please correct the followings and submit your data again!</p>
                <li><?php if (isset($_SESSION['err_e'])) {
                        echo $_SESSION['err_e'];
                    } ?></li>
                <li><?php if (isset($_SESSION['err_p'])) {
                        echo $_SESSION['err_p'];
                    } ?></li>
                <li><?php if (isset($_SESSION['err_cr'])) {
                        echo $_SESSION['err_cr'];
                    } ?></li>
                <li><?php if (isset($_SESSION['err_ie'])) {
                        echo $_SESSION['err_ie'];
                    } ?></li>
            </ul>
        <?php
        }
        if (isset($_SESSION['su'])) {
        ?>
            <div class="empty-body">
                <div class='container p-3 text-success text-center border mb-5'>

                    <p style="font-size: 22px;">Thank you for sharing your information! We're thrilled to have your input. Rest assured, we'll be in touch for any additional
                        assistance we may need in the future as we work towards making this platform truly functional and amazing! </p>
                    <p style="font-size: 24px;"> Want to add another student information? </p>
                    <p><a href="https://notredamian.com/">Click Here</a></p>

                </div>
            </div>
        <?php
        } else {


        ?>
            <div class="background_img">
                <div class="container">
                    <form action="" method="POST" enctype="multipart/form-data" id="ndc-form">
                        <div class="row jumbotron p-5">
                            <div class="mx-t3 mb-5">
                                <h2 class="text-center text-info">Notre Dame College : Student / Alumni Information Collection (Beta)</h2>
                            </div>
                            <div class="form-fields-group">
                                <p> We are excited to announce the development of a comprehensive social network exclusively for Notre Dame College students! As part of our beta approach, we have initiated the process of collecting data online to create a platform that caters to the unique needs and interests of the Notre Dame community. </p>

                                <p> Your collaboration in this endeavor is crucial, and we appreciate your participation in providing valuable insights that will shape the features and functionalities of this upcoming social network. </p>

                                <p> Thank you for being a part of this exciting journey as we work together to build a social network that truly resonates with the Notre Dame spirit.</p>

                            </div>
                            <div class="form-fields-group">
                                <div>
                                    <p class="form_p_tag">Personal Information</p>
                                    <div class="row">
                                        <div class="col-sm-6 form-group">
                                            <label for="name-f">First Name:*</label>
                                            <input type="text" class="form-control" name="fname" id="name-f" placeholder="Enter your first name." required>
                                        </div>
                                        <div class="col-sm-6 form-group">
                                            <label for="name-l">Last Name:</label>
                                            <input type="text" class="form-control" name="lname" id="name-l" placeholder="Enter your last name">
                                        </div>
                                        <div class="col-sm-6 form-group">
                                            <label for="name-n">Nick Name:</label>
                                            <input type="text" class="form-control" name="nickname" id="name-n" placeholder="Enter your nick name">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6 form-group">
                                        <label for="tel">Phone:*</label>
                                        <input type="text" name="phone" class="form-control" id="tel" placeholder="Enter Your Contact Number" required>
                                    </div>
                                    <div class="col-sm-6 form-group">
                                        <label for="email">Email:*</label>
                                        <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6 form-group">
                                        <label for="blood group">Blood Group:</label>
                                        <select id="bloodgroup" class="form-control custom-select browser-default" name="bloodgroup">
                                            <option value="">Please Select Blood Group</option>
                                            <option value="O positive">O positive</option>
                                            <option value="O negative">O negative</option>
                                            <option value="A positive">A positive</option>
                                            <option value="A negative">A negative</option>
                                            <option value="B positive">B positive</option>
                                            <option value="B negative">B negative</option>
                                            <option value="AB positive">AB positive</option>
                                            <option value="AB negative">AB negative</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6 form-group">
                                        <label for="image upload">Select a Recognizable Photo:</label>
                                        <input type="file" class="form-control" name="s_image" id="s_image" placeholder="">
                                        <!-- crop Image -->
                                        <div class="card text-center" id="uploadimage" style='display:none'>
                                            <div class="card-header">
                                                Crop Image
                                            </div>
                                            <div class="card-body d-flex">
                                                <div id="image_demo" class="w-50 me-5"></div>
                                                <div id="uploaded_image" class="w-50 mt-xl-5"></div>
                                            </div>
                                            <div class="card-footer text-muted">
                                                <button class="crop_image">Crop Image</button>
                                            </div>
                                        </div>
                                        <!--  -->
                                    </div>
                                </div>
                            </div>
                            <div class="form-fields-group">
                                <p class="form_p_tag">College Information</p>
                                <div class="row">
                                    <div class="col-sm-6 form-group">
                                        <label for="medium">Medium:*</label>
                                        <select id="medium" onchange="selectionchange();" class="form-control custom-select browser-default" name="medium" required>
                                            <option value="" selected disabled>Please Select Medium</option>
                                            <option value="1">Science-English Medium</option>
                                            <option value="2">Science-Bangla Medium</option>
                                            <option value="4">Arts</option>
                                            <option value="6">Commerce</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6 form-group">
                                        <label for="batch year">Batch:*</label>
                                        <select id="batch-year" onchange="selectionchange2();" class="form-control custom-select browser-default" name="batch" required>
                                            <option value="" selected disabled>Please Select Batch</option>
                                            <option value="2001">2001</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6 form-group">
                                        <label for="group">Group:*</label>
                                        <select id="commerce" onchange="group_commerce(this.value);" class="form-control custom-select browser-default" name="commerce">
                                            <option value="" selected disabled>Please Select Group</option>
                                            <option value="1">Section A</option>
                                            <option value="2">Section B</option>
                                            <option value="3">Section C</option>
                                            <option value="4">Section D</option>
                                            <option value="5">Section E</option>
                                        </select>
                                        <select id="arts" onchange="group_arts(this.value);" class="form-control custom-select browser-default" name="arts">
                                            <option value="" selected disabled>Please Select Group</option>
                                            <option value="1">Section H</option>
                                            <option value="2">Section W</option>
                                            <option value="3">Section G</option>
                                        </select>
                                        <select id="science" onchange="group_science(this.value);" class="form-control custom-select browser-default" name="science">
                                            <option value="" selected disabled>Please Select Group</option>
                                            <option value="1">Section 1</option>
                                            <option value="2">Section 2</option>
                                            <option value="3">Section 3</option>
                                            <option value="4">Section 4</option>
                                            <option value="5">Section 5</option>
                                            <option value="6">Section 6</option>
                                            <option value="7">Section 7</option>
                                        </select>
                                        <!-- <input type="text" class="form-control" name="c_group" id="c_group" placeholder="" required> -->
                                    </div>
                                    <div class="col-sm-6 form-group roll_number">
                                        <label for="college roll number">College Roll Number:*</label>
                                        <div class="row">
                                            <div class="col-4">
                                                <input type="number" class="form-control" name="c_roll_number_disabled" id="c_roll_number_disabled" placeholder="" readonly>
                                            </div>
                                            <div class="col-8">
                                                <input type="number" class="form-control" name="c_roll_number" id="c_roll_number" placeholder="" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-fields-group">
                                <p class="form_p_tag">Address</p>
                                <div class="row">
                                    <div class="col-sm-6 form-group">
                                        <label for="address-1">Current Address:*</label>
                                        <textarea class="form-control" name="current_address" id="address-1" required></textarea>
                                    </div>
                                    <div class="col-sm-6 form-group">
                                        <label for="address-2">Parmanent Address</label>
                                        <textarea class="form-control" name="permanent_address" id="address-2"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-fields-group">
                                <p class="form_p_tag">Professional Information</p>
                                <div class="row">
                                    <div class="col-sm-6 form-group">
                                        <label for="emplyment type">Employment Type:*</label>
                                        <select id="employ_type" class="form-control browser-default custom-select" name="employ_type" required>
                                            <option value="Employed">Employed</option>
                                            <option value="Self_Employed">Self Employed</option>
                                            <option value="Business">Business</option>
                                        </select>

                                        <label for="current position">Current Position:*</label>
                                        <input type="text" class="form-control" name="current_position" id="current_position" placeholder="" required>

                                        <label for="current position">LinkedIn Profile Link</label>
                                        <input type="text" class="form-control" name="linkedIn_profile_link" id="linkedIn_profile_link" placeholder="">
                                    </div>
                                    <div class="col-sm-6 form-group">
                                        <label for="Company Name and Details">Company Details</label>
                                        <textarea class="form-control" name="company_n_details" id="company_n_details"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group d-flex justify-content-center">
                                <input type="submit" name="submit_form" class="btn btn-primary" value="Submit">
                                <!-- <input type="hidden" class="form-control" name="submit_form">
                                <input type="submit" class="btn btn-primary g-recaptcha form-submit-btn" data-sitekey="6Lc80lUpAAAAAFqW0x3LePopbnvnc36jkJUnHkAP" data-callback='onSubmit' data-action='submit' value="Submit"> -->
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <script>
                // for margin_top
                var i = 1;
                // group dropdown - science, arts, commerce
                var commerce = document.getElementById('commerce');
                commerce.disabled = true;
                var arts = document.getElementById('arts');
                arts.style.visibility = 'hidden';
                var science = document.getElementById('science');
                science.style.visibility = 'hidden';
                var group;

                // other fields disabled
                var batch_year = document.getElementById('batch-year');
                batch_year.disabled = true;
                // medium select
                function selectionchange() {
                    var e = document.getElementById("medium");
                    var str = e.options[e.selectedIndex].value;
                    group = str;
                    document.getElementById('c_roll_number_disabled').value = str;
                    batch_year.disabled = false;
                    if (group == 1 || group == 2) {
                        commerce.style.visibility = 'hidden';
                        science.style.visibility = 'visible';
                        arts.style.visibility = 'hidden';
                        science.required = true;

                        if (i == 1) {
                            document.getElementById("science").style.marginTop = "-75px";
                        } else if (i == 5) {
                            document.getElementById("science").style.marginTop = "-40px";
                        }
                    } else if (group == 4) {
                        commerce.style.visibility = 'hidden';
                        arts.style.visibility = 'visible';
                        science.style.visibility = 'hidden';
                        document.getElementById("arts").style.marginTop = "-37px";
                        arts.required = true;
                        i = 5;
                    } else {
                        commerce.disabled = false;
                        commerce.style.visibility = 'visible';
                        science.style.visibility = 'hidden';
                        arts.style.visibility = 'hidden';
                        commerce.required = true;
                    }
                    document.getElementById("batch-year").value = "";
                }
                // batch_year select
                var digit1;
                var digit2;
                var p = false;
                var q = false;

                function selectionchange2() {
                    var e = document.getElementById("batch-year");
                    var digit1 = e.options[e.selectedIndex].value[2];
                    var digit2 = e.options[e.selectedIndex].value[3];

                    document.getElementById('c_roll_number_disabled').value += digit1;
                    document.getElementById('c_roll_number_disabled').value += digit2;
                    p = true;
                    document.getElementById("science").value = "";
                    document.getElementById("commerce").value = "";
                    document.getElementById("arts").value = "";
                    q = false;
                }
                // group select
                function group_commerce(value) {
                    if (q == false) {
                        if (p == true) {
                            document.getElementById('c_roll_number_disabled').value += value;
                            q = true;
                        }
                    } else {
                        var element = document.getElementById('c_roll_number_disabled');

                        // Get the current value
                        var currentValue = element.value;

                        // Remove the last character
                        var newValue = currentValue.slice(0, -1);

                        // Update the value of the element
                        element.value = newValue;
                        document.getElementById('c_roll_number_disabled').value += value;
                        q = true;
                    }
                }

                function group_arts(value) {
                    if (q == false) {
                        if (p == true) {
                            document.getElementById('c_roll_number_disabled').value += value;
                            q = true;
                        }
                    } else {
                        var element = document.getElementById('c_roll_number_disabled');

                        // Get the current value
                        var currentValue = element.value;

                        // Remove the last character
                        var newValue = currentValue.slice(0, -1);

                        // Update the value of the element
                        element.value = newValue;
                        document.getElementById('c_roll_number_disabled').value += value;
                        q = true;
                    }
                }

                function group_science(value) {
                    if (q == false) {
                        if (p == true) {
                            document.getElementById('c_roll_number_disabled').value += value;
                            q = true;
                        }
                    } else {
                        var element = document.getElementById('c_roll_number_disabled');

                        // Get the current value
                        var currentValue = element.value;

                        // Remove the last character
                        var newValue = currentValue.slice(0, -1);

                        // Update the value of the element
                        element.value = newValue;
                        document.getElementById('c_roll_number_disabled').value += value;
                        q = true;
                    }
                }
            </script>
<?php
        }
    }
    // thank you page and form page vice-versa
    $_SESSION['submit_file'] = 0;
    unset($_SESSION['err_e']);
    unset($_SESSION['err_p']);
    unset($_SESSION['err_cr']);
    unset($_SESSION['err_ie']);
    unset($_SESSION['su']);
}


function enqueue_bootstrap()
{
    // Enqueue Bootstrap CSS
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css', array(), '5.0.2');

    wp_enqueue_style('cropper-style', 'https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.css', array(), '1.0', 'all');

    wp_enqueue_style('custom-style', plugins_url('css/style.css', __FILE__), array(), '1.0');

    wp_enqueue_script('cropper-js', 'https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js', array('jquery'), null, true);
    wp_enqueue_script('recaptcha-js', 'https://www.google.com/recaptcha/api.js?render=6Lc80lUpAAAAAFqW0x3LePopbnvnc36jkJUnHkAP', array('jquery'), null, true);

    wp_enqueue_script('jquery');
    wp_register_script('jqueryexample', 'https://code.jquery.com/jquery-3.7.1.min.js');
    wp_enqueue_script('jqueryexample');

    wp_enqueue_script('custom-script', plugin_dir_url(__FILE__) . 'js/ndc_form.js', array('jquery', 'cropper-js'), '', true);

    // Pass Ajax Url to script.js
    wp_localize_script('custom-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));

    wp_enqueue_script('custom-script');
}
add_action('wp_enqueue_scripts', 'enqueue_bootstrap');



$Cropped_image_path = '';
add_action('wp_ajax_crop_img', 'crop_img');
add_action('wp_ajax_nopriv_crop_img', 'crop_img');
function crop_img()
{
    if (isset($_POST['image1'])) {
        $data = $_POST['image1'];
        $croped_image = $data;
        list($type, $croped_image) = explode(';', $croped_image);
        list(, $croped_image)      = explode(',', $croped_image);
        $croped_image = base64_decode($croped_image);

        $imageName = time() . '.png';

        // file_put_contents(plugin_dir_path(__FILE__) . 'photos/' . $imageName, $croped_image);

        // $Cropped_image_path = plugin_dir_url(__FILE__) . 'photos/' . $imageName;

        $_SESSION['crop_img_path'] = plugin_dir_path(__FILE__) . 'photos/' . $imageName;
        $_SESSION['crop_data'] = $croped_image;
        $_SESSION['crop_img_url'] = plugin_dir_url(__FILE__) . 'photos/' . $imageName;
    }
}
?>