/*
 * Basic javascript jquery extension
 *
 */
	jQuery.fn.loading = function(delay) {
		if (delay ==null){
			delay =60000;//---60 second delay
		}
		var option={
    			afterShow: function(option){
    				setTimeout(function(){$(option.element).hideLoading()},delay);
    			}
		}
		return $(this).showLoading(option);
   };
