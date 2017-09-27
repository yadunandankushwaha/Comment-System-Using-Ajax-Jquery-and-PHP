<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Yadu - Comment System Using Ajax, Jquery and PHP - Updated v4.0</title>






<!-- Required header files -->
<script type="text/javascript" src="js/jquery_1.5.2.js"></script>
<script type="text/javascript" src="js/post_watermarkinput.js"></script>
<link href="css/main_yadu_CSS.css" rel="stylesheet" type="text/css">






</head>
<body>
<br clear="all" />
<center>
<div style="font-family:Verdana, Geneva, sans-serif; font-size:24px;width:1000px;">Comment System Using Ajax, Jquery and PHP - Yadu</div><br clear="all" /><br clear="all" />

<!-- Comment Codes Starts Here-->

<center>
<div style="width:550px;" align="left">
<div style="width:550px;" align="left">
<textarea cols="40" rows="8" name="posted_comment" id="posted_comment" placeholder="What's on your mind?"  class="textAreaBox" style="width:500px; height:20px; padding-top:10px;"></textarea></div>
<div style="width:420px;float:left; padding-top:5px; border:0px solid #CCC;" align="left"><div id="comment_logout" onClick="comment_logout();" style="font-family:Verdana, Geneva, sans-serif; font-size:12px; color:#69F;display:none; padding-top:10px; cursor:pointer;"><span class="ccc"><a href="javascript:void(0);">Logout</a></span></div><input type="text" placeholder="Fullname" style="width:290px; height:20px; display:none;" id="fullname_id" class="textAreaBox" value="<?php if(isset($_COOKIE["fullname_id"])) { echo strip_tags($_COOKIE["fullname_id"]); } else { } ?>"></div>
<div style="width:100px; float:left;padding-top:5px;border:0px solid #CCC;" align="right">
<a class="vpb_general_button" onClick="comment_Post_data();">Post</a>
</div><br clear="all">
<div style="width:500px;float:left; padding-top:0px;" align="left"><input type="text" placeholder="Email Address" style="width:290px; height:20px; display:none;" id="email_id" class="textAreaBox" value="<?php if(isset($_COOKIE["email_id"])) { echo strip_tags($_COOKIE["email_id"]); } else { } ?>">
</div><br clear="all"><br clear="all">
<div align="left" id="display_posted_comments_by_yadu"></div><br clear="all">
<div align="left" id="yaduCommentsPosted"></div>

<?php
include "config.php"; //Include the database connection settings file

//This function will get the current page URL for comments identification purpose
function vpb_get_current_page_url() 
{
	 $vpb_this_page_url_is = 'http';
	 /*if ($_SERVER["HTTPS"] == "on") {
		 $vpb_this_page_url_is .= "s";
	 }*/
	 if (array_key_exists('HTTPS', $_SERVER) && $_SERVER["HTTPS"] == "on") {$vpb_this_page_url_is .= "s";}
	 $vpb_this_page_url_is .= "://";
	 if ($_SERVER["SERVER_PORT"] != "80") {
		 $vpb_this_page_url_is .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	 } 
	 else {
		 $vpb_this_page_url_is .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	 }
	 return $vpb_this_page_url_is;
}


//This function will UPPERCASE the first letters of users fullnames 
function vpb_format_users_fullnames($vpb_fullnames=NULL) 
{
	/* Formats a first or last name, and returns the formatted version */
	if (empty($vpb_fullnames))
		return false;
		
	// Initially set the string to lower, to work on it
	$vpb_fullnames = strtolower($vpb_fullnames);
	// Run through and uppercase any multi-barrelled name
	$vpb_fullnamess_array = explode('-',$vpb_fullnames);
	for ($i = 0; $i < count($vpb_fullnamess_array); $i++) 
	{	
		// "McDonald", "O'Conner"..
		if (strncmp($vpb_fullnamess_array[$i],'mc',2) == 0 || preg_match('/^[oO]\'[a-zA-Z]/',$vpb_fullnamess_array[$i])) 
		{
			$vpb_fullnamess_array[$i][2] = strtoupper($vpb_fullnamess_array[$i][2]);
		}
		// Always set the first letter to uppercase, no matter what
		$vpb_fullnamess_array[$i] = ucfirst($vpb_fullnamess_array[$i]);
	}
	// Piece the names back together
	$vpb_fullnames = implode('-',$vpb_fullnamess_array);
	// Return upper-casing on all missed (but required) elements of the $vpb_fullnames var
	return ucwords($vpb_fullnames);
}


//This function is responsible for date/time formatting
function vpb_time_commented( $timestamp )
{
    if( !is_numeric( $timestamp ) ) {
        $timestamp = strtotime( $timestamp );
        if( !is_numeric( $timestamp ) )
		{
            return "";
        }
    }
    $difference = time() - $timestamp;
    $periods = array( "second", "minute", "hour", "day", "week", "month", "years", "decade" );
    $lengths = array( "60","60","24","7","4.35","12","10");
    if ($difference > 0) {
		// this was in the past
        $ending = "ago";
    }
	else {
		// this was in the future
        $difference = -$difference;
        $ending = "to go";
    }
    for( $j=0; $difference>=$lengths[$j] and $j < 7; $j++ )
        $difference /= $lengths[$j];
    $difference = round($difference);
    if( $difference != 1 ) {
        // Also change this if needed for an other language
        $periods[$j].= "s";
    }
    $vpb_Text = "$difference $periods[$j] $ending";
    return $vpb_Text;
}

