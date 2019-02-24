(function () {

    let burgerForm = document.getElementById('order-form');
    burgerForm.addEventListener('submit', function (e) {
        e.preventDefault();
        let formData = new FormData(burgerForm);
        fetch('./order.php', {
            method: 'POST',
            body: formData
        }).then((res) => res.text())
            .then((text) => console.log(text))
            .catch((error) => console.error(error));
    });

})();