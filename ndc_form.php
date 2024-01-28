<?php
/*
 Plugin Name: NDC Form by TS
 Description: A Plugin for ndc student's form
 Version: 1.10.3
 Author: Feroz
 */

define('PLUGIN__DIR', plugin_dir_path(__FILE__));

session_start();
// Database Create
global $wpdb;
$table = $wpdb->prefix . 'ndc_form_details';
if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
    $sql = "CREATE TABLE $table (
        `id` int(11) NOT NULL,
        `f_name` varchar(50) NOT NULL,
        `l_name` varchar(50) NOT NULL,
        `n_name` varchar(50) NOT NULL,
        `phone` varchar(50) NOT NULL,
        `email` varchar(50) NOT NULL,
        `status` int(11) NOT NULL,
        `blood_grp` varchar(50) NOT NULL,
        `image` varchar(100) NOT NULL,
        `batch` int(11) NOT NULL,
        `college_roll` int(11) NOT NULL,
        `medium` varchar(50) NOT NULL,
        `group` varchar(50) NOT NULL,
        `current_address` text NOT NULL,
        `permanent_address` text NOT NULL,
        `employment_type` varchar(50) NOT NULL,
        `current_position` varchar(50) NOT NULL,
        `company_name_details` text NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    $sql1 = "ALTER TABLE $table ADD PRIMARY KEY (`id`);";
    $sql2 = "ALTER TABLE $table MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
    $wpdb->query($sql);
    $wpdb->query($sql1);
    $wpdb->query($sql2);
    $sql3 = "ALTER TABLE $table ADD generate_number VARCHAR(255);";
    $wpdb->query($sql3);
    $sql4 = "ALTER TABLE $table ADD linkedIn_profile_link VARCHAR(200) AFTER company_name_details;";
    $wpdb->query($sql4);

    // wp_option
    $my_data = '1';
    update_option('settings_verification', $my_data, 'yes');
}
$sql6 = "ALTER TABLE $table ADD status int(11);";
$wpdb->query($sql6);

add_action('admin_menu', 'ndc_form_menu');
function ndc_form_menu()
{
    add_menu_page('ndc_form_menu', 'NDC FORM', 9, PLUGIN__DIR, 'ndc_form_function');
}


function enqueue_bootstrap_admin_style()
{
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css');
    wp_enqueue_style('bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.19.0/font/bootstrap-icons.css');
}

add_action('admin_enqueue_scripts', 'enqueue_bootstrap_admin_style');

