import { siteKey } from './recaptchaCreds.js'

grecaptcha.ready(() => {
  grecaptcha.execute(siteKey, {action: 'login'})
  .then(response => {
    // set login form recaptcha hidden input value
    let url = new URL(window.location.href)
    if (url.pathname === `/login` || url.pathname === `/login/create`) {
      let recaptchaResponse = document.getElementById('recaptchaResponse')
      recaptchaResponse.value = response
      // enable login button
      let loginBtn = document.getElementById('loginBtn')
      loginBtn.disabled = false
    }
  })
})
