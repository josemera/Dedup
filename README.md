# Dedup
deduplicate records from a list


To run this script from command line :

php -d display_errors dedup_test.php _filename_

results will be written to results.json

Note :  The instructions asked that some sort of log be kept of changed fields, however this did not make sense as far as I understood the test.  

It seems to me that deduplicating a list according to a key (email or id) means removing the duplicates.  Removing entire records does not involve updating record fields, but instead keeping one of the records (according to timestamp).  So I've gone ahead and logged output to screen with information about deleted duplicate records.
