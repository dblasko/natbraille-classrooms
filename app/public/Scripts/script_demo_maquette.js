function simulateLogin(event) {
    document.getElementById("logged-in-content").hidden = false;
    document.getElementById("not-logged-in-content").hidden = true;

    let headerLinks = document.getElementsByClassName("headerLink");
    headerLinks[1].innerText = "Mon compte";
    headerLinks[2].innerText = "Me déconnecter";
}