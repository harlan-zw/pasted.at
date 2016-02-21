<?php
/* add in custom route */
Routing::custom('p', function($extra) {
	$return = array();
	$type = $_POST['type'];
	if (isset($type) && !empty($type)) {
		if ($type == 'upload') {
			PasteHandler_PasteAPI::upload($return);
		} else if ($type == 'DELETE') {
			PasteHandler_PasteAPI::delete($return);
		} else if ($type == 'CHECK_FOR_DELETE') {
			PasteHandler_PasteAPI::checkForExpired($return);
		} else {
			$return['error'] = 'Invalid request type!';
		}
		echo json_encode($return);
		return true;
	}
	if ($extra != false) {
		$pasteId = $extra;
		/* get the paste for the id supplied within the extra */
		$paste = PasteHandler_PasteAPI::forId($pasteId);
		if ($paste === true) {
			/* paste threw its one error when fetching id */
			return true;
		} else 
		if ($paste  === false) {
			Lunor::$base->router->throwError(404);
			return true;
		}
		/* need to call this to check for expires pastes */
		//PasteHandler_ExpirePastes::go();
		Plugin_Hook::createTemp('page_title', function($current) use ($paste, $pasteId) {
			if (empty($paste->title))
				return 'pasted.at - ' . $pasteId;
			else
				return 'pasted.at - ' . $paste->title;
		});
		$paste->incrementViewCount();
		$GLOBALS['viewingpaste'] = $paste;
		if ($paste->type === 'TEXT') {
			/* we create a hook that'll modify the pages title */
			redirect('text');
			Lunor::$base->router->forPage('text');
			return true;
		} else if ($paste->type === 'IMAGE') {
			redirect('image');
			Lunor::$base->router->forPage('image');
			return true;
		} else {
			redirect('file');
			Lunor::$base->router->forPage('file');
			return true;
		}
	}
});
Routing::custom('f', function($extra) {
	if ($extra != false) {
		$pasteId = $extra;
		/* get the paste for the id supplied within the extra */
		$paste = PasteHandler_PasteAPI::forId($pasteId);
		if ($paste === true) {
			/* paste threw its one error when fetching id */
			return true;
		} else 
		if ($paste  === false) {
			Lunor::$base->router->throwError(404);
			return true;
		}
		if (file_exists($paste->datapath)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.$paste->title);
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($paste->datapath));
			ob_clean();
			flush();
			readfile($paste->datapath);
			exit;
		}
		return true;
	}
});
?>