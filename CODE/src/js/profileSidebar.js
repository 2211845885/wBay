
// Handle click on the Personal Info tab
document.getElementById("personalInfoBtn").addEventListener("click", function (e) {
    // Remove 'active' class from the currently active tab and its content
    const tabs = document.querySelectorAll(".tab-content");
    tabs.forEach(tab => tab.classList.remove("active"));
    
    const btns = document.querySelectorAll(".nav-item");
    btns.forEach(tab => tab.classList.remove("active"));

    // Add 'active' class to the clicked tab's content
    document.getElementById("personalInfo").classList.add("active");

    document.getElementById("personalInfoBtn").classList.add("active");
});

// Handle click on the Orders tab
document.getElementById("ordersBtn").addEventListener("click", function (e) {
    // Remove 'active' class from the currently active tab and its content
    const tabs = document.querySelectorAll(".tab-content");
    tabs.forEach(tab => tab.classList.remove("active"));

    const btns = document.querySelectorAll(".nav-item");
    btns.forEach(tab => tab.classList.remove("active"));
    
    // Add 'active' class to the clicked tab's content
    document.getElementById("orders").classList.add("active");

    document.getElementById("ordersBtn").classList.add("active");
});

// Handle click on the Security tab
document.getElementById("securityBtn").addEventListener("click", function (e) {
    // Remove 'active' class from the currently active tab and its content
    const tabs = document.querySelectorAll(".tab-content");
    tabs.forEach(tab => tab.classList.remove("active"));

    const btns = document.querySelectorAll(".nav-item");
    btns.forEach(tab => tab.classList.remove("active"));
    
    // Add 'active' class to the clicked tab's content
    document.getElementById("security").classList.add("active");

    document.getElementById("securityBtn").classList.add("active");
});
