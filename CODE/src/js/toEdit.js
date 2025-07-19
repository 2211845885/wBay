
const a =  document.getElementsByClassName("to-edit");

Array.prototype.forEach.call(a,element => {
    element.addEventListener("click", function(e) {
        e.preventDefault();
        e.stopPropagation();
        window.location.href = "vendorEditProduct.php?prodID=" + e.target.value;
    })
});


const admin =  document.getElementsByClassName("to-AdminEdit");

Array.prototype.forEach.call(admin,element => {
    element.addEventListener("click", function(e) {
        e.preventDefault();
        e.stopPropagation();
        window.location.href = "adminEditProduct.php?prodID=" + e.target.value;
    })
});