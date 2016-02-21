<?php 

class PasteHandler_Paste extends ORM_Model {


	public $id = false;
	public $exposure = 'public';
	public $title = 'default';
	public $type;
	public $since = null;
	public $views = null;
	public $data;
	public $datapath;
	/* ip of the paster ! */
	public $ip;

	public function findDataLength() {
		if (!empty($this->data) && isset($this->data)) {
			$len = strlen($this->data);
		} else {
			$len = filesize ($this->datapath);
		}
		if ($len > 1000000) {
			$len /= 1000000;
			$len .= ' MB';
		}
		else if ($len > 1000) {
			$len /= 1000;
			$len .= ' KB';
		}
		else {
			$len .= ' B';
		}
		return $len;
	}
	public function getTitle() {
		if (empty($this->title))
			return 'Untitled';
		return htmlspecialchars($this->title);
	}

	public function getTextualSince() {
		return date("d M, Y", strtotime($this->since));
	}
	
	public function incrementViewCount() {
		return PasteHandler_PasteAPI::incrementViews($this);
	}

	public function getSyntax() {
		return htmlspecialchars(PasteHandler_PasteAPI::findSyntax($this));
	}

	public function findAuthor() {
		return htmlspecialchars(PasteHandler_PasteAPI::findAuthor($this));
	}

	public function getFormattedData() {
		return htmlspecialchars(str_replace('</br>', "\n", $this->data));
	}
}

?>