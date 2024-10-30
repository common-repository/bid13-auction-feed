jQuery(document).ready(function($) {
	$(function() {
		// For the sorted view we have a filter, this handler filters the results on selection
		$('#bid13-auction-feed').on('change', '#state-select', function(){
			if($(this).val() == 'all'){
				$('#bid13-auction-feed .region-wrap').show()
				$('#bid13-auction-feed .city-wrap').show()
				$('#bid13-auction-feed #city-select option').show()
				// reset city if we select show all on province
				$('#bid13-auction-feed #city-select').val('all')
			} else {
				// hide show results
				$('#bid13-auction-feed .region-wrap:not([data-region="'+$(this).val()+'"])').hide()
				$('#bid13-auction-feed .region-wrap[data-region="'+$(this).val()+'"]').show()
				// limit city dropdown options to only this state
				$('#bid13-auction-feed #city-select option:not([data-region="'+$(this).val()+'"])').hide()
				$('#bid13-auction-feed #city-select option[data-region="'+$(this).val()+'"]').show()
				$('#bid13-auction-feed #city-select option[data-region="all"]').show()
			}
		})

		$('#bid13-auction-feed').on('change', '#city-select', function(){
			if($(this).val() == 'all'){
				region = $('#bid13-auction-feed #state-select').val();
				console.log(region)
				if(region == 'all'){
					$('#bid13-auction-feed .city-wrap').show()					
				} else {
					$('#bid13-auction-feed .city-wrap[data-region="'+region+'"]').show()
				}
				
			} else {
				$('#bid13-auction-feed .city-wrap:not([data-city="'+$(this).val()+'"])').hide()
				$('#bid13-auction-feed .city-wrap[data-city="'+$(this).val()+'"]').show()				
			}
		})
	});
});