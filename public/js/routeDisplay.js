// Collapsible
window.addEventListener('DOMContentLoaded', function () {
  // get all collapsibles
  const collapsibles = [].slice.call(document.querySelectorAll('[id^=collapse]'))

  for (const collapsible of collapsibles) {
    const id = '#' + collapsible.id
    $(id).on('show.bs.collapse', function () {
      const card = $(this)
      const spinner = document.getElementById(`${collapsible.id}loader`)
      var xmlhttp = new XMLHttpRequest()

      xmlhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
          var response = JSON.parse(this.responseText)
          for (const route of response) {
            const driverName = ` ${route.name} ${route.last_name}`
            let paxCapacity = ''
            card.find('.card-body .driver-name').text(driverName)
            const paxIcon = card.find('.card-body .pax-icon')

            if (route.occupied === route.pax_capacity) {
              paxCapacity += 'Більше місць не залишилося. '
              paxIcon.addClass('text-warning')
            } else if (route.occupied === '0') {
              paxCapacity += 'Ще порожній. '
              paxIcon.addClass('text-success')
              card.find('.card-body .go-form').removeClass('invisible')
            } else {
              const ending = route.occupied === '1' ? 'ом. ' : 'ами. '
              paxCapacity = `З ${route.occupied} пасажир` + ending
              paxIcon.addClass('text-primary')
              card.find('.card-body .go-form').removeClass('invisible')
            }
            const freeSeats = `Усього місць: ${route.pax_capacity}`
            card.find('.card-body .free-seats').text(freeSeats)
            card.find('.card-body .pax-capacity').text(paxCapacity)
            card.find('.card-body .info-container').removeClass('invisible')
            spinner.classList.add('invisible')
          }
        }
      }
      xmlhttp.open('GET', 'routes/getRoute?routeId=' + this.dataset.routeId, true)
      xmlhttp.send()
    })
  }
})
