API entry replacing job.process_mailing that:

 - comes with a locking mecanism enforcing the setting 'Mailing CRON job limit'. 
 This lock implementation is usable elsewhere even if you don't use the API job of this extension.
 - makes sure A/B versions are sent in parralel

# Setup
By default, the lock files are stored in the `locks` directory of the CiviCRM files base directory.
You may override this by defining CIVICRM_FILELOCK_DIR with a path (including trailing slash).

