document
  .getElementById("loginForm")
  .addEventListener("submit", function () {

    var email = document.getElementById("email").value;
    var password = document.getElementById("password").value;

    if (email === "" || password === "") {
      alert("Please fill in both email and password.");
      return;
    }

    // هنا بنوجه المستخدم لصفحة رئيسية بعد التحقق من البيانات
    window.location.href = "../html/index.html";
  });