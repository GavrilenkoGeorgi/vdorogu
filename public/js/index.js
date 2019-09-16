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
/*
$(function () {
  $('[data-toggle="popover"]').popover()
  console.log('hi')
}) */

document.addEventListener('DOMContentLoaded', () => {
  $('[data-toggle="popover"]').popover()
  $('.popover-dismiss').popover({
    trigger: 'focus'
  })
})
