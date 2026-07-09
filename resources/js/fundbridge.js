document.addEventListener(
    "DOMContentLoaded",
    () => {


        const button =
            document.getElementById(
                "themeToggle"
            );



        const icon =
            button?.querySelector("i");



        let theme =
            localStorage.getItem(
                "fundbridge-theme"
            );



        if (theme === "light") {

            document.body.classList.add("light");


            if (icon) {

                icon.className =
                    "fa-solid fa-sun";

            }

        }




        button?.addEventListener(
            "click",
            () => {


                document.body.classList.toggle(
                    "light"
                );



                if (
                    document.body.classList.contains("light")
                ) {


                    localStorage.setItem(
                        "fundbridge-theme",
                        "light"
                    );



                    if (icon)

                        icon.className =
                            "fa-solid fa-sun";


                }

                else {


                    localStorage.setItem(
                        "fundbridge-theme",
                        "dark"
                    );



                    if (icon)

                        icon.className =
                            "fa-solid fa-moon";


                }



            });


    });