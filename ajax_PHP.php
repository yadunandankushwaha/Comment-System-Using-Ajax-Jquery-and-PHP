<?php



include "config.php"; //Include the database connection settings file

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

if(isset($_POST["page"]) && !empty($_POST["page"])) //Page Validation
{
	 //Post Comment Page Starts from here
	if($_POST["page"] == "postComment")
	{
		$vpb_posted_comment = trim($_POST["posted_comment"]);
		
		if(isset($_POST["fullname_id"]) && !empty($_POST["fullname_id"]) && $_POST["fullname_id"] != "Fullname" && isset($_POST["email_id"]) && !empty($_POST["email_id"]) && $_POST["email_id"] != "Email Address" && isset($_POST["posted_comment"]) && !empty($vpb_posted_comment) && isset($_POST["page_url"]) && !empty($_POST["page_url"]))
		{
			$fullname_id = strip_tags(strtoupper($_POST["fullname_id"]));
			$email_id = strip_tags($_POST["email_id"]);
			$page_url = $_POST["page_url"];
			
			$query = strip_tags(htmlspecialchars($_POST['posted_comment']));
			function no_magic_quotes($query) 
			{
				$data = explode("\\",$query);
				$cleaned = implode("",$data);
				return $cleaned;
			}
			
			mysql_query("insert into `comments` values('', '".mysql_real_escape_string($fullname_id)."', '".mysql_real_escape_string($email_id)."', '".mysql_real_escape_string(no_magic_quotes($query))."', '".mysql_real_escape_string($page_url)."', '".mysql_real_escape_string(strip_tags(strtotime(date("Y-m-d H:i:s"))))."')");
			
			$checkLastInsertedComment = mysql_query("select * from `comments` where `email` = '".mysql_real_escape_string(strip_tags($_POST["email_id"]))."' order by `id` desc limit 1");
			
			$getLastInsertedComment = mysql_fetch_array($checkLastInsertedComment);
			?>
            
			<div class="vpb_commentWrapper" id="comment_<?php echo strip_tags($getLastInsertedComment["id"]); ?>">
            <vasplus_programming_blog_wrap_contents>
            <center><div style="width:450px;" align="center" id="deleting_comment_<?php echo strip_tags($getLastInsertedComment["id"]); ?>"></div></center>
            <br clear="all">
               
            <div style="width:430px;float:left;" align="left">
            <b style="color:#400080; cursor:pointer;"><?php echo vpb_format_users_fullnames(strip_tags($getLastInsertedComment["fullname"])); ?></b>
            </div>
            
            <div style="width:60px;float:right;" align="right">
            <?php if(isset($_COOKIE["email_id"])) { ?>
            <span id="deletion_button" class="ccc"><a style="" href="javascript:void(0);" onClick="deleteThisComment(<?php echo strip_tags($getLastInsertedComment["id"]); ?>);">Delete</a></span>
            <?php } else { } ?>
            </div>
            <br clear="all">
            
            <div style="width:490px;float:left; padding-top:5px;" align="left">
            <?php echo vpb_add_link_to_urls(nl2br(strip_tags($getLastInsertedComment["comment"]))); ?>
            </div>
            <br clear="all">
            
            <div style="width:490px;float:right; padding-top:5px;" align="right">
            <span style="font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#999;" title="<?php echo date("l jS \of F Y h:i:s a", strip_tags($getLastInsertedComment["date"])); ?>">
            <?php echo strip_tags(stripslashes(vpb_time_commented($getLastInsertedComment["date"]))); ?>
            </span>
            </div>
            <br clear="all">
            <br clear="all">
            </vasplus_programming_blog_wrap_contents>
            </div>
			<?php
			
		}
		else
		{
			echo '<font style="font-size:0px;">errormessage</font>';
			echo "<div class='info'>Sorry, the comment could not be posted at the moment.<br>Please be sure that the comment box is not empty and try again or contact this website admin to report this error message if the problem persist (1). Thanks.</div>";
		}
	}
	 //Post Comment Page Ends here
	 
	 
	//Load More Comment Page Starts from here
	elseif($_POST["page"] == "load_more_comments")
	{
		if(isset($_POST["last_loaded_id"]) && !empty($_POST["last_loaded_id"]) && isset($_POST["page_url"]) && !empty($_POST["page_url"]))
		{
			$last_loaded_id = strip_tags($_POST["last_loaded_id"]);
			$page_url = $_POST["page_url"];
		
			$check_for_more_comments = mysql_query("select * from `comments` where `id` > '".mysql_real_escape_string($last_loaded_id)."' and `page_url` = '".mysql_real_escape_string($page_url)."' order by `id desc` desc limit 5");
			
			//Check for the name of the admin for comment moderation purpose
			$check_for_admin_name = mysql_query("select * from `users` order by `id` desc limit 1");
			$getr_admin_name = mysql_fetch_array($check_for_admin_name);
			
			
			if(mysql_num_rows($check_for_more_comments) > 0)
			{
				while($get_more_comments = mysql_fetch_array($check_for_more_comments))
				{
					$last_loaded_id = strip_tags($get_more_comments["id"]);
				?>
					<div class="vpb_commentWrapper" id="comment_<?php echo strip_tags($get_more_comments["id"]); ?>">
					<vasplus_programming_blog_wrap_contents>
					<center><div style="width:450px;" align="center" id="deleting_comment_<?php echo strip_tags($get_more_comments["id"]); ?>"></div></center>
					<br clear="all">
					   
					<div style="width:430px;float:left;" align="left">
					<b style="color:#400080; cursor:pointer;"><?php echo vpb_format_users_fullnames(strip_tags($get_more_comments["fullname"])); ?></b>
					</div>
					
					<div style="width:60px;float:right;" align="right">
					<?php if(isset($_COOKIE["email_id"]) && $_COOKIE["email_id"] == strip_tags($get_more_comments["email"]) || $_COOKIE["email_id"] == strip_tags($getr_admin_name["admin_email_address"])) { ?>
					<span id="deletion_button" class="ccc"><a style="" href="javascript:void(0);" onClick="deleteThisComment(<?php echo strip_tags($get_more_comments["id"]); ?>);">Delete</a></span>
					<?php } else { } ?>
					</div>
					<br clear="all">
					
					<div style="width:490px;float:left; padding-top:5px;" align="left">
					<?php echo vpb_add_link_to_urls(nl2br(strip_tags($get_more_comments["comment"]))); ?>
					</div>
					<br clear="all">
					
					<div style="width:490px;float:right; padding-top:5px;" align="right">
					<span style="font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#999;" title="<?php echo date("l jS \of F Y h:i:s a", strip_tags($get_more_comments["date"])); ?>">
					<?php echo strip_tags(stripslashes(vpb_time_commented($get_more_comments["date"]))); ?>
					</span>
					</div>
					<br clear="all">
					<br clear="all">
					</vasplus_programming_blog_wrap_contents>
					</div>
				<?php
				}
				//Do final checking for the load more button to see if it should be hiden now or not
				$check_for_more_comments_again = mysql_query("select * from `comments` where `id` > '".mysql_real_escape_string($last_loaded_id)."' and `page_url` = '".mysql_real_escape_string($page_url)."' order by `id` desc limit 5");
				if(mysql_num_rows($check_for_more_comments_again) < 1)
				{
					?>
                     <div class="vpb_show_more_or_the_ends" align="center"><center><font style="font-family:Verdana, Geneva, sans-serif; font-size:12px; color:gray;">No more comments</font></center></div>
                     
                     <script type="text/javascript">$(document).ready(function(){ $("#vpb_more_button").remove(); });</script>
                     <br clear="all" />
                     <?php
				}
				else {}
				?>
        			<script type="text/javascript"> $(document).ready(function(){ $("#last_displayed_comment_id").val(parseInt('<?php echo $last_loaded_id; ?>'));  }); </script>
        		<?php
			}
			else
			{
				 ?>
                 <div class="vpb_show_more_or_the_ends" align="center"><center><font style="font-family:Verdana, Geneva, sans-serif; font-size:12px; color:gray;">No more comments</font></center></div>
                 
                 <script type="text/javascript">$(document).ready(function(){ $("#vpb_more_button").remove(); });</script>
                 <br clear="all" />
                 <?php
                 exit;
			}
		}
		else
		{
			echo '<font style="font-size:0px;">errormessage</font>';
			echo "<div class='info'>Sorry, the operation you were trying to perform could not be completed at the moment.<br>Please try again or contact this website admin to report this error message if the problem persist (2). Thanks.</div>";
		}
	}
	 //Load More Comment Page Ends here
	 
	  //Comment Deletion Page Starts from here
	elseif($_POST["page"] == "deleteComment")
	{
		mysql_query("delete from `comments` where `id` = '".mysql_real_escape_string(strip_tags($_POST["comment_id"]))."'");
		if(!empty($_POST["page_url"]))
		{
			$check_if_there_are_still_comments_for_this_page = mysql_query("select * from `comments` where `page_url` = '".mysql_real_escape_string($_POST["page_url"])."'");
			
			if(mysql_num_rows($check_if_there_are_still_comments_for_this_page) < 1)
			{
				echo '<div id="default_no_comments" class="info">There are no more comments to display on this page at the comment. Thanks...</div>';
			}
			else
			{
				//Do not show any message since there are still comments on the specified page
			}
		}
	}
	 //Comment Deletion Page Ends here
	else
	{
		echo '<font style="font-size:0px;">errormessage</font>';
		echo "<div class='info'>Sorry, the operation you were trying to perform could not be completed at the moment.<br>Please try again or contact this website admin to report this error message if the problem persist (2). Thanks.</div>";
	}
}
else
{
	echo '<font style="font-size:0px;">errormessage</font>';
	echo "<div class='info'>Sorry, the operation you were trying to perform could not be completed at the moment.<br>Please try again or contact this website admin to report this error message if the problem persist (3). Thanks.</div>";
}
?>