document.addEventListener("DOMContentLoaded", () => {

    /*
    Dark / Light Mode
    */

    const themeButton = document.getElementById("themeToggle");
    const themeIcon = themeButton?.querySelector("i");
    const savedTheme = localStorage.getItem(
        "fundbridge-theme"
    );

    if (savedTheme === "light") {
        document.body.classList.add("light");
        if (themeIcon) {
            themeIcon.className =
                "fa-solid fa-sun";
        }
    }

    themeButton?.addEventListener("click", () => {
        document.body.classList.toggle("light");

        if (document.body.classList.contains("light")) {
            localStorage.setItem(
                "fundbridge-theme",
                "light"
            );

            if (themeIcon) {
                themeIcon.className =
                    "fa-solid fa-sun";
            }
        } else {
            localStorage.setItem(
                "fundbridge-theme",
                "dark"
            );
            if (themeIcon) {
                themeIcon.className =
                    "fa-solid fa-moon";
            }
        }
    });

    /*
    Register Founder / Investor Switching
    */

    const founderBtn = document.getElementById("founderBtn");
    const investorBtn = document.getElementById("investorBtn");
    const founderForm = document.getElementById("founderForm");
    const investorForm = document.getElementById("investorForm");
    const roleInput = document.getElementById("role");
    const accountTitle = document.getElementById("accountTitle");

    if (founderBtn && investorBtn && founderForm && investorForm) {
        founderBtn.addEventListener(
            "click",
            () => {
                founderBtn.classList.add("active");
                investorBtn.classList.remove("active");
                founderForm.style.display ="block";
                investorForm.style.display ="none";
                if (roleInput) {
                    roleInput.value ="founder";
                }
                if (accountTitle) {
                    accountTitle.innerHTML = "Create Account";
                }
            }
        );
        investorBtn.addEventListener(
            "click",
            () => {
                investorBtn.classList.add("active");
                founderBtn.classList.remove("active");
                founderForm.style.display ="none";
                investorForm.style.display ="block";
                if (roleInput) {
                    roleInput.value ="investor";
                }
                if (accountTitle) {
                    accountTitle.innerHTML ="Create Investor Account";
                }
            }
        );
    }

    /*
    Login Founder / Investor Switching
    */

    const founderLoginBtn = document.getElementById("founderLogin");
    const investorLoginBtn = document.getElementById("investorLogin");
    const founderLoginForm =document.getElementById("founderLoginForm");
    const investorLoginForm =document.getElementById("investorLoginForm");
    const loginTitle =document.getElementById("loginTitle");
    if (founderLoginBtn && investorLoginBtn && founderLoginForm && investorLoginForm) {
        founderLoginBtn.addEventListener(
            "click",
            () => {
                founderLoginBtn.classList.add(
                    "active"
                );
                investorLoginBtn.classList.remove(
                    "active"
                );
                founderLoginForm.style.display =
                    "block";
                investorLoginForm.style.display =
                    "none";
                if (loginTitle) {
                    loginTitle.innerHTML =
                        "Welcome Back Founder";
                }
            }
        );
        investorLoginBtn.addEventListener(
            "click",
            () => {
                investorLoginBtn.classList.add(
                    "active"
                );
                founderLoginBtn.classList.remove(
                    "active"
                );
                founderLoginForm.style.display =
                    "none";
                investorLoginForm.style.display =
                    "block";
                if (loginTitle) {
                    loginTitle.innerHTML =
                        "Welcome Back Investor";
                }
            }
        );
    }
});