(function ($) {
	"use strict";
	
	$(document).ready(function () {

		jQuery('.post-type-product_ticket input[name="post_title"]').prop("disabled", true)
		jQuery('.post-type-product_ticket textarea[name="content"]').prop("disabled", true)

		$('#users').select2({
			width: '100%'
		});

		$('.select2-tags').select2({tags: true});
		$(".select2-tags").on("select2:select", function (evt) {
			var element = evt.params.data.element;
			var $element = $(element);
			
			$element.detach();
			$(this).append($element);
			$(this).trigger("change");
		});

		// Admin chat choose image from wp-media
		let attachment = false;
		let sendfalse = true;
		let _replacetext = "Choose Image";
		$('body').on('click', '.wcpt_upload_image_button', function(e){
			e.preventDefault();
			
				var button = $(this),
					custom_uploader = wp.media({
				title: 'Insert image',
				library : {
					// uncomment the next line if you want to attach image to the current post
					// uploadedTo : wp.media.view.settings.post.id, 
					// type : 'image'
					type : ['image/png', 'image/jpeg', 'image/jpg']
				},
				button: {
					text: 'Use this image' // button label text
				},
				multiple: false // for multiple image selection set to true
			}).on('select', function() { // it also has "open" and "close" events 
				attachment = custom_uploader.state().get('selection').first().toJSON();
				if (attachment.type == "image") {
					$(button).next().val(attachment.id)
					$(".upload-img-btn-span").text(attachment.title);
					sendfalse = true;
				}else{
					sendfalse = false;
					$(".upload-img-btn-span").text(attachment.title);
					alert("This file type not supported.");
					return false;
				}
			})
			.open();
		});
	
		$("#chattext").bind("keypress", {}, keypressInBox);
		function keypressInBox(e) {
			var code = (e.keyCode ? e.keyCode : e.which);
			if (code == 13) { //Enter keycode                        
				e.preventDefault();
				$('.wcpt-chat #chatsend').trigger("click");
			}
		};
		
		$('body').on('click', '.wcpt-chat #chatsend', function(e){

			if (sendfalse === false) {
				alert("Please select valid image.");
				return
			}
			
			let agentname = jQuery('.agent-name').val();
			const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
				"Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
			];

			var days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
		
			var tz = jstz.determine(); // Determines the time zone of the browser client
			var visitortimezone = tz.name();

			const today = new Date();
			const yyyy = today.getFullYear();
			let mm = today.getMonth(); // Months start at 0!
			let dd = today.getDate();
			let fulldate = days[today.getDay()] + " " + dd + " " + monthNames[mm] + " " + yyyy;

			let chattext = jQuery('#chattext').val();
			let chattextdiv = '<div class="chat outgoing"><div class="details"><p><span>' + agentname + '</span>' + chattext + '<span>' + fulldate + '</span></p></div></div>';

			let pticket_id = jQuery('.pticket_id').val();
			let incoming_msg = jQuery('.incoming_msg').val();
			let outgoing_mgs = jQuery('.outgoing_mgs').val();
			let chat_attachment = jQuery('#chat_attachment').val();
			let in_out = jQuery('.in_out').val();
			let scrollElement = document.getElementById("scroll-box");
			
			let output = false;
			if (attachment) {
				let imgsrc = attachment.url;
				if (imgsrc) {
					output = '<div class="chat outgoing ">' + 
									'<div class="details">' +
										'<a href="' + imgsrc + '" target="_blank"><img src="'+imgsrc+'" ></a>' +
									'</div>'+
								'</div>';
				}
			}

			var data = {
				'action': 'chatajax',
				'post_id': pticket_id,
				'incoming_msg': incoming_msg,
				'outgoing_mgs': outgoing_mgs,
				'img': chat_attachment,
				'in_out': in_out,
				'message': chattext,
				'timezone': visitortimezone,
				'ajaxnonce': wcptvar.ajaxnonce,
			};

			if (!output && !chattext) {
				return;
			}

			$.ajax({
				url: ajaxurl,
				type: "post",
				data: data,
				beforeSend: function(xhr) {
					$('#chattext').val("");
					// $(".chat-area .wcpt_upload_image_button").html("Upload image");
					// $(".chat-area .wcpt_remove_image_button").hide(); 
					$(".chat-area #chat_attachment").val(); 
					$(".upload-img-btn-span").text("Choose Image");
					attachment = false;
				
					if (chattext) {
						$(".chat-box").append(chattextdiv);
					}
					if (output) {
						$(".chat-box").append(output);
					}
					scrollElement.scrollTop = scrollElement.scrollHeight;
				},
				success: function(res) {	
					console.log(res);
				},
			});
		});

	});


})(jQuery);
