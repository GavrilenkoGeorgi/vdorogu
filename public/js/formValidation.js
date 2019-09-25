'use strict'
window.addEventListener('load', function () {
  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  var forms = document.getElementsByClassName('needs-validation')
  // Loop over them and prevent submission
  // eslint-disable-next-line no-unused-vars
  var validation = Array.prototype.filter.call(forms, function (form) {
    form.addEventListener('submit', function (event) {
      if (form.checkValidity() === false) {
        event.preventDefault()
        event.stopPropagation()
      }
      form.classList.add('was-validated')
    }, false)
  })
}, false)

/**
* Validate the create route form
*/
$(document).ready(function () {
  $('#createRouteForm').validate({
    rules: {
      routeOrigin: {
        required: true,
        maxlength: 100
      },
      routeDestination: {
        required: true,
        maxlength: 100
      },
      createDepartureDate: {
        required: true,
        dateISO: true
      },
      paxCapacity: {
        required: true,
        digits: true,
        range: [1, 5]
      }
    },
    messages: {
      routeOrigin: {
        required: 'Де ви знаходитесь?'
      },
      routeDestination: {
        required: 'Куди ви їдете?'
      },
      paxCapacity: {
        required: 'Ви їдете один?'
      }
    },
    success: function (label, element) {
      label.parent().removeClass('error')
      label.remove()
    }
  })
})

/**
* Validate the find route form
*/
$(document).ready(function () {
  $('#findRouteForm').validate({
    rules: {
      routeOrigin: {
        required: true,
        maxlength: 100
      },
      routeDestination: {
        maxlength: 100
      }
    },
    messages: {
      routeOrigin: {
        required: 'Де ви знаходитесь?',
        maxlength: 'Це занадто далеко.'
      },
      routeDestination: {
        maxlength: 'Це занадто далеко.'
      }
    },
    success: function (label, element) {
      label.parent().removeClass('error')
      label.remove()
    }
  })
})
