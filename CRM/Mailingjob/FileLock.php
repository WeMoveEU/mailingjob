<?php

/**
 * LockInterface implementation that relies on lock files and the PHP function flock.
 */
class CRM_MailingJob_FileLock implements \Civi\Core\Lock\LockInterface {
  protected $_name;
  protected $_locked;
  protected $_path;
  protected $_fh; // File handle

  public function __construct($name) {
    $this->_name = $name;
    $locksDir = defined('CIVICRM_FILELOCK_DIR') ? CIVICRM_FILELOCK_DIR : (CRM_Utils_File::baseFilePath() . 'locks' . DIRECTORY_SEPARATOR);
    $this->_path = $locksDir . $name;
  }

  public static function create($name){
    return new static($name);
  }

  public function acquire($timeout = NULL) {
    if (!$this->_locked) {
      if (defined('CIVICRM_LOCK_DEBUG')) {
	CRM_Core_Error::debug_log_message('Acquiring lock for ' . $this->_name);
      }
      $this->_fh = fopen($this->_path, 'a');
      $this->_locked = flock($this->_fh, LOCK_EX | LOCK_NB);
      if (!$this->_locked) {
	fclose($this->_fh);
	if (defined('CIVICRM_LOCK_DEBUG')) {
	  CRM_Core_Error::debug_log_message('Failed to acquire lock for ' . $this->_name);
	}
      }
    }
    return $this->_locked;
  }

  public function release() {
    if ($this->_locked) {
      if (defined('CIVICRM_LOCK_DEBUG')) {
	CRM_Core_Error::debug_log_message('Releasing lock for ' . $this->_name);
      }
      flock($this->_fh, LOCK_UN);
      fclose($this->_fh);
    }
  }

  public function isFree() {
    return !$this->_locked;
  }

  public function isAcquired() {
    return $this->_locked;
  }

}

