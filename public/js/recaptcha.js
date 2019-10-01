import { siteKey } from './recaptchaCreds.js'

grecaptcha.ready(() => {
  grecaptcha.execute(siteKey, { action: 'login' })
    .then(response => {
    // set login form recaptcha hidden input value
      const url = new URL(window.location.href)
      if (url.pathname === '/login' || url.pathname === '/login/create') {
        const recaptchaResponse = document.getElementById('recaptchaResponse')
        recaptchaResponse.value = response
        // enable login button
        const loginBtn = document.getElementById('loginBtn')
        loginBtn.disabled = false
      }
    })
})
