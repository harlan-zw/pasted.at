<?php
/**
 * The only page that a user will interacte with. All that is done in the file is
 * to load the base of the lunor cms system, which loads everything needed.
 * @author Harlan Wilton
 * @since 1.0
 */
/* the name of the sites path */
$sitePath = dirname( __FILE__ );
/* the path to the lunor framework */
$lunorPath = dirname( __FILE__ ) . '/../lunor.php' 
/** We require the class so if there is an error we don't proceed */
require_once( $lunorPath );

?>