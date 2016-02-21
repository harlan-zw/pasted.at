<?php

class PasteHandler_PasteAPI {

	

	public static function checkForExpired(&$return) {
		/* select all pastes which expire date is > then current date */
		
	}

	public static function delete(&$return, $id = false) {
		/* if an id is supplied then we can presume it's a request from the server and not a client, no need to check privelages */
		if ($id === false) {
			/* check logged in */
			if (Account_AccountAPI::getLoggedIn() === false) {
				$return['error'] = 'You need to be logged in to delete a paste.';
				return;
			}
			$pasteId = $_POST['paste_id'];
			if (!isset($pasteId) && empty($pasteId)) {
				$return['error'] = 'You must supply a paste id.';
				return;
			}

			/* check can delete */
			$res = Lunor::$base->dbi->select(PASTE_TABLE_PREFIX .'user_paste')->where(array('paste_id' => $pasteId, 'user_id' => Account_AccountAPI::getUserId()))->go();
			if ($res === false || empty($res)) {
				$return['error'] = 'This paste either does not exist, or does not belong to you.';
				return;
			}
		}
		/* delete */
		Lunor::$base->dbi->delete(array('id' => $id === false ? $pasteId : $id), PASTE_TABLE_PREFIX . 'paste')->go();
		$return['sucess'] = true;
	}

