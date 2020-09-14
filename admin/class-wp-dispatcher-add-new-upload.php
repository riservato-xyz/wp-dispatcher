<?php

/**
 * Class to add new Upload.
 *
 * @link       ekn.dev
 * @since      1.0.0
 *
 * @package    Wp_Dispatcher
 * @subpackage Wp_Dispatcher/admin
 */


/**
 * Class WordPress_Plugin_Template_Settings
 *
 */
class Wp_Dispatcher_Add_New_Upload {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * This function introduces the theme options into the 'Appearance' menu and into a top-level
	 * 'WPPB Demo' menu.
	 */
	public function setup_plugin_sub_menu() {

    add_submenu_page(
      'wp_dispatcher',
      'Add New',
      'Add New',
      'manage_options',
      'wp_dispatcher_new',
      array( $this, 'render_new_upload_page_content'),				// The name of the function to call when rendering this menu's page

  );

	}

	/**
	 * Renders a simple page to display for the theme menu defined above.
	 */
	public function render_new_upload_page_content(  ) {

		?>
		<!-- Create a header in the default WordPress 'wrap' container -->
		<div class="wrap">

			<h2><?php _e( 'WP Dispatcher', 'wp-dispatcher' ); ?></h2>
      <?php settings_errors(); ?>
				<h2>Add new file</h2>

				<!-- Form to handle the upload - The enctype value here is very important -->
				<form action="<?php echo get_site_url() ?>/wp-admin/admin-post.php"  method="post" enctype="multipart/form-data">
					<input type="hidden" name="action" value="process_upload">
          <input type='file' id='file_upload' name='file_upload'></input>
          <?php submit_button('Upload') ?>
      </form>
   		
	</div><!-- /.wrap -->
	<?php
  }
  

  public function wp_dispatcher_process_upload(){
		// First check if the file appears on the _FILES array
		
		if($_FILES['file_upload']["error"] == 4) {
			wp_redirect( get_admin_url() . 'admin.php?page=wp_dispatcher_new&upload=empty');
			exit();
		}
		else {
			if(isset($_FILES['file_upload'])){

				$source = $_FILES['file_upload']['tmp_name'];
				
				$filename = $_FILES['file_upload']['name'];

        //$ext = pathinfo($_FILES['file_upload']['name'], PATHINFO_EXTENSION);
        //$uuid = uniqid();

        $destination = WP_CONTENT_DIR . '/uploads/wp-dispatcher/' . $filename;

        $uploaded = move_uploaded_file( $source, $destination );

        // Error checking using WP functions
        if(is_wp_error($uploaded)){
            echo "Error uploading file: " . $uploaded->get_error_message();
        }else{

					// Insert to data base
					global $wpdb;
					$table_name = $wpdb->prefix . 'dispatcher_uploads';

					$wpdb->insert( 
						$table_name, 
						array( 
							'date' => current_time( 'mysql' ), 
							'count' => 0,
							'author' => wp_get_current_user()->user_login,
							'filename' => $filename, 
						) 
					);

					wp_redirect( get_admin_url() . 'admin.php?page=wp_dispatcher&upload=success');
					exit();
        }
			}
		}
	}
}