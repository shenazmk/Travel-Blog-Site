function validateEmail(email){
    let emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    if(emailRegex.test(email))
        return true;
    else
        return false;
}

function validateScreenname(sname) {
	let snameRegEx = /^\w+$/;

	if (snameRegEx.test(sname))
		return true;
	else
		return false;
}
function validateDOB(dob) {
	let dobRegEx = /^\d{4}[-]\d{2}[-]\d{2}$/;

	if (dobRegEx.test(dob))
		return true;
	else
		return false;
}

function validateAvatar(avatar) {
	if (avatar.length === 0)
		return false;
	else
		return true;
}

function validatePwd(pwd) {
	if (pwd.length === 6 && /[^a-zA-Z]/.test(pwd))
		return true;
	else
		return false;
}

function snameHandler(event) {
	let sname = event.target;
	let sname_error = document.getElementById("err-sname");
	let sname_error_msg = document.getElementById("err-sname-msg");
	
	if (!validateScreenname(sname.value)) {
		//add a class name to <input> tag to highlight the input box.	
		sname.classList.add("invalid");
		//remove a class name to <p> tag to show the error message.	
		sname_error.classList.remove("hidden");
		sname_error_msg.classList.remove("none");
	}
	else {
		//remove a class name from the <input> tag to remove the highlights from the input box. 
		sname.classList.remove("invalid");
		//add a class name from the <p> tag to hide the error message.	
		sname_error.classList.add("hidden");
		sname_error_msg.classList.add("none");
	}
}

function dobHandler(event) {
	let dob = event.target;
	let dob_err = document.getElementById("err-dob");
	let dob_err_msg = document.getElementById("err-dob-msg");

	if (!validateDOB(dob.value)) {
		// Comment the line below
		//console.log("Date of birth '" + dob.value + "' is not valid.");
		//add a class name to <input> tag to highlight the input box.	
		dob.classList.add("invalid");
		//remove a class name to <p> tag to show the error message.	
		dob_err.classList.remove("hidden");
		dob_err_msg.classList.remove("none");
	}
	else {
		// Comment the line below
		//console.log("Date of birth is valid.");
		//remove a class name from the <input> tag to remove the highlights from the input box. 
		dob.classList.remove("invalid");
		//add a class name from the <p> tag to hide the error message.	
		dob_err.classList.add("hidden");
		dob_err_msg.classList.add("none");
	}
}


function cpwordHandler(event) {
    let pword = document.getElementById("pword");
    let cpword = event.target;
    let cpword_error = document.getElementById("err-cpword");
    let cpword_error_msg = document.getElementById("err-cpword-msg");

    if (pword.value !== cpword.value) {
        cpword.classList.add("invalid");
        cpword_error.classList.remove("hidden");
        cpword_error_msg.classList.remove("none");
        return false;
    }
    else {
        cpword.classList.remove("invalid");
        cpword_error.classList.add("hidden");
        cpword_error_msg.classList.add("none");
        return true;
    }
}

function pwordHandler(event) {
	let pword = event.target;
	let pword_error = document.getElementById("err-pword");
	let pword_error_msg = document.getElementById("err-pword-msg");

	if (!validatePwd(pword.value)) {
		//add a class name to <input> tag to highlight the input box.	
		pword.classList.add("invalid");
		//remove a class name to <p> tag to show the error message.	
		pword_error.classList.remove("hidden");
		pword_error_msg.classList.remove("none");
	}
	else {
		// remove a class name from the <input> tag to remove the highlights from the input box. 
		pword.classList.remove("invalid");
		// add a class name from the <p> tag to hide the error message.	
		pword_error.classList.add("hidden");
		pword_error_msg.classList.add("none");
	}
}

function avatarHandler(event) {
	let avatar = event.target;
    let avatar_error = document.getElementById("err-pp");
	let avatar_error_msg = document.getElementById("err-pp-msg");

	if (!validateAvatar(avatar.value)) {
		console.log("Avatar '" + avatar.value + "' is not valid.");
        avatar.classList.add("invalid");
        avatar_error.classList.remove("hidden");
		avatar_error_msg.classList.remove("none");
	}
	else {
		console.log("Avatar is valid.");
        avatar.classList.remove("invalid");
        avatar_error.classList.add("hidden");
		avatar_error_msg.classList.add("none");
	}
}

function emailHandler(event){
    let email = event.target;
    let email_error = document.getElementById("err-email");
	let email_error_msg = document.getElementById("err-email-msg");

    if (!validateEmail(email.value)) {
        console.log("Email '" + email.value + "' is not valid.");
        email.classList.add("invalid");
        email_error.classList.remove("hidden");
		email_error_msg.classList.remove("none");
    }
    else {
        console.log("Email is valid.");
        email.classList.remove("invalid");
        email_error.classList.add("hidden");
		email_error_msg.classList.add("none");
    }
}

function validateSignup(event) {
    let email = document.getElementById("email");
	let sname = document.getElementById("sname");
	let pword = document.getElementById("pword");
	let avatar = document.getElementById("pp");
	let dob = document.getElementById("dob");
	let cpword = document.getElementById("cpword");

	let formIsValid = true;

    if (!validateEmail(email.value)) {
		formIsValid = false;
	}

	if (!validateScreenname(sname.value)) {
		formIsValid = false;
	}
    
	if (!validateAvatar(avatar.value)) {
		formIsValid = false;
	}

    if (!validatePwd(pword.value)) {
		formIsValid = false;
	}

	if (!validateDOB(dob.value)) {
		formIsValid = false;
	}

	if (pword.value !== cpword.value) {
        cpword.classList.add("invalid");
        document.getElementById("err-cpword").classList.remove("hidden");
        document.getElementById("err-cpword-msg").classList.remove("none");
        formIsValid = false;
    } else {
        cpword.classList.remove("invalid");
        document.getElementById("err-cpword").classList.add("hidden");
        document.getElementById("err-cpword-msg").classList.add("none");
    }

	if (formIsValid === false) {
		event.preventDefault();
	}
	else {
		console.log("Validation successful, sending data to the server");
	}
}