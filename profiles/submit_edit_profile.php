<?
/** File name: submit_edit_profile.php 
 *  Author: Edward Sullivan (ELSULLI@g.clemson.edu, 240 383 0498)
 *  Date: 30 August, 2012
 *  Purpose: Take data that users submitted on the form (at edit_profile.php)
 *           and update the database. The data is about the user's personal 
 *           info (preferred name, email address, phone number, etc.) and about
 *           each of the user's current shows (show name, alias, etc.).
 *
 */
require_once('../conn.php');

if(isset($_POST['sms_recv'])){ // Basic check to make sure that the posted info from edit_submit.php is present

   // Personal information about the user
   // from the form on the edit_profile.php page
   $username = $_POST['username'];
   $preferred_name = $_POST['preferred_name'];
   $email_addr = $_POST['email_addr'];
   $phone_number = $_POST['phone_number'];
   $sms_recv = $_POST['sms_recv'];
   $profile_paragraph = $_POST['profile_paragraph'];
   $statusID = $_POST['statusID']; // active, inactive, alumni, etc.
   $show_count = $_POST['show_count']; // Current # of shows
   $genre = $_POST['genre'];
   
   $sched_ID = $_POST['scheduleID'];
 
   // Finding schedule ID for each of user's current shows
   for ($j = 0; $j <= $show_count; $j++){
      $post_name = sprintf("scheduleID%d", $j);
      $show_ID[$j] = $_POST[$post_name];
	  
	  
   }

   // Finding user's alias for each of user's current shows
   //for ($j = 0; $j <= $show_count; $j++){
      //$post_name = sprintf("txtbox_alias%d", $j);
     // $show_alias[$j] = $_POST[$post_name];
  // }
  
  $show_alias = $_POST['txtbox_alias'];

   // Finding show name for each of user's current shows
   //for ($j = 0; $j <= $show_count; $j++){
     // $post_name = sprintf("txtbox_showname%d", $j);
     // $show_name[$j] = $_POST[$post_name];
   //}
   
   $show_name = $_POST['txtbox_showname'];

   // Finding show description for each of user's current shows
   //for ($j = 0; $j <= $show_count; $j++){
      //$post_name = sprintf("txtarea_desc%d", $j);
      //$show_desc[$j] = $_POST[$post_name];
   //}
   
   $show_desc = $_POST['txtarea_desc'];
   
   //test code for adding genre
   /*
   for ($j = 1; $j <= $show_count; $j++){
      $post_name = sprintf("genre%d", $j);
      $genre[$j] = $_POST[$post_name];
	  
	  echo "TEST CODE";
   }
*/
   // Updating the MySQL database with the user's personal information
   $update_query = sprintf("UPDATE users SET preferred_name='%s', email_addr='%s', phone_number='%s', sms_recv='%d', statusID='%d', profile_paragraph='%s' WHERE username='%s'",
                           mysql_real_escape_string($preferred_name),mysql_real_escape_string($email_addr),
                           mysql_real_escape_string($phone_number),$sms_recv,$statusID,mysql_real_escape_string($profile_paragraph),mysql_real_escape_string($username));
   mysql_query($update_query) or die("MySQL error [". __FILE__ ."] near line " . __LINE__ . ": " .mysql_error());
   
   $update_query = sprintf("UPDATE schedule SET genre='%s', description='%s', show_name='%s' WHERE scheduleID='%d'", mysql_real_escape_string($genre), mysql_real_escape_string($show_desc), mysql_real_escape_string($show_name), $sched_ID);
    mysql_query($update_query) or die("MySQL error [". __FILE__ ."] near line " . __LINE__ . ": " .mysql_error());
  
   
   // Updating the MySQL database with show details for each of the user's shows
   /*
   for($k = 1; $k <= $show_count; $k++){
      $update_query = sprintf("UPDATE schedule SET show_name='%s', description='%s', genre='%s' WHERE scheduleID='%d'",
                              mysql_real_escape_string($show_name[$k]),mysql_real_escape_string($show_desc),mysql_real_escape_string($genre), $show_ID[$k]);
      mysql_query($update_query) or die("MySQL error [". __FILE__ ."] near line " . __LINE__ . ": " .mysql_error());

      $update_query = sprintf("UPDATE schedule_hosts SET schedule_alias='%s' WHERE scheduleID='%d' and username='%s'",
                              mysql_real_escape_string($show_alias[$k]), $show_ID[$k], mysql_real_escape_string($username));
      mysql_query($update_query) or die("MySQL error [". __FILE__ ."] near line " . __LINE__ . ": " .mysql_error());
   }
	*/

   $pictUploadError = storeUploadedPicture($username);


   // HTML header information and a short "thank you" note in the body with links
   echo "<html>";
      echo "<head>";
         echo " <meta http-equiv=\"content-type\" content=\"text/html; charset=iso-8859-1\" />
         <meta name=\"author\" content=\"Edward Sullivan\" />
         <meta name=\"keywords\" content=\"WSBF, wsbf, Clemson, radio, music, stream, online, mp3, profile, DJ, show, submit\" />
         <meta name=\"description\" content=\"WSBF FM Clemson is an alternative local radio station broadcasting from Clemson University. Page to submit DJ/show info.\" />
         <meta name=\"robots\" content=\"all\" />
         <title>WSBF FM CLEMSON: Submit Profile Info</title>";
      echo "</head>";
      echo "<body>";
		
         echo "<h5>Thank you for updating your profile information, ";
		 echo $preferred_name . "<br>";
		 echo $show_desc;
         if ($pictUploadError == -1){
             echo "<br>Profile picture successfully uploaded.";
         }
         if ($pictUploadError == 0){
             echo "<br>No profile picture uploaded.";
         }
         if ($pictUploadError == 1){
             echo "<br>Profile picture not uploaded. The file extension for ";
             echo "the image must be .jpg, .jpeg, .png, or .gif";
         }
         if ($pictUploadError == 2){
             echo "<br>Profile picture not uploaded. The image file ";
             echo "must be 1 MB (1,024 KB) or smaller.";
         } 
         echo "<br>If you want to, you can go back to the <a href=\"..\submit_login.php\">main menu</a>, or you can <a href=\"..\logout.php\">log out</a>.<h5>";
      echo "</body>";
   echo "</html>";

}