	public static function upload(&$return) {
		if (!isset($_POST['meta']) || empty($_POST['meta'])) {
			$return['sucess'] = false;
			$return['error'] = 'Please provide a meta type for the file upload (TEXT, FILE).';
			return;
		}
		$meta = strtoupper($_POST['meta']);
		if ($meta != 'FILE' && $meta != 'TEXT') {
			$return['sucess'] = false;
			$return['error'] = 'Invalid meta type! : ' . $meta;
			return;
		}
		if ($meta == 'FILE') {
			$err = isValidFile(PASTE_FILE_TMP_NAME, PASTE_MAX_LENGTH_MB * 1000 * 1000);
			if ($err != true) {
				$return['sucess'] = false;
				$return['error'] = 'Bad file! Error: ' . $err;
				return;
			}
		}
		/* ensure that no output is sent to the json output */
		/* create and fill the paste object */
		$paste = new PasteHandler_Paste();
		$paste->fill($_POST);
		$paste->type = $meta;
		if ($meta == 'FILE') {
			$fileName = $_FILES[PASTE_FILE_TMP_NAME]['name'];
			if(!get_magic_quotes_gpc()) {
				$fileName = addslashes($fileName);
			}
			$dir = currentSite()->relativePath . 'uploads/';
			$permFile = $dir . getRandomString() . '_' . $fileName;
			$tmp = $_FILES[PASTE_FILE_TMP_NAME]["tmp_name"];
			/* change name so there's no colissions if someone uploads a file named the same thing */
		//	$_FILES[PASTE_FILE_TMP_NAME]["tmp_name"] = $_FILES[PASTE_FILE_TMP_NAME]["name"] = getRandomString() . $_FILES[PASTE_FILE_TMP_NAME]["name"] ;
			/* move the file out of the tmp directory */
			move_uploaded_file($tmp, $permFile);
			//echo 'tmp: ' . $tmp;
			//echo 'path : '  . currentSite()->absolutePath . 'uploads/' . getRandomString() . $_FILES[PASTE_FILE_TMP_NAME]["name"];
			if (exif_imagetype ($permFile ) !== false) {
				$paste->type = 'IMAGE';
			}
			$paste->datapath = $permFile;
			
			$paste->title = $fileName;
		}

		if (strlen($paste->data) > PASTE_MAX_LENGTH_MB * 1000 * 1000) {
			$return['sucess'] = false;
			$return['error'] = 'Paste too big. Please paste below ' . PASTE_MAX_LENGTH_MB . 'MB of data!';
			return;
		}
		if ($paste->exposure === 'private' && !Account_AccountAPI::getLoggedIn()) { 
			$return['sucess'] = false;
			$return['error'] = 'You cannot use private pastes without being logged in.';
			return;
		}
		$longIP =  findIPLong();

		/* check that this ip has not submitted more then the limit of pastes in last hour */
		$res = Lunor::$base->db->query('SELECT id from ' . TABLE_PREFIX . PASTE_TABLE_PREFIX . 'paste where `ip` = \'' . $longIP . '\' and DATE_SUB(NOW(), INTERVAL 1 HOUR) <= `since`;');
		if ($res !== false) {
			$res = Lunor::$base->dbi->fetchAll($res);
			$entries = sizeof($res);
			if ($entries >= PASTE_MAX_UPLOADS_PER_HOUR) {
				$return['sucess'] = false;
				$return['error'] = 'You have pasted too many items within the last hour. Please slow down. This is in place to stop spam bots.';
				return;
			}
		}


		/* give generated id */
		$paste->id = self::generateUID();

		$cur = new ORM_Operator(new PasteHandler_Paste(), array('id' => $paste->id));
		while (!$cur->isEmpty()) {
			$paste->id = self::generateUID();
			$cur = new ORM_Operator(new PasteHandler_Paste(), array('id' => $paste->id));
		}

		$paste->views = 0;
		$paste->since = 'CURRENT_TIMESTAMP';
		$paste->ip = $longIP;
		/* inset data into db */
		Lunor::$base->dbi->beginTransaction();
		Lunor::$base->dbi->setAdditionalPrefix(PASTE_TABLE_PREFIX);
		/*	insert base class into the db */
		$paste->insert();
		/* now need to put in variable table data */
		if ($_POST['expiration'] === 'views') {
			/* we are using the views table*/
			Lunor::$base->dbi->insert('expiration_views')->map(array('paste_id' => $paste->id,'view_limit' => $_POST['views']))->go();
		} else {
			/* otherwise we use the time table */
			Lunor::$base->dbi->insert('expiration_time')->map(array('paste_id' => $paste->id,'expires' => self::getTimestamp()))->go();
		}
		if ($_POST['meta'] === 'text') {
			/* if type is text we need to insert for the syntax highlighting */
			Lunor::$base->dbi->insert('paste_text')->map(array('paste_id' => $paste->id, 'syntax_highlighting' => $_POST['paste_mode']))->go();
		}
		/* adds our ip to the viewed list so we don't increment it ourself */
		Lunor::$base->dbi->insert('paste_view')->map(array('paste_id' => $paste->id, 'ip_address' => $longIP))->go();

		/* poster is not Guest! */
		if (Account_AccountAPI::getLoggedIn()) {
			Lunor::$base->dbi->insert('user_paste')->map(array('paste_id' => $paste->id, 'user_id' => Account_AccountAPI::getUserId()))->go();
		}

		/* if the transaction has to rollback then we let the client know it failed */
		if (Lunor::$base->dbi->endTransaction() === false) {
			$return['sucess'] = false;
			return;
		}
		$return['id'] = $paste->id;
		$return['sucess'] = true;
		return;
	}


	public static function incrementViews($paste) {
		$longIP =  findIPLong();
		/* need to select from user_views where ip adress  = ours and paste id */
		/* we need to add in an initial view for the creator ip without incrmentingf count */
		$row = Lunor::$base->dbi->selectAllFrom(PASTE_TABLE_PREFIX. 'paste_view', array('paste_id' => $paste->id, 'ip_address' => $longIP));
		if (empty($row)) {
			Lunor::$base->dbi->beginTransaction();
			Lunor::$base->dbi->setAdditionalPrefix(PASTE_TABLE_PREFIX);
			/* the user hasn't view this paste before ! increment count */
			$paste->views++;
			Lunor::$base->dbi->update('paste')->map(array('views' => $paste->views))->where(array('id' => $paste->id))->go();
			Lunor::$base->dbi->insert('paste_view')->map(array('paste_id' => $paste->id, 'ip_address' => $longIP))->go();
			Lunor::$base->dbi->endTransaction();

		}
	}

