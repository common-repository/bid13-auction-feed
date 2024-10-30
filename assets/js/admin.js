jQuery(document).ready(function($) {
	  var uploadForm = $('#bid13_auction_feed_settings form[class^="settings-tab-auction"] table tbody tr:last-child');
	  toggleUploadForm($('input[name^="wpt_preferred_image"]:checked').val());
		$('input[name^="wpt_preferred_image"').on('change', function(){
			toggleUploadForm($(this).val());
		})
		function toggleUploadForm(current_choice){
			if(current_choice == 'custom'){
				uploadForm.show();
			} else {
				if(uploadForm.is(':visible')){
					uploadForm.hide();
				}
			}			
		}
		
		// Add a help "toggle All" link to make enabling all facilities easier for big chains
		var facilityList = $('#bid13_auction_feed_settings > form.settings-tab-standard > table > tbody > tr:nth-child(2) > td');
		if(!facilityList.length){
			var facilityList = $('#bid13_auction_feed_settings > form.settings-tab- > table > tbody > tr:nth-child(2) > td');
		}
		
		if(facilityList && facilityList.length){
			var $toggleLink = $('<a href="#">Toggle All</a><br>');
			facilityList.prepend($toggleLink);
			$toggleLink.on('click', function(){
				$(this).parent().find('input[type="checkbox"]').each(function(){
					checkbox = $(this);
					if(typeof checkbox.prop == "function"){
						checkbox.prop("checked", !checkbox.prop("checked"));						
					} else if (typeof checkbox.attr == "function") {
						checkbox.attr("checked", !checkbox.attr("checked"));
					}
				}); 
			});
		}
});