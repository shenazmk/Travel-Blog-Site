
let submitBtn = document.querySelector('input[type="submit"]');
submitBtn.addEventListener("click", validateTextarea);
let inputField = document.getElementById("message");
let remainingChars = document.getElementById("remaining_chars");
const MAX_CHARS = 2000;
let charCount = document.getElementById("numOfChar");

// dynamic character function

inputField.addEventListener('input', (e) => {

    const NumofChar = e.target.value.length;

    charCount.textContent = `Character count: ${NumofChar}`;


    const remaining = MAX_CHARS - NumofChar;
    let colour = remaining < (MAX_CHARS * 0.1) ? 'red' : null;
    remainingChars.textContent = `Characters remaining: ${remaining}`;
    remainingChars.style.color = colour;

});

// form validation for blog creation page

function validateTextarea(event) {
    let formIsValid = true;

    const regEx = /[0-9a-z]/i;
    let err_box = document.getElementById("error-text-blank");


    if (!regEx.test(inputField.value)) {
        inputField.classList.add("highlight");
        err_box.classList.remove("error-text-hidden");
        formIsValid = false;

    }
    else {
        inputField.classList.remove("highlight");

        err_box.classList.add("error-text-hidden");

    }

    let titleField = document.getElementById("title");
    let err_title = document.getElementById("error-text-title");


    if (!regEx.test(titleField.value)) {
        titleField.classList.add("highlight");
        err_title.classList.remove("error-text-hidden");
        formIsValid = false;

    }
    else {
        titleField.classList.remove("highlight");

        err_title.classList.add("error-text-hidden");

    }

    let avatarRegEx = /^[^\n]+\.[a-zA-Z]{3,4}$/;

    let avatarField = document.getElementById("pic");
    let err_avatar = document.getElementById("error-text-avatar");


    if (!avatarRegEx.test(avatarField.value)) {
        avatarField.classList.add("highlight");
        err_avatar.classList.remove("error-text-hidden");
        formIsValid = false;

    }
    else {
        avatarField.classList.remove("highlight");

        err_avatar.classList.add("error-text-hidden");

    }

    let err_length = document.getElementById("error-text-length");

    if (inputField.value.length > MAX_CHARS) {
        err_length.classList.remove("error-text-hidden");
        formIsValid = false;
    }
    else {
        err_length.classList.add("error-text-hidden");

    }

    if (formIsValid === false) {
        event.preventDefault();

    }
    else {
		console.log("Validation successful, sending data to the server");
	}

}