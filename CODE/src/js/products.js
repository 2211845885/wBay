import Cart from './cart.js';
$(".add-to-cart-btn").click(async function(event){
    event.preventDefault(); // Prevent the anchor navigation
    event.stopPropagation(); // Stop bubbling up to the card

 
    // Add your Add to Cart logic here
   let button=$(event.target);
   let result = await Cart.add(button.val())
   if(result["added"] == 1){
        button.addClass("add-disabled")
        button.removeClass("add-to-cart-btn")
        button.html("  <i class='fas fa-shopping-cart'></i> Added to Cart")
        button.prop('disabled', true);

        $('#cartCount').text(result["newCount"]);
   }

});