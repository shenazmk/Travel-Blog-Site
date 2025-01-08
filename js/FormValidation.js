/*login page starts here*/
// Function to validate email
function validateEmail(email) {
    const emailRegEx = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; 
    return emailRegEx.test(email);
}

// Function to validate password
function validatePWD(pwd) {
    // Check if the password is at least 6 characters long and contains no spaces
    return pwd.length >= 6 && !/\s/.test(pwd);
}

// Function to handle login validation
function validateLogin(event) {
    // Prevent form submission at the start
    event.preventDefault();

    // Get email and password input fields
    let email = document.getElementById("email");
    let password = document.getElementById("password");
    let formIsValid = true;

    // Email validation
    if (!validateEmail(email.value)) {
        email.classList.add("highlight"); // Highlight the input box
        document.getElementById("error-text-email").classList.remove("error-text-hidden"); // Show error message
        formIsValid = false;
    } else {
        email.classList.remove("highlight"); // Remove highlight
        document.getElementById("error-text-email").classList.add("error-text-hidden"); // Hide error message
    }

    // Password validation
    if (!validatePWD(password.value)) {
        password.classList.add("highlight"); // Highlight the input box
        document.getElementById("error-text-password").classList.remove("error-text-hidden"); // Show error message
        formIsValid = false;
    } else {
        password.classList.remove("highlight"); // Remove highlight
        document.getElementById("error-text-password").classList.add("error-text-hidden"); // Hide error message
    }

    // Only submit the form if it's valid

    if (formIsValid) {
        document.getElementById("login-form").submit(); // Submit the form
    }
}
window.onload = function() {
    // Ensure the validation runs on form submission
    const loginForm = document.getElementById("login-form");
    if (loginForm) {
        loginForm.addEventListener("submit", validateLogin);
    }
};
/*login page ends here*/

/*blogpostdetail starts*/



let submitBtn = document.querySelector('input[type="submit"]');
submitBtn.addEventListener("click", validateComment);
let commentField = document.getElementById("comment-content");

let remainingChars = document.getElementById("remaining_chars");
const MAX_CHARS = 1000;

let charCount = document.getElementById("numOfChar");

commentField.addEventListener('input', (e) => {

const NumofChar = e.target.value.length;

charCount.textContent = `Character count: ${NumofChar}`;


     const remaining = MAX_CHARS - NumofChar;
     let colour = remaining < (MAX_CHARS * 0.1) ? 'red' : null;
     remainingChars.textContent = `Characters remaining: ${remaining}`;
     remainingChars.style.color = colour;
 });

//  function validator for comments
 function validateComment(event){

     let formIsValid = true;
          let err_length = document.getElementById("error-text-length");

    const regEx = /[0-9a-z]/i;
    let err_box = document.getElementById("error-text-blank");


     if (!regEx.test(commentField.value)) {
         commentField.classList.add("highlight");
         err_box.classList.remove("error-text-hidden");
         formIsValid = false;

     }
     else {
         commentField.classList.remove("highlight");

         err_box.classList.add("error-text-hidden");

     }


     if (commentField.value.length > MAX_CHARS) {
         err_length.classList.remove("error-text-hidden");
         formIsValid = false;
     }
     else {
         err_length.classList.add("error-text-hidden");

     }

     if (formIsValid === false) {
         event.preventDefault();

     }


 }



























