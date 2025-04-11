function setupConfirmation(formSelector, buttonSelector, message) {
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector(formSelector);
        const button = document.querySelector(buttonSelector);

        if (button && form) {
            button.addEventListener('click', function (event) {
                event.preventDefault(); // Empêche l'envoi immédiat du formulaire
                const confirmation = confirm(message);

                if (confirmation) {
                    form.submit();
                } else {
                    console.log('Action annulée');
                }
            });
        }
    });
}