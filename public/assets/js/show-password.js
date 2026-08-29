
"use strict"

// for show password 
// let createpassword = (type, ele) => {
//     document.getElementById(type).type = document.getElementById(type).type == "password" ? "text" : "password"
//     let icon = ele.childNodes[0].classList
//     let stringIcon = icon.toString()
//     if (stringIcon.includes("ri-eye-line")) {
//         ele.childNodes[0].classList.remove("ri-eye-line")
//         ele.childNodes[0].classList.add("ri-eye-off-line")
//     }
//     else {
//         ele.childNodes[0].classList.add("ri-eye-line")
//         ele.childNodes[0].classList.remove("ri-eye-off-line")
//     }
// }

// Add Code By Mohit

let createpassword = (inputId, ele) => {
    let passwordField = document.getElementById(inputId);
    if (!passwordField) {
        console.error("No element found with ID:", inputId);
        return;
    }

    passwordField.type = passwordField.type === "password" ? "text" : "password";

    let icon = ele.querySelector('i');
    if (!icon) return;

    if (icon.classList.contains("ri-eye-line")) {
        icon.classList.remove("ri-eye-line");
        icon.classList.add("ri-eye-off-line");
    } else {
        icon.classList.add("ri-eye-line");
        icon.classList.remove("ri-eye-off-line");
    }
};