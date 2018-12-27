/*
 * Basic javascript jquery extension
 *
 */
	jQuery.fn.loading = function(delay) {
		if (delay ==null){
			delay =10000;//---10 second delay
		}
		var option={
    			afterShow: function(option){
    				setTimeout(function(){$(option.element).hideLoading()},delay);
    			}
		}
		return $(this).showLoading(option);
   };