//This function formats all URLs in a comment
function vpb_add_link_to_urls($vpb_Text = '')
{
	$vpb_Text = preg_replace('#(script|about|applet|activex|chrome):#is', "\\1:", $vpb_Text);
	$vpb_replacements = ' ' . $vpb_Text;
	$vpb_replacements = preg_replace("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<span class='ccc'><a href=\"\\2\" target=\"_blank\"><font style='font-family: Verdana, Geneva, sans-serif;color: blue;font-size:11px; line-height:20px;'>\\2</font></a></span>", $vpb_replacements);
	$vpb_replacements = preg_replace("#(^|[\n ])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<span class='ccc'><a href=\"http://\\2\" target=\"_blank\"><font style='font-family: Verdana, Geneva, sans-serif;color: blue;font-size:11px; line-height:20px;'>\\2</font></a></span>", $vpb_replacements);
	$vpb_replacements = preg_replace("#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<span class='ccc'><a href=\"mailto:\\2@\\3\"><font style='font-family: Verdana, Geneva, sans-serif;color: blue;font-size:11px; line-height:20px;'>\\2@\\3</font></a></span>", $vpb_replacements);
	$vpb_replacements = substr($vpb_replacements, 1);
	return $vpb_replacements;
}



//Check for all comments for this page in database table using the page URL
$check_for_all_comments_for_this_page_in_db = mysql_query("select * from `comments` where `page_url` = '".mysql_real_escape_string(base64_encode(vpb_get_current_page_url()))."' order by `id` desc limit 5");

//Check for the name of the admin for comment moderation purpose
$check_for_admin_name = mysql_query("select * from `users` order by `id` desc limit 1");
$getr_admin_name = mysql_fetch_array($check_for_admin_name);

if(mysql_num_rows($check_for_all_comments_for_this_page_in_db) < 1) 
{
	echo '<div id="default_no_comments" class="info">There are no comments to display on this page at the comment. Thanks...</div>';
}
else
{
	//Get all the comments for this page and display on the screen to the users
	while($get_comments_for_this_page = mysql_fetch_array($check_for_all_comments_for_this_page_in_db))
	{
		$last_displayed_comment_id = strip_tags($get_comments_for_this_page["id"]);
		?>
        <div class="vpb_commentWrapper" id="comment_<?php echo strip_tags($get_comments_for_this_page["id"]); ?>">
        <vasplus_programming_blog_wrap_contents>
        <center><div style="width:450px;" align="center" id="deleting_comment_<?php echo strip_tags($get_comments_for_this_page["id"]); ?>"></div></center>
        <br clear="all">
           
        
        <div style="width:430px;float:left;" align="left">
        <b style="color:#400080; cursor:pointer;"><?php echo vpb_format_users_fullnames(strip_tags($get_comments_for_this_page["fullname"])); ?></b>
        </div>
        
        <div style="width:60px;float:right;" align="right">
        <?php if(isset($_COOKIE["email_id"]) && $_COOKIE["email_id"] == strip_tags($get_comments_for_this_page["email"]) || $_COOKIE["email_id"] == strip_tags($getr_admin_name["admin_email_address"])) { ?>
        <span id="deletion_button" class="ccc"><a style="" href="javascript:void(0);" onClick="deleteThisComment('<?php echo strip_tags($get_comments_for_this_page["id"]); ?>');">Delete</a></span>
        <?php } else { } ?>
        </div>
        <br clear="all">
        
        <div style="width:490px;float:left; padding-top:5px;" align="left">
        <?php echo vpb_add_link_to_urls(nl2br(strip_tags($get_comments_for_this_page["comment"]))); ?>
        </div>
        <br clear="all">
        
        <div style="width:490px;float:right; padding-top:5px;" align="right">
        <span style="font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#999;" title="<?php echo date("l jS \of F Y h:i:s a", strip_tags($get_comments_for_this_page["date"])); ?>">
        <?php echo strip_tags(stripslashes(vpb_time_commented($get_comments_for_this_page["date"]))); ?>
        </span>
        </div>
        <br clear="all">
        <br clear="all">
        </vasplus_programming_blog_wrap_contents>	
        </div>
        <?php
	}
	?>
    <!--Displays more comments -->
     <div id="vpb_display_more_loaded_comments"></div>
     
     
     <!--Holds the id of the last loaded comment for the next comments to load-->
     <input type="hidden" id="last_displayed_comment_id" value="<?php echo $last_displayed_comment_id; ?>" />
     
     <?php
	 $check_for_the_total_comments_for_this_page_in_db = mysql_query("select * from `comments` where `page_url` = '".mysql_real_escape_string(base64_encode(vpb_get_current_page_url()))."'");
	 
	 if(mysql_num_rows($check_for_the_total_comments_for_this_page_in_db) > 5)
	 { ?> 
         <!--This is the load more comments button-->
         <div id="vpb_more_button" class="vpb_show_more_or_the_end" align="center" onclick="vasplus_programming_blog_load_more_comments();"><center><span id="vpb_more_comments_loading">Load more comments</span></center></div>
    <?php
	 }
	 else
	 {
		 //Do not show the load more button if the comments are less than five
	 }
}
?>
<!-- This hidden field is used for comments page identification purpose therefore, 
let it always be on the pages where this comment system is placed -->
<textarea id="get_current_page_url" name="get_current_page_url" style="display:none;"><?php echo base64_encode(vpb_get_current_page_url()); ?></textarea>

<script language="javascript" type="text/javascript" src="js/main_yadu_blog_JS.js"></script>
</div>
</center>


<!-- Comment Codes Ends Here-->














<p style="margin-bottom:200px;">&nbsp;</p>

</center>
</body>
</html>