$(document).on('click','.cartIconAction',function(){
    var productId = $(this).data('id');
            
    const that = this;
    if ($(this).hasClass('added')) {
        $.ajax({
            type: 'POST',
            url: removeFromCartUrl,
            data: {
                product_id: productId,
                quantity: 1,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $(that).find('path').attr('fill','');
                $(that).removeClass('added');
                $('.cartDataContainer').html(response.data.htmlData);
                $('.mainCartCounter').text(response.data.count);
                show_message('Product has been removed from cart successfully','success');
            },
            error: function(error) {
                show_message('Error removing product from cart','error');
               
            }
        });
    }else{

        $.ajax({
            type: 'POST',
            url: addToCartUrl,
            data: {
                product_id: productId,
                quantity: 1,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $(that).find('path').attr('fill','#FF0000');
                $(that).addClass('added');
                $('.cartDataContainer').html(response.data.htmlData);
                $('.mainCartCounter').text(response.data.count);
                show_message('Product has been added to cart successfully','success');

            },
            error: function(error) {
                show_message('Error adding product to cart','error');
            }
        });
    }
    
});

$(document).on('click','.wishlistIconAction',function(){
    var productId = $(this).data('id');
    if(isLoggedIn == 'no'){
        window.location.href = addToWishlistUrl;
    }        
    const that = this;
    if ($(this).hasClass('added')) {
        $.ajax({
            type: 'POST',
            url: removeFromWishlistUrl,
            data: {
                product_id: productId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $(that).find('path').attr('fill','');
                $(that).removeClass('added');
                $('.mainWishlistCounter').text(response.data.count);
                show_message('Product has been removed from wishlist successfully','success');
            },
            error: function(error) {
                show_message('Error removing product from wishlist','error');
               
            }
        });
    }else{

        $.ajax({
            type: 'POST',
            url: addToWishlistUrl,
            data: {
                product_id: productId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $(that).find('path').attr('fill','red');
                $(that).addClass('added');
                $('.mainWishlistCounter').text(response.data.count);
                show_message('Product has been added to wishlist successfully','success');

            },
            error: function(error) {
                show_message('Error adding product to wishlist','error');
            }
        });
    }
    
});