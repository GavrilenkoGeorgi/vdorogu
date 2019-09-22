/**
 * Enable or disable car name text input field
 *
 * @return void
 */
function toggleCarNameField() {
  const textField = document.getElementById('inputCarName')
  textField.disabled = !textField.disabled
  textField.required = !textField.required
}
/**
 * Toggle password input visibility
 *
 * @return void
*/
function togglePasswordVis() {
  const passwordField = document.getElementById('inputPassword')
  if (passwordField.type === 'password') {
    passwordField.type = 'text'
  } else {
    passwordField.type = 'password'
  }
}

document.addEventListener('DOMContentLoaded', () => {
  $('[data-toggle="popover"]').popover()
  $('.popover-dismiss').popover({
    trigger: 'focus'
  })

  // Parallax
  const image = document.getElementsByClassName('thumbnail')
  const carImage = new simpleParallax(image, {
    delay: 2,
    transition: 'cubic-bezier(0,0,0,1)',
    orientation: 'right'
  })
})

// Datalists for main page forms

window.addEventListener('DOMContentLoaded', function () {
  //
  // Create route origin list suggestions
  //
  var createRouteOrigin = document.getElementById('createRouteOrigin')
  var createRouteOriginList = document.getElementById('createRouteOriginList')
  // Add a keyup event listener to our input element
  if (createRouteOriginList) {
    createRouteOrigin.addEventListener('keyup', function (event) { hinter(event, createRouteOriginList) })
  }

  // Search route destination list sugestions
  var createRouteDestination = document.getElementById('createRouteDestination')
  var createRouteDestinationList = document.getElementById('createRouteDestinationList')
  if (createRouteDestinationList) {
    createRouteDestination.addEventListener('keyup', function (event) { hinter(event, createRouteDestinationList) })
  }

  // and set current date to the input
  $('#createDepartureDate').val(new Date().toISOString().slice(0, 10))

  //
  // Search route origin list sugestions
  //
  var searchRouteOrigin = document.getElementById('searchRouteOrigin')
  var searchRouteOriginList = document.getElementById('searchRouteOriginList')
  // Add a keyup event listener to our input element
  if (searchRouteOriginList) {
    searchRouteOrigin.addEventListener('keyup', function (event) { hinter(event, searchRouteOriginList) })
  }

  // Search route destination list sugestions
  var searchRouteDestination = document.getElementById('searchRouteDestination')
  var searchRouteDestinationList = document.getElementById('searchRouteDestinationList')
  if (searchRouteDestinationList) {
    searchRouteDestination.addEventListener('keyup', function (event) { hinter(event, searchRouteDestinationList) })
  }

  // create one global XHR object
  // so we can abort old requests when a new one is made
  window.hinterXHR = new XMLHttpRequest()
})

// Autocomplete for search and create routes forms
function hinter (event, suggestionList) {
  // retrieve the input element
  var input = event.target

  // minimum number of characters before we start generating suggestions
  var minCharacters = 3

  if (input.value.length < minCharacters) {
    return false
  } else {
    // abort any pending requests
    window.hinterXHR.abort()

    window.hinterXHR.onreadystatechange = function () {
      if (this.readyState === 4 && this.status === 200) {
        // We're expecting a json response so we convert it to an object
        var response = JSON.parse(this.responseText)

        // clear any previously loaded options in the datalist
        suggestionList.innerHTML = ''

        response.forEach(function (item) {
          // Create a new <option> element.
          var option = document.createElement('option')
          option.value = item

          // attach the option to the datalist element
          suggestionList.appendChild(option)
        })
      }
    }
    window.hinterXHR.open('GET', '/search/city?query=' + input.value, true)
    window.hinterXHR.send()
  }
}
