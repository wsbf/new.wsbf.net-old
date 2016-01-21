WSBF - Fishbowl
Documented by David Cohen, 1/7/2013.
README

==== What it does ====
This set of PHP pages/scripts is written to more or less emulate the original WSBF method of deciding who got to pick their showtime first. This consisted of everyone writing on a piece of paper what they had done for the station and turning it into senior staff, who in turn put the papers in one of five fishbowls. At the meeting, they drew cards randomly from each fishbowl, starting with the Fishbowl 1 (the best), such that the best DJs get one of the best picks, and the shittier DJs get shittier picks. 


==== How to use it ====
1. Run fishbowl_clear_table.php to clean out the previous semester's entries.
2. Edit fishbowl_config.php, and change the values to the proper ones
3. Tell DJs to go to new.wsbf.net/fishbowl to fill out their app. Test it for 
   yourself and make sure it works.

==== Files ====
fishbowl_app.php - contains the fishbowl form logic, and is called in index.php
fishbowl_clear_table.php - clears the mysql table `fishbowl`, putting the
   values into `fishbowl_log`
fishbowl_config.php - contains configuration values for the semester and dates
fishbowl_print.php - prints out the results. Notice that it randomizes the 
   contents of each fishbowl on each refresh.
fishbowl_review.php - this is the page that senior staff should go to in order 
   to rate the apps
index.php - this is the page that DJs should go to for the form (since it 
   works with new.wsbf.net/fishbowl)

