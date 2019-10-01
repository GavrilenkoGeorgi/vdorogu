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
 * Add jQuery Validation plugin method for a valid password
 *
 * Valid password contain at least one letter and one number
 */
$.validator.addMethod('validPassword',
  function (value, element, param) {
    if (value !== '') {
      if (value.match(/.*[a-z]+.*/i) == null) {
        return false
      }
      if (value.match(/.*\d+.*/) == null) {
        return false
      }
    }
    return true
  },
  'Повинен містити принаймні одну букву та одне число.'
)

/**
* Validate the create route form !this!
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

$(document).ready(function () {
  /**
  * Validate the find route form
  */
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

  /**
   * Validate the signup form
   */
  $('#formSignup').validate({
    rules: {
      name: {
        required: true
      },
      lastName: 'required',
      email: {
        required: true,
        email: true,
        remote: '/account/validate-email'
      },
      password: {
        required: true,
        minlength: 6,
        validPassword: true
      },
      birthDate: 'required',
      carName: 'required',
      terms: 'required'
    },
    messages: {
      email: {
        required: 'Це поле є обов\'язковим.',
        remote: 'E-mail вже зайнято.',
        minlength: '6'
      },
      password: {
        required: 'Це поле є обов\'язковим.',
        validPassword: 'Цифри та букви!'
      },
      name: {
        required: 'Як вас звати.'
      },
      lastName: {
        required: 'Ваше прізвище.'
      },
      gender: {
        required: 'Ви повинні вирішити.'
      },
      birthDate: {
        required: 'Перевірте це?'
      },
      carName: {
        required: 'Введіть назву виробника та модель вашого особистого транспортного засобу?'
      },
      terms: {
        required: 'Ви згодні?'
      }
    }
  })
  /**
   * Validate login form
   */
  $('#loginForm').validate({
    errorPlacement: function (error, element) {
      if (element.parent('.input-group').length) {
        error.insertAfter(element.parent())
      } else {
        error.insertAfter(element)
      }
    },
    onError: function () {
      $('.input-group.error-class').find('.help-block.form-error').each(function () {
        $(this).closest('.form-group').addClass('error-class').append($(this))
      })
    },
    rules: {
      email: {
        required: true,
        email: true
      },
      password: {
        required: true
      }
    },
    messages: {
      email: {
        required: 'Це поле є обов\'язковим.',
        email: 'Неправильна електронна пошта.'
      },
      password: {
        required: 'Це поле є обов\'язковим.'
      }
    }
  })
  /**
   * Validate the profile edit form
   */
  $('#formProfile').validate({
    rules: {
      name: {
        required: true
      },
      lastName: 'required',
      email: {
        required: true,
        email: true,
        remote: '/account/validate-email'
      },
      password: {
        required: true,
        minlength: 6,
        validPassword: true
      },
      birthDate: 'required',
      carName: 'required',
      terms: 'required'
    },
    messages: {
      email: {
        required: 'Це поле є обов\'язковим.',
        remote: 'E-mail вже зайнято.',
        minlength: '6'
      },
      password: {
        required: 'Це поле є обов\'язковим.',
        validPassword: 'Цифри та букви!'
      },
      name: {
        required: 'Як вас звати.'
      },
      lastName: {
        required: 'Ваше прізвище.'
      },
      gender: {
        required: 'Ви повинні вирішити.'
      },
      birthDate: {
        required: 'Перевірте це?'
      },
      carName: {
        required: 'Введіть назву виробника та модель вашого особистого транспортного засобу?'
      },
      terms: {
        required: 'Ви згодні?'
      }
    }
  })
})