function storeUploadedPicture($username){

   //Uploading the profile picture
   
   // There is one folder for each user to store a picture. Seeing
   // if that folder has been created yet.

   $pictFolder = "user_profile_pictures/".$username;
   $pictFileName = "user_profile_pictures/".$username."/".$username.".*";
   if (!file_exists($pictFolder)) {
       mkdir($pictFolder, 0777);
   }



   // Deleting all (there should be at most 1) current exisitng
   // profile pictures if the user selected that checkbox
   $pictFolder = "user_profile_pictures/".$username;
   if( !empty($pictFolder)&& isset($_POST['checkBoxDeletePic']) &&
   $_POST['checkBoxDeletePic'] == 'deletePic'){
      $stre = "user_profile_pictures/".$username."/*.*";
      foreach(glob($stre) as $filename){
         unlink($filename);
      }
   }
   





  define ("MAX_SIZE","400");

  $errors=0;
 
  if($_SERVER["REQUEST_METHOD"] == "POST"){
     $image =$_FILES["fileField"]["name"];
     $uploadedfile = $_FILES["fileField"]["tmp_name"];

     if ($image){
        $filename = stripslashes($_FILES['fileField']["name"]);
        $extension = getExtension($filename);
        $extension = strtolower($extension);
        if (($extension != "jpg") && ($extension != "jpeg") 
           && ($extension != "png") && ($extension != "gif")) {
              $errors=1;
        }
        else{
           $size=$_FILES['fileField']['size'];
 
           if ($size > MAX_SIZE*1024 or empty($size)){
              $errors=2;
           }
           else{

              // Deleting all (there should be at most 1) current exisitng
              // profile pictures
              $pictFolder = "user_profile_pictures/".$username;
              if( !empty($pictFolder)&& isset($_POST['checkBoxDeletePic']) &&
              $_POST['checkBoxDeletePic'] == 'deletePic'){
                 $stre = "user_profile_pictures/".$username."/*.*";
                 foreach(glob($stre) as $filename){
                    $unlink($filename);
                 }
              }



              if($extension=="jpg" || $extension=="jpeg" ){
                 $uploadedfile = $_FILES['fileField']['tmp_name'];
                 $src = imagecreatefromjpeg($uploadedfile);
              }
              else if($extension=="png"){
                 $uploadedfile = $_FILES['fileField']['tmp_name'];
                 $src = imagecreatefrompng($uploadedfile);
              }
              else {
                 $src = imagecreatefromgif($uploadedfile);
              }
 
              list($width,$height)=getimagesize($uploadedfile);

              $longerPicSide = max($width, $height);
              if ($longerPicSide == $width){
                 $newwidth=400;
                 $newheight=($height/$width)*$newwidth;
                 $tmp=imagecreatetruecolor($newwidth,$newheight);
              }              
              else{
                 $newheight=400;
                 $newwidth=($width/$height)*$newheight;
                 $tmp=imagecreatetruecolor($newwidth,$newheight);
              }
  
          //    $newwidth=300;
          //    $newheight=($height/$width)*$newwidth;
          //    $tmp=imagecreatetruecolor($newwidth,$newheight);

              $newwidth1=250;
              $newheight1=($height/$width)*$newwidth1;
              $tmp1=imagecreatetruecolor($newwidth1,$newheight1);
 
              imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,
                                                $width,$height);

              imagecopyresampled($tmp1,$src,0,0,0,0,$newwidth1,$newheight1, 
                                                 $width,$height);

              $filename = "user_profile_pictures/".$username. "/" . $username . "." . $extension;
              $filename1 = "user_profile_pictures/".$username. "/small" . $username . "." . $extension;

              imagejpeg($tmp,$filename,100);
              $errors = -1; // No errors. Image successfully uploaded
            //imagejpeg($tmp1,$filename1,100);
            //$errors = -1; 
 
              imagedestroy($src);
              imagedestroy($tmp);
              imagedestroy($tmp1);
           }
        }
     }

     //If no errors registred, print the success message
     if(isset($_POST['Submit']) && !$errors) {
        // mysql_query("update SQL statement ");
     }
   }
   return $errors;
}
 
function getExtension($str) {
   $i = strrpos($str,".");
   if (!$i) { 
      return ""; 
   } 
   $l = strlen($str) - $i;
   $ext = substr($str,$i+1,$l);
   return $ext;
}






?>
