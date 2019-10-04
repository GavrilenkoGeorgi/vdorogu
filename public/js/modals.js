document.addEventListener('DOMContentLoaded', () => {
  $('#cancelRoute').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var routeId = button.data('route-id') // Extract info from data-* attributes
    const origin = button.data('route-origin')
    const destination = button.data('route-destination')
    // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
    // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
    var modal = $(this)
    // eslint-disable-next-line quotes
    modal.find('.modal-body')
      .text(`Ви дійсно хочете скасувати маршрут з
        ${origin} до ${destination}?`)
    document.getElementById('cancelRouteForm').action = `/routes/removePassenger?routeId=${routeId}`
  })
})
