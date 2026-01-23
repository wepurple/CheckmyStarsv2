"use strict";
console.log("hello");
const notesContainer = document.querySelector(".notes-container");
const createBtn = document.querySelector(".btn");
let notes = document.querySelectorAll(".notes");

createBtn.addEventListener("click", () => {
  let input = document.createElement("p");
  let image = document.createElement("img");
  input.classList.add("notes");
  input.setAttribute("contenteditable", "true");
  image.src = "bootstrap 5.3/assets/delbtn.png";
  notesContainer.appendChild(input).appendChild(image);
});

notesContainer.addEventListener("click", function (e) {
  if (e.target.tagName.toLowerCase() === "img") {
    e.target.parentElement.remove();
    saveStorage();
  } else if (e.target.tagName.toLowerCase() === "p") {
    notes = document.querySelectorAll(".notes");
    notes.forEach((n) => {
      n.onkeyup = function () {
        saveStorage();
      };
    });
  }
});

function saveStorage() {
  localStorage.setItem("notes", notesContainer.innerHTML);
}

function displayStorage() {
  notesContainer.innerHTML = localStorage.getItem("notes");
}

displayStorage();
