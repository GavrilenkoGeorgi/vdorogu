  /**
   * Enable or disable car name text input field
   * 
   * @return void
   */
  function toggleCarNameField() {
    let textField = document.getElementById(`inputCarName`)
    textField.disabled = !textField.disabled
    textField.required = !textField.required
  }
  /**
   * Toggle password input visibility
   * 
   * @return void
  */
 function togglePasswordVis() {
  let passwordField = document.getElementById(`inputPassword`)
  if (passwordField.type === `password`) {
    passwordField.type = `text`;
  } else {
    passwordField.type = `password`;
  }
 }
 