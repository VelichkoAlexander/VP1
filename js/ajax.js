(function () {
    let errorPopup = document.getElementById('error');
    let burgerForm = document.getElementById('order-form');
    burgerForm.addEventListener('submit', function (e) {
        e.preventDefault();
        let formData = new FormData(burgerForm);
        fetch('./order.php', {
            method: 'POST',
            body: formData
        }).then((res) => res.json())
            .then((data) => {
                console.log(data);
                if (data.error) {
                    errorPopup.querySelector('.error-message').innerHTML = data.error;
                    $.fancybox('#error');
                } else if (data.success) {
                    $.fancybox('#success');
                    burgerForm.reset();

                }
            })
            .catch((error) => console.error(error));
    });

})();