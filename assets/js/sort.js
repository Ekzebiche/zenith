$(document).ready(function () {
	var SavePosition = window.location.href + '/SavePosition';
	var SavePositionSub = window.location.href + '/SavePositionSub';
	var getCategories = window.location.href + '/getAjaxCategories';
	var getSubcategories = window.location.href + '/getAjaxSubcategories';
	
	var getPostCategories =  window.location.href.replace('/add', '') + '/getCategories';
	var getPostSubCategories = window.location.href.replace('/add', '') + '/getSubcategories';
	
	var getPostCategories =  'http://' + window.location.host + '/pskov-eparhiya/administrator/posts/getCategories';
	var getPostSubCategories =  'http://' + window.location.host + '/pskov-eparhiya/administrator/posts/getSubcategories';
	
	$('#list').sortable({
		opacity: '0.5',
		update: function(e, ui){
			newOrder = $(this).sortable("serialize");
			$.ajax({
				url: SavePosition,
				type: "POST",
				data: newOrder,
			});}
	});
	$('#listCat').sortable({
		opacity: '0.5',
		update: function(e, ui){
			newOrder = $(this).sortable("serialize");
			$.ajax({
				url: SavePosition,
				type: "POST",
				data: newOrder,
			});}
	});
	$('#listSubCat').sortable({
		opacity: '0.5',
		update: function(e, ui){
			newOrder = $(this).sortable("serialize");
			$.ajax({
				url: SavePositionSub,
				type: "POST",
				data: newOrder,
			});}
	});
	
	$('.page').click(function() {
		$('.subcategories').html('');
        var page = $(this).attr('id');
        $.ajax({
  			type: 'POST',
  			url: getCategories,
  			data: {'pageId': page },
  			success: function(data){
    			$('.categories').html( data );
    			$('.cat').click(function() {
        			var cat = $(this).attr('id');
        			$.ajax({
  						type: 'POST',
  						url: getSubcategories,
  						data: {'categoryId': cat },
  						success: function(data){
    						$('.subcategories').html( data );
  						}
					});
				});
  			}
		});
	});
	
	$('#postPage').change(function() {
		$('.subcategories').html('');
		var id = $(this).val();
		$.ajax({
			type: 'POST',
			url: getPostCategories,
			data: {'pageId': id},
			success: function(data){
				$('.categories').html( data );
				$('#postCategories').change(function(){
					var idS = $(this).val();
					$.ajax({
						type: 'POST',
						url: getPostSubCategories,
						data:{'categoryId': idS},
						success: function(data){
							$('.subcategories').html( data );
						}
					});

				});
				
			}
		});
	});
	$('#postCategories').change(function(){
		var idS = $(this).val();
		$.ajax({
			type: 'POST',
			url: getPostSubCategories,
			data:{'categoryId': idS},
			success: function(data){
				$('.subcategories').html( data );
			}
		});

	});
	$('#refresh').click(function(){ 
		show();
	});

});