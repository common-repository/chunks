<?php
/*
Plugin Name: Chunks
Plugin URI: http://kovshenin.com/wordpress/plugins/chunks/
Description: Chunks
Author: Konstantin Kovshenin
Version: 1.1
Author URI: http://kovshenin.com/
*/

/*
 * Chunks Class
 * 
 * All the functionality is inside here.
 */
class Chunks {
	var $options = array();
	var $defaults = array();
	var $chunks = array();
	var $values = array();
	
	/*
	 * Chunks Constructor
	 * 
	 * Fired during 'plugins_loaded' action, should add the
	 * rest of the actions (if any).
	 */
	function __construct() {
		
		// Use this prefix to store chunks based on current theme.
		$this->prefix = 'chunks-' . sanitize_title( get_current_theme() );
		
		// Let's add some actions
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
		
		// The [chunk key="key"] shortcode
		add_shortcode( 'chunk', array( &$this, 'shortcode' ) );
	}
	
	/*
	 * Chunk shortcode
	 *
	 * Allows the usage of the [chunk] shortcode inside posts
	 * and pages as well as other areas where shortcodes are
	 * interpreted.
	 *
	 * @uses chunk()
	 */
	function shortcode( $atts ) {
		extract( shortcode_atts( array(
			'key' => '',
			'default' => '', 
		), $atts ) );
		
		return chunk( $key, $default );
	}
	
	/*
	 * Admin Menu Builder
	 * 
	 * Adds an administrative menu under the Appearance section called
	 * Theme Chunks. Also makes sure that chunks are loaded from
	 * the database.
	 * 
	 * @uses add_theme_page
	 */
	function admin_menu() {
		add_theme_page( 'Theme Chunks', 'Theme Chunks', 'manage_options', 'chunks-plugin', array( &$this, 'theme_page' ) );
		$this->load_chunks();
	}
	
	/*
	 * Save Chunks
	 * 
	 * Saves the chunks values array to the database, using the
	 * theme prefix.
	 * 
	 * @uses update_option
	 */
	function save_chunks() {
		update_option( $this->prefix . '-values', $this->values );
	}
	
	/*
	 * Load Chunks
	 * 
	 * Loads the chunks values array from the database or cache
	 * using the active theme prefix.
	 * 
	 * @uses get_option
	 */
	function load_chunks() {
		$this->values = (array) get_option( $this->prefix . '-values' );
	}
	
	/*
	 * Register Chunks
	 * 
	 * This function is used from outside this class by a register_chunks
	 * function of it's own. Populates the chunks array.
	 */
	function register_chunks( $array ) {
		if ( ! is_array( $array ) ) return false;
		$this->chunks = $array;
	}
	
	/*
	 * Chunks interpreter
	 *
	 * The actual chunks interpreter, pass in a key and a default
	 * value and this function will return the value of the chunk
	 * if set, otherwise the default value passed in.
	 */
	function chunk( $key, $default = '' ) {
		$this->load_chunks();
		
		if (WP_DEBUG) {
			if ( !isset( $this->chunks[$key] ) ) return "Chunk not set: {$key}";
			if ( !isset( $this->values[$key] ) ) return "Value for chunk not set: {$key}";
		}
		
		if ( isset( $this->chunks[ $key ] ) && isset( $this->values[ $key ] ) )
			return $this->values[ $key ];
			
		return $default;

	}
	
