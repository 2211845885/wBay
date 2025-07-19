const Cart = {
    add: async function (id) {
        const response = await $.ajax("products.php",{
            method: "POST",
            data: {
                action: "add-to-cart-btn",
                id: id
            }
        });
        console.log(response);
        return JSON.parse(response);
    }
};
export default Cart;