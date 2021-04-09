<?php

	Class fieldField_Group_Start extends Field {


	/*-------------------------------------------------------------------------
		Definition:
	-------------------------------------------------------------------------*/

		public function __construct(){
			parent::__construct();

			$this->_name = 'Group Start';
	        $this->set('show_column', 'no');
	        $this->set('required', 'no');
		}

	/*-------------------------------------------------------------------------
		Setup:
	-------------------------------------------------------------------------*/

		public function createTable(){
			return Symphony::Database()->query(
				"CREATE TABLE IF NOT EXISTS `tbl_entries_data_" . $this->get('id') . "` (
				  `id` int(11) unsigned NOT NULL auto_increment,
				  `entry_id` int(11) unsigned NOT NULL,
				  `value` double default NULL,
				  PRIMARY KEY  (`id`),
				  KEY `entry_id` (`entry_id`),
				  KEY `value` (`value`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
			);
		}

		/**
		 * Save field settings in section editor.
		 */
		public function commit() {
			if(!parent::commit()) return false;

			$id = $this->get('id');
			$handle = $this->handle();

			if($id === false) return false;

			$fields = array();
			$fields['field_id'] = $id;
			return FieldManager::saveSettings($id, $fields);
		}

		public function processRawFieldData($data, &$status, &$message = NULL, $simulate = false, $entry_id = NULL) {
			$status = self::__OK__;

			return array(
				'value' => ''
			);
		}

		/**
		 * Exclude field from DS output.
		 */
		public function fetchIncludableElements() {
 			return null;
 		}

		public function prepareReadableValue($data, $entry_id = NULL, $truncate = false, $defaultValue = NULL) {
			return $this->prepareTableValue($data, null, $entry_id);
		}

		public function prepareTableValue($data, XMLElement $link=NULL, $entry_id=NULL) {
			// build this entry fully
			$entries = EntryManager::fetch($entry_id);

			if ($entries === false) return parent::prepareTableValue(NULL, $link, $entry_id);

			$entry = reset(EntryManager::fetch($entry_id));

			// get the first field inside this tab
			$field_id = Symphony::Database()->fetchVar('id', 0, "SELECT `id` FROM `tbl_fields` WHERE `parent_section` = '".$this->get('parent_section')."' AND `sortorder` = ".($this->get('sortorder') + 1)." ORDER BY `sortorder` LIMIT 1");

			if ($field_id === NULL) return parent::prepareTableValue(NULL, $link, $entry_id);

			$field = FieldManager::fetch($field_id);

			// get the first field's value as a substitude for the tab's return value
			return $field->prepareTableValue($entry->getData($field_id), $link, $entry_id);
		}
	}