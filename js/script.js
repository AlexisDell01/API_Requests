import { initializeApp } from "https://www.gstatic.com/firebasejs/9.6.10/firebase-app.js";
import { getAnalytics } from "https://www.gstatic.com/firebasejs/9.6.10/firebase-analytics.js";
import {
  getAuth,
  signInWithEmailAndPassword,
  createUserWithEmailAndPassword,
} from "https://www.gstatic.com/firebasejs/9.6.10/firebase-auth.js";

// Your web app's Firebase configuration
const firebaseConfig = {
  apiKey: "AIzaSyD9oeEquAXCwaOSIYJtTJRvWH0b0uuWFzU",
  authDomain: "login-85fd0.firebaseapp.com",
  projectId: "login-85fd0",
  storageBucket: "login-85fd0.appspot.com",
  messagingSenderId: "410454299766",
  appId: "1:410454299766:web:7baa3e666bb270d9ecfb57",
  measurementId: "G-KL588K43L3",
};

const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);
const auth = getAuth(app);

// Set session persistence to LOCAL
getAuth().setPersistence(localStorage);

const submitButton = document.getElementById("submit");
const signupButton = document.getElementById("sign-up");
const emailInput = document.getElementById("email");
const passwordInput = document.getElementById("password");
const main = document.getElementById("main");
const createacct = document.getElementById("create-acct");

const signupEmailIn = document.getElementById("email-signup");
const confirmSignupEmailIn = document.getElementById("confirm-email-signup");
const signupPasswordIn = document.getElementById("password-signup");
const confirmSignUpPasswordIn = document.getElementById("confirm-password-signup");
const createacctbtn = document.getElementById("create-acct-btn");

const returnBtn = document.getElementById("return-btn");

var email,
  password,
  signupEmail,
  signupPassword,
  confirmSignupEmail,
  confirmSignUpPassword;

createacctbtn.addEventListener("click", function () {
  var isVerified = true;

  signupEmail = signupEmailIn.value;
  confirmSignupEmail = confirmSignupEmailIn.value;
  if (signupEmail != confirmSignupEmail) {
    window.alert("Email fields do not match. Try again.");
    isVerified = false;
  }

  signupPassword = signupPasswordIn.value;
  confirmSignUpPassword = confirmSignUpPasswordIn.value;
  if (signupPassword != confirmSignUpPassword) {
    window.alert("Password fields do not match. Try again.");
    isVerified = false;
  }

  if (
    signupEmail == null ||
    confirmSignupEmail == null ||
    signupPassword == null ||
    confirmSignUpPassword == null
  ) {
    window.alert("Please fill out all required fields.");
    isVerified = false;
  }

  if (isVerified) {
    createUserWithEmailAndPassword(auth, signupEmail, signupPassword)
      .then((userCredential) => {
        const user = userCredential.user;
        window.alert("Success! Account created.");

        // Set session variable (example: user email)
        localStorage.setItem("userEmail", user.email);
      })
      .catch((error) => {
        window.alert("Error occurred: " + error.message);
      });
  }
});

submitButton.addEventListener("click", function () {
  email = emailInput.value;
  password = passwordInput.value;

  signInWithEmailAndPassword(auth, email, password)
    .then((userCredential) => {
      const user = userCredential.user;

      // Set session variable (example: user email)
      localStorage.setItem("userEmail", user.email);

      console.log("Success! Welcome back!");
      window.alert("Success! Welcome back!");
      window.location.href = "index.php";

      // Verificar sesión después de iniciar sesión con éxito
      const userEmail = localStorage.getItem("userEmail");
      console.log("Session exists for user:", userEmail);
    })
    .catch((error) => {
      console.log("Error occurred: " + error.message);
      window.alert("Error occurred: " + error.message);
    });
});

signupButton.addEventListener("click", function () {
  main.style.display = "none";
  createacct.style.display = "block";
});

// Additional functionality if you want to check for an existing session
if (auth.currentUser) {
  const userEmail = localStorage.getItem("userEmail");
  console.log("Session exists for user:", userEmail);
  // Redirect or perform other actions as needed
}