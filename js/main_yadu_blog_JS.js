//WaterMark Textarea and Input boxes
$(document).ready(function() 
{
	$("#posted_comment").Watermark("What's on your mind?"); 
	$("#fullname_id").Watermark("Fullname");
	$("#email_id").Watermark("Email Address");
}); 

//This function is responsible for comments deletion
function deleteThisComment(comment_id) 
{
	var page_url = $("#get_current_page_url").val();
	if(confirm("Are you really sure that you want to delete this comment? click on Ok if Yes and cancel if No."))
	{
		var dataString = 'comment_id=' + comment_id + '&page_url=' + page_url +  '&page=deleteComment';
		$.ajax({
			type: "POST",
			url: "ajax_PHP.php",
			data: dataString,
			cache: false,
			beforeSend: function() 
			{
				$("#deleting_comment_"+comment_id).html('<br clear="all"><div style="font-family:Verdana, Geneva, sans-serif; font-size:12px; color:gray;">Deleting <img style="" src="images/loadings.gif" align="absmiddle" alt="Loading" /></div>');
			},
			success: function(response) 
			{
				$("#comment_"+comment_id).slideUp(1000);
				$(".vpb_show_more_or_the_ends").hide();
				if(response != "")
				{
					$("#display_posted_comments_by_yadu").hide().fadeIn('slow').html(response);
				}
				else
				{
					//Do not show any message since there are still comments on the specified page
				}
			}
		});
	}
	return false;
}

//This function is responsible for comments posting and displaying newly posted comments
function comment_Post_data() 
{
	
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	var fullname_id = $("#fullname_id").val();
    var email_id = $("#email_id").val();
	var posted_comment = $("#posted_comment").val();
	var page_url = $("#get_current_page_url").val();
	$("#default_no_comments").hide();
	$("#display_posted_comments_by_yadu").show();
	
	if(posted_comment == "" || posted_comment === "What's on your mind?")
	{
		$("#display_posted_comments_by_yadu").html('<div class="info">Please type your comment in the required field to post a comment.</div>');
		$("#posted_comment").focus();
	}
	else if(fullname_id == "" || fullname_id === "Fullname")
	{
		$("#display_posted_comments_by_yadu").html('<div class="info">Please enter your fullname in the required field to proceed.</div>');
		$("#fullname_id").focus();
	}
	else if(email_id == "" || email_id === "Email Address")
	{
		$("#display_posted_comments_by_yadu").html('<div class="info">Please enter your email address in the required field to proceed.</div>');
		$("#email_id").focus();
	}
	else if(reg.test(email_id) == false)
	{
		$("#display_posted_comments_by_yadu").html('<div class="info">Please enter a valid email address to proceed.</div>');
		$("#email_id").focus();
	}
	else if(page_url == "")
	{
		$("#display_posted_comments_by_yadu").html('<div class="info">Sorry, the identity of this page could not be verified at the moment. Please try refresh this page and try again or contact the admin of this website to report this error message if the problem persist. Thanks.</div>');
	}
	else 
	{
		var dataString = 'fullname_id=' + fullname_id + '&email_id=' + email_id + '&posted_comment=' + posted_comment + '&page_url=' + page_url + '&page=postComment';
		$.ajax({
			type: "POST",
			url: "ajax_PHP.php",
			data: dataString,
			cache: false,
			beforeSend: function() 
			{
				$("#display_posted_comments_by_yadu").html('<br clear="all"><div style="font-family:Verdana, Geneva, sans-serif; font-size:12px;color:gray;">Please wait <img style="" src="images/loadings.gif" align="absmiddle" alt="Loading" /></div>');
			},
			success: function(response) 
			{
				var vpb_response_brought=response.indexOf('errormessage');
				if(vpb_response_brought != -1)
				{
					$("#display_posted_comments_by_yadu").html(response);
				}
				else
				{
					$("#display_posted_comments_by_yadu").html('');
					$("#display_posted_comments_by_yadu").hide();
					$("#posted_comment").val('').animate({
							"height": "20px"
					}, "fast" );
					$.cookie('fullname_id', fullname_id);
					$.cookie('email_id', email_id);
					$("#fullname_id").hide();
					$("#email_id").hide();
					$("#comment_logout").show();
					$("#yaduCommentsPosted").prepend($(response).fadeIn(400));
					$("#posted_comment").Watermark("What's on your mind?");
				}
			}
		});
	}
}


//This is the load more comments function
function vasplus_programming_blog_load_more_comments() //This is the function to load more comments or content when clicked on the more button
{
	var last_displayed_comment_id = $("#last_displayed_comment_id").val();
	var page_url = $("#get_current_page_url").val();
	
	if(last_displayed_comment_id == "")
	{
		$("#vpb_more_button").html('<div class="info">Sorry, the identity of this page could not be verified at the moment. Please try refresh this page and try again or contact the admin of this website to report this error message if the problem persist (Load 1). Thanks.</div>');
	}
	else if(page_url == "")
	{
		$("#vpb_more_button").html('<div class="info">Sorry, the identity of this page could not be verified at the moment. Please try refresh this page and try again or contact the admin of this website to report this error message if the problem persist (Load 2). Thanks.</div>');
	}
	else
	{
		var dataString = "last_loaded_id="+ last_displayed_comment_id + "&page_url="+ page_url + "&page=load_more_comments";
		$.ajax({
			type: "POST",
			url: "ajax_PHP.php",
			data: dataString, 
			cache: false,
			beforeSend: function() 
			{
				$("#vpb_more_comments_loading").html('<center><div align="center"><img src="images/loadings.gif" align="absabsmiddle" title="Loading more..." /></div></center>');
			},
			success: function(response) 
			{
				$("#vpb_display_more_loaded_comments").append($(response).fadeIn(2000));
				$("#vpb_more_comments_loading").html('Load more comments');
			}
		});
	}
}


