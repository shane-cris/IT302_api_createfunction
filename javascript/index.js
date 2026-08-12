// login <-> signup toggle
const login = document.getElementById('Log_in');
const signup = document.getElementById('sign_up');

function signuppage() {
  login.style.display = 'none';
  signup.style.display = 'block';
}

function loginpage() {
  signup.style.display = 'none';
  login.style.display = 'block';
}

// user / staff role toggle
const btns = document.querySelectorAll('.btns');
const authsections = document.querySelectorAll('.authsection');

function slideNav(manual) {
  btns.forEach((btn) => btn.classList.remove('active'));
  authsections.forEach((section) => section.classList.remove('active'));

  btns[manual].classList.add('active');
  authsections[manual].classList.add('active');
}

btns.forEach((btn, i) => {
  btn.addEventListener('click', () => slideNav(i));
});