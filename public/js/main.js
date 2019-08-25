
document.addEventListener(`DOMContentLoaded`, () => {
  // manage sort links active state
  // get currently set sort type
  let url = new URL(window.location.href)
  let sort = url.searchParams.get(`sort`)
  if (sort) {
    // remove currently set class
    let previousActiveLink = document.querySelector(`.active-sort`)
    previousActiveLink.classList.remove(`active-sort`)
    // add class to the new link
    let currentActiveLink = document.getElementById(sort)
    currentActiveLink.classList.add(`active-sort`)
  }

  // if we got from from previous user sort
  let order = url.searchParams.get(`order`)
  if (order) {
    // Got new order
    document.getElementById(order).checked = true
    setOrder(order)
  } else {
    // Default order
    let defaultCheckedRadio = document.getElementById(`asc`)
    if (defaultCheckedRadio) {
      defaultCheckedRadio.checked = true
    }
  }

  // Radio buttons to set order
  let sortRadios = document.getElementsByName(`sortOrder`)
  for (let radio of sortRadios) {
    radio.addEventListener(`click`, (event) => {
      setOrder(event.target.id)
    })
  }

  // trim current url string and set new order
  function setOrder (displayOrder) {
    // set new radio button state
    document.getElementById(displayOrder).checked = true

    // modify links
    let sortingLinks = document.querySelectorAll(`.sort-link`)
    modifyLinks(sortingLinks, displayOrder)

    let paginationLinks = document.querySelectorAll(`.page-link`)
    modifyLinks(paginationLinks, displayOrder)
  }
  function modifyLinks(links, displayOrder) {
    for (link of links) {
      let href = new URL(link.href)
      href.searchParams.set(`order`, displayOrder)
      link.href = href.toString()
    }
  }
})