//Hide the fullname and email address fields in there's a valid logged in session
$(document).ready(function() 
{
	if($.cookie('fullname_id') && $.cookie('email_id')) 
	{
		$("#fullname_id").hide();
        $("#email_id").hide();
		$("#comment_logout").show();
		
	} 
	else { }
});

//This is the logout function
function comment_logout() 
{
	if(confirm("Are you really sure that you want to log out? click on Ok if Yes and cancel if No."))
	{
		$.cookie('fullname_id', '');
		$.cookie('email_id', '');
		$("#comment_logout").hide();
		$("#deletion_button").hide();
		//$("#fullname_id").show();
		//$("#email_id").show();
		$("#fullname_id").val("");
		$("#email_id").val("");
		$("#fullname_id").Watermark("Fullname");
		$("#email_id").Watermark("Email Address");
		$(".vpb_show_more_or_the_ends").hide();
	}
	return false;
}

$("#posted_comment").live("keypress",function() { $("#display_posted_comments_by_yadu").html(''); });
$("#fullname_id").live("keypress",function() { $("#display_posted_comments_by_yadu").html(''); });
$("#email_id").live("keypress",function() { $("#display_posted_comments_by_yadu").html(''); });

//Expand Textarea Box onfocusin
$("#posted_comment").live("focusin",function() 
{
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	if ($(this).val() == "" || $(this).val() == "What's on your mind?") {
	    $(this).animate({
				"height": "60px"
		}, "fast" );
	}
	else { }
	if ($("#fullname_id").val() != "" && $("#fullname_id").val() != "Fullname") { /*$("#fullname_id").hide();*/ } else { $("#fullname_id").show(); }
	if ($("#email_id").val() != "" && $("#email_id").val() != "Email Address" && reg.test($("#email_id").val()) != false) { /*$("#email_id").hide();*/ } else { $("#email_id").show(); }
});

//Adjust back Textarea Box onfocusout
$("#posted_comment").live("focusout",function() 
{
	if($.cookie('fullname_id') && $.cookie('email_id')) 
	{
	  if ($(this).val() == "" || $(this).val() == "What's on your mind?") {
			$(this).animate({
					"height": "20px"
			}, "fast" );
			 $("#fullname_id").hide();
			 $("#email_id").hide();
	  }
	  else { }
	}
  $("#display_posted_comments_by_yadu").html('');
});

//Expand and Adjust Textarea Box as the user types in messages
/*<![CDATA[*/(function(a){a.fn.autoResize=function(j){var b=a.extend({onResize:function(){},animate:true,animateDuration:150,animateCallback:function(){},extraSpace:20,limit:1000},j);this.filter('textarea').each(function(){var c=a(this).css({resize:'none','overflow-y':'hidden'}),k=c.height(),f=(function(){var l=['height','width','lineHeight','textDecoration','letterSpacing'],h={};a.each(l,function(d,e){h[e]=c.css(e)});return c.clone().removeAttr('id').removeAttr('name').css({position:'absolute',top:0,left:-9999}).css(h).insertBefore(c)})(),i=null,g=function(){f.height(0).val(a(this).val()).scrollTop(10000);var d=Math.max(f.scrollTop(),k)+b.extraSpace,e=a(this).add(f);if(i===d){return}i=d;if(d>=b.limit){a(this).css('overflow-y','');return}b.onResize.call(this);b.animate&&c.css('display')==='block'?e.stop().animate({height:d},b.animateDuration,b.animateCallback):e.height(d)};c.unbind('.dynSiz').bind('keyup.dynSiz',g).bind('keydown.dynSiz',g).bind('change.dynSiz',g)});return this}})(jQuery);$('textarea#posted_comment').autoResize();/*]]>*/ jQuery.cookie = function(name, value, options) { if (typeof value != 'undefined') { options = options || {}; if (value === null) { value = ''; options.expires = -1; } var expires = ''; if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) { var date; if (typeof options.expires == 'number') { date = new Date(); date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000)); } else { date = options.expires; } expires = '; expires=' + date.toUTCString(); } var path = options.path ? '; path=' + (options.path) : ''; var domain = options.domain ? '; domain=' + (options.domain) : ''; var secure = options.secure ? '; secure' : ''; document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join(''); } else { var cookieValue = null; if (document.cookie && document.cookie != '') { var cookies = document.cookie.split(';'); for (var i = 0; i < cookies.length; i++) { var cookie = jQuery.trim(cookies[i]); if (cookie.substring(0, name.length + 1) == (name + '=')) { cookieValue = decodeURIComponent(cookie.substring(name.length + 1)); break; } } } return cookieValue; } };