document.addEventListener(`DOMContentLoaded`, () => {
  // Parallax
  var image = document.getElementsByClassName('thumbnail')
  console.log(image)
  new simpleParallax(image, {
    delay: 2,
    transition: 'cubic-bezier(0,0,0,1)',
    orientation: 'right'
  })

  console.log(`Hi from js`)
})