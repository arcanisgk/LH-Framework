console.log("Hello World!!!");

window.addEventListener("load", () => setTimeout(() => {
    console.log('Full Load!!!');
    const fragment = window.location.hash;
    if (fragment) {
        const element = document.querySelector(fragment);
        console.log(element);
        if (element) {
            element.scrollIntoView({behavior: "smooth", block: "nearest", inline: "nearest"});
        }
    }
}, 1500));

$(document).on('click', '#liveToastBtn', function (e) {
    e.preventDefault();

    $('#liveToast').toast('show');

    $(this).blur();
});