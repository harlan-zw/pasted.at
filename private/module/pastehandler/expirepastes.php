<?php
/**
 * This code should be ran by a CRON job set to run approximately every hour.
 * The purpose of this code is to find any pastes which have expired and delete them from the db.
 * This code needs to be executed through the lunor router.
 */

/* first we load in ALL paste ids, views and expiriation info */
class PasteHandler_ExpirePastes {

	public static function go() {
		/* delete all submissions that have been online longer then the expiration date */
		Lunor::$base->db->query('delete p FROM harlan_pastehandler_paste p JOIN harlan_pastehandler_expiration_time et ON et.paste_id = p.id where et.expires < CURRENT_TIMESTAMP;');
		/* delete all submissions which have more views then their view limit */
		Lunor::$base->db->query('delete p FROM harlan_pastehandler_paste p JOIN harlan_pastehandler_expiration_views ev ON ev.paste_id = p.id where p.views >= ev.view_limit;');
	}

}
?>