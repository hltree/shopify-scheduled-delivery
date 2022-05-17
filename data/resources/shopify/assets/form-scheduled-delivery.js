document.addEventListener('DOMContentLoaded', function() {
    flatpickr('#scheduled-delivery', {
        mode: "multiple",
        enableTime: true,
        dateFormat: "Y-m-d H:i"
    })

    var fsd = document.querySelector('#scheduled-delivery')
    var fsdh = document.querySelector('#scheduled-delivery-hidden')
    if (fsd && fsdh) {
        var inputName = fsd.attributes.name.textContent
        var matches = inputName.match(/^attributes\[(.*)\]/)
        if (matches[1]) {
            fsdh.setAttribute('value', matches[1])
        }
    }
});
