let sname = document.getElementById("sname");
sname.addEventListener("blur", snameHandler);

let email = document.getElementById("email");
email.addEventListener("blur", emailHandler);

let avatar = document.getElementById("pp");
avatar.addEventListener("blur", avatarHandler);

let dob = document.getElementById("dob");
dob.addEventListener("blur", dobHandler);

let password = document.getElementById("pword");
password.addEventListener("blur", pwordHandler);

let cpassword = document.getElementById("cpword");
cpassword.addEventListener("blur", cpwordHandler);

let sform = document.getElementById("mySignupForm");
sform.addEventListener("submit", validateSignup);