function ndc_form_function()
{
?>

    <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__) ?>css/style.css">
    <div class="p-4 mt-5">
        <h1 class="text-center shadow p-4 text-info">Notredame College : Student Information Table</h1>
        <div class="pt-3"></div>
        <!-- Settings for verification -->
        <div class="d-flex justify-content-start pe-5">
            <form action="" method="POST">
                <label for="emplyment type">Settings</label>
                <select id="settings_p" class="form-control browser-default custom-select" name="settings_p">
                    <?php $res = get_option('settings_verification'); ?>
                    <option value="1" <?php if ($res == 1) { ?>selected<?php } ?>>With Verification</option>
                    <option value="2" <?php if ($res == 2) { ?>selected<?php } ?>>Without Verification</option>
                </select>
                <div class="pt-1"></div>
                <input type="submit" name="ss" value="Go" />
            </form>
        </div>
        <div class="pb-4"><?php custom_export_page(); ?></div>
        <table class="table table-success table-striped ndc_admin_table">
            <thead>
                <tr class="text-center">
                    <th width="1%" scope="col">ID</th>
                    <th width="4%" scope="col">First name</th>
                    <th width="4%" scope="col">Last name</th>
                    <th width="2%" scope="col">Nick name</th>
                    <th width="3%" scope="col">Phone</th>
                    <th width="4%" scope="col">Email</th>
                    <th width="4%" scope="col">Blood Group</th>
                    <th width="3%" scope="col">Batch</th>
                    <th width="4%" scope="col">College Roll number</th>
                    <th width="5%" scope="col">Medium</th>
                    <th width="4%" scope="col">Group</th>
                    <th width="5%" scope="col">Current Address</th>
                    <th width="5%" scope="col">Permanent Address</th>
                    <th width="4%" scope="col">Employment Type</th>
                    <th width="4%" scope="col">Current Position</th>
                    <th width="6%" scope="col">Company name & Details</th>
                    <th width="6%" scope="col">LinkedIn Profile Link</th>
                    <th width="3%" scope="col">Status</th>
                    <th width="5%" scope="col"></th>
                    <th width="2%" scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $plugins_path = plugin_dir_path(__FILE__);
                $plugins_url = plugins_url('/', __FILE__);
                $img_gallery_url = admin_url('admin.php?page=NDC_FORM');
                global $wpdb;
                $table = $wpdb->prefix . 'ndc_form_details';

                $per_page = 4; // images per page -  Pagination
                $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
                $offset = ($current_page - 1) * $per_page;
                $a = $offset + 1;
                $results = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT * FROM $table  LIMIT %d OFFSET %d;",
                        $per_page,
                        $offset
                    )
                );
                foreach ($results as $row) :
                ?>
                    <tr class="text-center">
                        <th><?php echo $a++; ?></th>
                        <td><?php echo $row->f_name; ?></td>
                        <td><?php echo $row->l_name; ?></td>
                        <td><?php echo $row->n_name; ?></td>
                        <td><?php echo $row->phone; ?></td>
                        <td><?php echo $row->email; ?></td>
                        <td><?php echo $row->blood_grp; ?></td>
                        <td><?php echo $row->batch; ?></td>
                        <td><?php echo $row->college_roll; ?></td>
                        <td><?php if($row->medium == 1) echo "Science-English Medium"; else if($row->medium == 2) echo "Science-Bangla Medium"; else if($row->medium == 4) echo "Arts"; else echo "Commerce"; ?></td>
                        <td><?php if($row->medium == 1 || $row->medium == 2) { if($row->group == 1) echo "Section 1"; else if($row->group == 2) echo "Section 2"; else if($row->group == 3) echo "Section 3"; else if($row->group == 4) echo "Section 4"; else if($row->group == 5) echo "Section 5"; else if($row->group == 6) echo "Section 6"; if($row->group == 7) echo "Section 7";} else if($row->medium == 4) { if($row->group == 1) echo "Section H"; else if($row->group == 2) echo "Section W"; else if($row->group == 3) echo "Section G"; } else if($row->medium == 6) { if($row->group == 1) echo "Section A"; else if($row->group == 2) echo "Section B"; else if($row->group == 3) echo "Section C"; else if($row->group == 4) echo "Section D"; else if($row->group == 5) echo "Section E"; } ?></td>
                        <td><?php echo $row->current_address; ?></td>
                        <td><?php echo $row->permanent_address; ?></td>
                        <td><?php echo $row->employment_type; ?></td>
                        <td><?php echo $row->current_position; ?></td>
                        <td><?php echo $row->company_name_details; ?></td>
                        <td><?php echo $row->linkedIn_profile_link; ?></td>
                        <td><?php if ($row->status == 0) echo "inactive";
                            else echo "active"; ?></td>
                        <td><img src="<?php echo $row->image; ?>" class="w-75" alt=""></td>
                        <td class="pt-1"><a href="<?php echo $plugins_url; ?>ndc_delete.php?del=<?php echo $row->id; ?>" class="text-decoration-none text-white"><button class="btn btn-danger"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
                                        <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0" />
                                    </svg>
                                </button></a></td>
                    </tr>
                <?php
                endforeach;
                ?>
            </tbody>
        </table>
        <?php
        // Pagination
        $total_images = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        $total_pages = ceil($total_images / $per_page);

        echo '<div class="B_pagination">';
        for ($i = 1; $i <= $total_pages; $i++) {
            $active_class = ($i == $current_page) ? 'active' : '';
            echo '<a href="' . esc_url(add_query_arg('paged', $i)) . '" class="' . $active_class . '">' . $i . '</a>';
        }
        echo '</div>';
        ?>
    </div>
<?php
}
// submit settings  
// if(!isset($_SESSION['settings_ad'])) {
//     $_SESSION['settings_ad'] = 1;
// }
if (isset($_POST['ss'])) {
    $settings_verification = $_POST['settings_p'];
    update_option('settings_verification', $settings_verification);
}
function custom_export_page()
{
?>
    <div class="d-flex justify-content-end">
        <form method="post" action="">
            <?php
            // Add nonce for security
            wp_nonce_field('custom_export_nonce', 'custom_export_nonce');
            ?>
            <input type="submit" class="button button-primary" name="export_data" value="Export Data">
        </form>
    </div>
<?php
}

// Function to handle data export
function custom_export_data()
{
    // Check if the export button is clicked and the nonce is valid
    if (isset($_POST['export_data']) && check_admin_referer('custom_export_nonce', 'custom_export_nonce')) {
        // Get data from the WordPress database (customize this query as needed)
        $data = get_results_from_database();

        // Generate CSV content
        $csv_content = 'ID,First name,Last name,Nick name,Phone,Email,status,Blood Group,Image,Batch,College Roll number,Medium,Group,Current Address,Permanent Address,Employment Type,Current Position,Company name & Details,linkedIn_profile_link' . "\r\n";
        foreach ($data as $row) {
            $csv_content .= implode(',', $row) . "\r\n";
        }

        // Send CSV as a download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=export.csv');
        echo $csv_content;
        exit;
    }
}
add_action('admin_init', 'custom_export_data');

// Function to fetch data from the WordPress database (customize this function as needed)
function get_results_from_database()
{
    global $wpdb;

    // Example query: Get all posts
    $query = "SELECT * FROM {$wpdb->prefix}ndc_form_details";
    $results = $wpdb->get_results($query, ARRAY_A);

    foreach ($results as $key => $subArr) {
        unset($results[$key]['generate_number']);
        if ($results[$key]['status'] == 0) {
            $results[$key]['status'] = 'inactive';
        }
        if ($results[$key]['status'] == 1) {
            $results[$key]['status'] = 'active';
        }
    }

    return $results;
}


include("ndc_form_shortcode.php");