	public static function findSyntax($paste) {
		if ($paste->type === 'TEXT') {
			$row = Lunor::$base->dbi->selectAllFrom(PASTE_TABLE_PREFIX . 'paste_text', array('paste_id' => $paste->id));
			if (empty($row))
				return 'Text';
			else
				return $row[0]['syntax_highlighting'];
		} else {
			Lunor::$base->logger->warning('Syntax requested for invalid paste type: ' . $paste->type);
		}
		return false;
	}
	public static function findAuthor($paste) {
		/* join table user_paste with user on user id for paste id */
		$res = Lunor::$base->dbi->selectAllFrom(PASTE_TABLE_PREFIX . 'user_paste', array('paste_id' => $paste->id));
		if ($res !== false) {
			$row = $res[0];
			$user = new Account_User();
			$user->forId('account_user', $row['user_id']);
			if (isset($user->name) && !empty($user->name))
				return $user->name;
		}
		return 'Guest';
	}

	public static function getRecentPublic($amt, $loop) {
		$res = Lunor::$base->dbi->select('pastehandler_paste')
		->where(array('exposure' => 'public'))
			->sortDesc('since')//make the earlier pastes appear first
			->limit($amt)
			->go();
			/* all of the paste ids belonging to the user */
			if ($res !== false && !empty($res)) {
				foreach ($res as $value) {
					$paste = new PasteHandler_Paste();
					$paste->fill($value);
					$loop($paste);
					
				}
			}
		}

		public static function getMostPopular($amt, $loop) {
			$res = Lunor::$base->dbi->select('pastehandler_paste')
			->where(array('exposure' => 'public'))
			->sortDesc('views')//make the earlier pastes appear first
			->limit($amt)
			->go();
			/* all of the paste ids belonging to the user */
			if ($res !== false && !empty($res)) {
				foreach ($res as $value) {
					$paste = new PasteHandler_Paste();
					$paste->fill($value);
					$loop($paste);
					
				}
			}
		}

		public static function getPastesForUser($userId = false) {
			if ($userId === false) {
				/* we use logged in user */
				$res = Lunor::$base->dbi->select('pastehandler_user_paste up')
				->join(array('pastehandler_paste p' => array('up.paste_id', 'p.id')))
				->where(array('user_id' => Account_AccountAPI::getUserId()))
			->sortDesc('since')//make the earlier pastes appear first
			->go();
			/* all of the paste ids belonging to the user */
			if ($res !== false && !empty($res)) {
				return $res;
			}
		}
		return false;
	}

	public static function forId($id) {
		$paste = new PasteHandler_Paste();
		$paste->forId(PASTE_TABLE_PREFIX . 'paste', $id);
		if ($paste->id === false)
			return false;
		/* check privelages to see it */
		if ($paste->exposure === 'private') {
			if (Account_AccountAPI::getLoggedIn() === false || $paste->findAuthor() !== Account_AccountAPI::getUsername()) {
				/* it is private to ourself and we are looking at it */
				Lunor::$base->router->throwError('private');
				return true;
			}
		}
		/* check view/data limit */
		$res = Lunor::$base->dbi->select(PASTE_TABLE_PREFIX . 'expiration_views')->where(array('paste_id' => $paste->id))->go();
		/* too many people have viewed it, delete paste! */
		if ($res !== false && !empty($res) && $paste->views >= $res[0]['view_limit']) {
			self::delete($return, $paste->id);
			Lunor::$base->router->throwError(404);
			return true;
		}

		return $paste;
	}

	private static function getTimestamp() {
		$d = new DateTime('UTC');
		$exp = $_POST['expiration'];
		if ($exp === 'forever') {
			$d->modify('+5 years');
		} else {
			$exp = str_replace('_', ' ', $exp);
			$d->modify('+' . $exp);
		}
		return $d->format("Y/m/d m:i:s");
	}

	private static function generateUID() {
		return getRandomString(PASTE_ID_LENGTH);
	}

	

}


?>