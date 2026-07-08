



document.addEventListener("DOMContentLoaded", function () {



    const themeButton = document.getElementById("themeToggle");

    const icon = themeButton.querySelector("i");





    /*
        Load saved theme
    */


    const savedTheme = localStorage.getItem("fundbridge-theme");



    if (savedTheme === "light") {


        document.body.classList.add("light");


        icon.classList.remove("fa-moon");

        icon.classList.add("fa-sun");


    }







    /*
        Toggle Theme
    */


    themeButton.addEventListener("click", function () {



        document.body.classList.toggle("light");



        if (document.body.classList.contains("light")) {


            localStorage.setItem(
                "fundbridge-theme",
                "light"
            );


            icon.classList.remove(
                "fa-moon"
            );


            icon.classList.add(
                "fa-sun"
            );



        }

        else {


            localStorage.setItem(
                "fundbridge-theme",
                "dark"
            );


            icon.classList.remove(
                "fa-sun"
            );


            icon.classList.add(
                "fa-moon"
            );


        }



    });





});









/* =====================================================
   Smooth Scroll Navigation
===================================================== */


document.querySelectorAll("nav a").forEach(link => {


    link.addEventListener("click", function (e) {


        if (this.getAttribute("href").startsWith("#")) {


            e.preventDefault();


            const section =
                document.querySelector(
                    this.getAttribute("href")
                );


            if (section) {


                section.scrollIntoView({

                    behavior: "smooth"

                });


            }


        }



    });


});









/* =====================================================
   Card Hover Animation
===================================================== */


const cards =
    document.querySelectorAll(
        ".feature-card,.startup-card,.testimonial-card"
    );



cards.forEach(card => {


    card.addEventListener(
        "mouseenter",
        () => {


            card.style.transform =
                "translateY(-8px)";


        });



    card.addEventListener(
        "mouseleave",
        () => {


            card.style.transform =
                "translateY(0)";


        });


});









/* =====================================================
   Scroll Reveal Animation
===================================================== */


const observer =
    new IntersectionObserver(
        (entries) => {


            entries.forEach(entry => {


                if (entry.isIntersecting) {


                    entry.target.classList.add(
                        "show"
                    );


                }


            });


        },
        {
            threshold: 0.15
        }

    );



document
    .querySelectorAll(
        "section,.feature-card,.step,.startup-card"
    )
    .forEach(
        (el) => observer.observe(el)
    );
