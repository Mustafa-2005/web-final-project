// signup.js
document
  .getElementById("signupForm")
  .addEventListener("submit", function () {

    var name = document.getElementById("name").value;
    var email = document.getElementById("email").value;
    var password = document.getElementById("password").value;

    // التحقق من الاسم
    if (name.length < 3) {
      alert("Name must be at least 3 characters long.");
      return;
    }

    // التحقق من كلمة السر
    var passwordRegex = /[a-zA-Z]/; // التأكد من وجود حرف واحد على الأقل
    if (password.length < 5 || !passwordRegex.test(password)) {
      alert(
        "Password must be at least 5 characters long and contain at least one letter."
      );
      return;
    }

    // التحقق من البريد الإلكتروني (إذا كان في صيغة صحيحة أو لا)
    var emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
    if (!emailRegex.test(email)) {
      alert("Please enter a valid email address.");
      return;
    }

    // لو كل البيانات صحيحة، نوجه المستخدم لصفحة تسجيل الدخول
    window.location.href = "../html/login.html";
  });