	/*
	 * Theme Options
	 * 
	 * Theme Chunks page in the administrative area. Handles list,
	 * edit and save actions.
	 */
	function theme_page() {
		if ( isset( $_POST[ 'edit-chunks-submit' ] ) ) {
			$key = $_POST[ 'edit-chunks-submit' ];
			if ( isset( $this->chunks[ $key ] ) ) {
				$value = stripslashes( $_POST[ 'chunk-value' ] );
				if ( strlen( $value ) > 0) {
					$this->values[ $key ] = $value;
					$this->save_chunks();
					$updated = true;
				} else {
					unset( $this->values[ $key ] );
					$this->save_chunks();
					$updated = true;
				}
			}
		}
	?>
<div class="wrap">
	<div id="icon-themes" class="icon32"><br></div>
	<h2><?php _e( 'Theme Chunks', 'chunks' ); ?></h2>
	
	<?php if ( isset( $updated ) ): ?>
	<div id="message" class="updated below-h2"><p><?php _e( 'Chunk updated.', 'chunks' ); ?></p></div>
	<?php endif; ?>
	
	<p>Below is a list of chunks that <strong><?php echo get_current_theme(); ?></strong> has registered. Learn more about <a href="http://kovshenin.com/wordpress/plugins/chunks/">Using Chunks</a>.</p>

	<table class="widefat">
	<thead>
		<tr>
			<th width="30%">Key</th>
			<th>Value</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Key</th>
			<th>Value</th>
		</tr>
	</tfoot>
	<tbody>
<?php
	foreach ( $this->chunks as $key => $description ):
		if ( isset( $this->values[ $key ] ) )
			$value = substr( htmlspecialchars( $this->values[ $key ] ), 0, 400)  . '...';
		else
			$value = 'No value set';
?>
	   <tr>
		 <td><strong><a href="?page=chunks-plugin&action=edit&key=<?php echo $key; ?>#edit" class="row-title"><?php echo $key; ?></a></strong><br /><span class="description"><?php echo $description; ?></span></td>
		 <td><?php echo $value; ?></td>
	   </tr>
<?php
	endforeach;
?>
	</tbody>
	</table>
	
<?php
	if ( isset( $_GET['action'] ) && $_GET[ 'action' ] == 'edit' && isset( $_GET['key'] ) && isset( $this->chunks[ $key ] ) ) :
?>
	<a name="edit">&nbsp;</a>
	<h2>Editing &quot;<?php echo $_GET[ 'key' ]; ?>&quot;:</h2>
	<form action="?page=chunks-plugin" method="POST">
		<input type="hidden" name="edit-chunks-submit" value="<?php echo $_GET['key']; ?>" />
		
		<table class="form-table">
		<tbody>

			<tr valign="top">
				<th scope="row"><label for="upload_url_path"><?php _e( 'Chunk key', 'chunks' ); ?></label></th>
				<td>
					<p style="margin-top: 4px;"><?php echo $_GET[ 'key' ]; ?></p>
					<span class="description"><?php echo $this->chunks[ $_GET[ 'key' ] ]; ?></span>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="upload_url_path">Chunk value</label></th>
				<td>
					<textarea name="chunk-value" rows="5" cols="60" /><?php if ( isset( $this->values[ $_GET[ 'key' ] ] ) ) echo htmlspecialchars( $this->values[ $_GET[ 'key' ] ] ); ?></textarea><br />
					<span class="description"><?php _e( 'Note: chunk values are not escaped or sanitized, you can use HTML, javascript and the rest.', 'chunks' ); ?></span>
				</td>
			</tr>

		</tbody></table>
		
		<p class="submit">
			<input type="submit" name="Submit" class="button-primary" value="Save Changes"> or <a href="?page=chunks-plugin">Cancel</a>
		</p>
	</form>
<?php
	endif;
?>
</div>
	<?php
	}
};

/*
 * Register Chunks Function
 * 
 * Used outside of this plugin to register chunks for a theme (via
 * functions.php).
 * 
 * @global $chunks
 */
if ( ! function_exists( 'register_chunks' ) ) {
	function register_chunks( $array ) {
		global $chunks;
		return $chunks->register_chunks( $array );
	}
}

/*
 * Chunk Function
 * 
 * Used to request a chunk from the database using a $key and an (optional)
 * $default value. If WP_DEBUG is switched on, warning messages are
 * returned.
 * 
 * @global $chunks
 */
if ( ! function_exists( 'chunk' ) ) {
	function chunk( $key, $default='' ) {
		global $chunks;
		return $chunks->chunk( $key, $default );
	}
}

// Fire up the global $chunks;
add_action( 'plugins_loaded', create_function( '', 'global $chunks; $chunks = new Chunks();' ) );
