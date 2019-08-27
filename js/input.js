import 'nodelist-foreach-polyfill'

class MapboxGeojson {
  constructor (el) {
    this.el = el
    this.key = el.attr('data-access-token')
    this.fieldKey = el.context.dataset.key
    this.map = null
    this.marker = null
    this.mapDOM = el.find('.mapbox-geojson-map')
    this.inputField = el.find('.mapbox-geojson-field')
    this.container = 'mapbox-geojson-map'
    this.zoom = 14
    this.street = document.querySelector('input.mapbox-geojson__street')
    this.city = document.querySelector('input.mapbox-geojson__city')
    this.state = document.querySelector('select.mapbox-geojson__state')
    this.zip = document.querySelector('input.mapbox-geojson__zip')
    this.lat = '40.7249832'
    this.lng = '-74.0082994'

    // Check if there's already a value
    this.fromSaved = this.getValue()

    // Build the map/marker
    this.buildMap()
    this.buildMarker()

    // register events
    this.registerEvents()
  }
  buildMap () {
    mapboxgl.accessToken = this.key // eslint-disable-line
    this.map = new mapboxgl.Map({ // eslint-disable-line
      container: this.container,
      center: [this.lng, this.lat],
      style: this.el.attr('data-map-style'),
      zoom: this.zoom
    })
  }
  buildMarker () {
    const el = document.createElement('div')
    el.className = 'acf-mapbox-marker'
    // el.style.backgroundImage = 'url("../icon/icon-map.svg")'
    // el.style.backgroundColor = 'red'
    // el.style.width = '30px'
    // el.style.height = '30px'
    this.marker = new mapboxgl.Marker({ // eslint-disable-line
      draggable: true,
      element: el
    })
    if (this.fromSaved) {
      this.getLocalityByLngLat(this.lng, this.lat)
    }
  }
  getLocalityByLngLat (lng = '-74.0082994', lat = '40.7249832') {
    this.lng = lng
    this.lat = lat
    const icon = document.querySelector('#mapbox-geojson__clear .load-icon')
    icon.classList.add('loading')
    axios // eslint-disable-line
      .get(
        `https://api.mapbox.com/geocoding/v5/mapbox.places/${this.lng},${
          this.lat
        }.json?access_token=${this.key}`
      )
      .then(res => {
        this.marker.setLngLat([this.lng, this.lat]).addTo(this.map)
        this.setValue()
        icon.classList.remove('loading')
      })
      .catch(function (err) {
        console.log(err)
        icon.classList.remove('loading')
      })
  }
  getLocalitiesByText () {
    const requestText = encodeURI(
      this.street.value +
        ' ' +
        this.city.value +
        ' ' +
        this.state.value +
        ' ' +
        this.zip.value
    )
    const icon = document.querySelector('#mapbox-geojson__clear .load-icon')
    icon.classList.add('loading')
    axios // eslint-disable-line
      .get(
        `https://api.mapbox.com/geocoding/v5/mapbox.places/${requestText}.json?access_token=${
          this.key
        }&limit=1`
      )
      .then(res => {
        // re-center the ma
        const data = res.data.features[0]
        this.map.easeTo({ center: res.data.features[0].center })
        this.getLocalityByLngLat(data.center[0], data.center[1])
        this.setValue()
        icon.classList.remove('loading')
      })
      .catch(function (err) {
        console.log(err)
        icon.classList.remove('loading')
      })
  }
  clearData () {
    this.street.value = ''
    this.city.value = ''
    this.state.value = ''
    this.zip.value = ''
    this.lat = '40.7249832'
    this.lng = '-74.0082994'
    this.map = null
    this.marker = null
    this.buildMap()
    this.buildMarker()
    this.registerEvents()
  }
  onDragEnd () {
    const lngLat = this.marker.getLngLat()
    this.getLocalityByLngLat(lngLat.lng, lngLat.lat)
  }
  setValue () {
    // set the value
    let value = {
      street: this.street.value,
      city: this.city.value,
      state: this.state.value,
      zip: this.zip.value,
      lat: this.lat,
      lng: this.lng
    }
    value = JSON.stringify(value)
    this.validateForm()

    const field = document.querySelector(
      '#mapbox-geojson-value__' + this.fieldKey
    )
    field.value = value
  }
  getValue () {
    const field = document.querySelector(
      '#mapbox-geojson-value__' + this.fieldKey
    )
    if (field.value) {
      const value = JSON.parse(field.value)
      this.street.value = value.street
      this.city.value = value.city
      this.state.value = value.state
      this.zip.value = value.zip
      this.lat = value.lat
      this.lng = value.lng
      return true
    }
    return false
  }

  // EVENT HANDLERS
  registerEvents () {
    this.mapClickHandler()
    this.recenterMapHandler()
    this.dragHandler()
    this.clearHandler()

    if (!document.querySelector('body').classList.contains('wp-admin')) {
      document.querySelector('input.mapbox-geojson__city').addEventListener('focusout', (e) => {
        e.target.classList.add('touched')
        this.validateForm()
      })

      document.querySelector('select.mapbox-geojson__state').addEventListener('change', (e) => {
        e.target.classList.add('touched')
        this.validateForm()
      })

      document.querySelector('select.mapbox-geojson__state').addEventListener('focusout', (e) => {
        e.target.classList.add('touched')
        this.validateForm()
      })

      document.querySelector('input.mapbox-geojson-value').addEventListener('oninput', (e) => {
        this.validateForm()
      })

      document.querySelector('input.mapbox-geojson-value').addEventListener('change', (e) => {
        this.validateForm()
      })

      document.querySelector('#mapbox-geojson__find').addEventListener('click', (e) => {
        this.validateForm()
      })

      this.validateForm()
    }
  }
  clearHandler () {
    const self = this
    document
      .querySelector('#mapbox-geojson__clear')
      .addEventListener('click', function (e) {
        e.preventDefault()
        self.clearData()
      })
  }
  dragHandler () {
    const self = this
    this.marker.on('dragend', function () {
      self.onDragEnd()
    })
  }
  mapClickHandler () {
    const self = this
    this.map.on('click', function (e) {
      self.getLocalityByLngLat(e.lngLat.lng, e.lngLat.lat)
    })
  }
  recenterMapHandler () {
    const self = this
    document
      .querySelector('#mapbox-geojson__find')
      .addEventListener('click', function (e) {
        e.preventDefault()
        self.getLocalitiesByText()
      })
  }

  // FORM HANDLERS/VALIDATION
  appendHtml (el, html) {
    el.insertAdjacentHTML('beforeend', html)
    return el
  }
  insertError (errorMsg, input) {
    const span = document.createElement('span')
    this.appendHtml(span, errorMsg)
    span.classList.add('error')
    span.classList.add('form-error__span')
    input.classList.add('form-error__input')
    this.appendNode(input, span)
  }
  appendNode (el, node) {
    el.parentNode.insertBefore(node, el.nextSibling)
    return el
  }
  remove (el) {
    el.parentNode.removeChild(el)
  }
  clearErrors (selector, type = 'input') {
    const spans = document.querySelectorAll(`${selector} .form-error__span`)
    const input = document.querySelector(`${selector} ${type}`)
    input.classList.remove('form-error__input')
    spans.forEach((span) => {
      this.remove(span)
    })
  }
  validateForm () {
    const cityInput = document.querySelector('input.mapbox-geojson__city')
    const stateInput = document.querySelector('select.mapbox-geojson__state')
    const value = document.querySelector('input.mapbox-geojson-value')
    let json = (value.value) ? JSON.parse(value.value) : null
    const submitBtn = document.querySelector('.acf-form-submit input')
    let hasErrors = false

    this.clearErrors('.mapbox-geojson__city .acf-input-wrap')
    if (cityInput.classList.contains('touched')) {
      if (cityInput.value === '' || !cityInput.value) {
        this.insertError('* This field is required', cityInput)
        hasErrors = true
      }
    }

    this.clearErrors('.mapbox-geojson__state .acf-input-wrap', 'select')
    if (stateInput.classList.contains('touched')) {
      if (stateInput.value === '' || !stateInput.value) {
        this.insertError('* This field is required', stateInput)
        hasErrors = true
      }
    }

    if (!json || json.city === '' || json.state === '' || json.lat === '' || json.lng === '') {
      hasErrors = true
    }

    if (hasErrors) {
      submitBtn.disabled = true
    } else {
      submitBtn.disabled = false
    }
  }
}
(function ($) {
  function initializeField ($el, acf) { // eslint-disable-line
    const mapbox = new MapboxGeojson($el.find('.mapbox-geojson-map')) // eslint-disable-line
  }

  if (typeof acf.add_action !== 'undefined') { // eslint-disable-line
    /*
     *  ready append (ACF5)
     *
     *  These are 2 events which are fired during the page load
     *  ready = on page load similar to $(document).ready()
     *  append = on new DOM elements appended via repeater field
     *
     *  @type    event
     *  @date    20/07/13
     *
     *  @param   $el (jQuery selection) the jQuery element which contains the ACF fields
     *  @return  n/a
     */

    acf.add_action('ready append', function ($el) { // eslint-disable-line
      // search $el for fields of type 'mapbox_geojson'
      acf.get_fields({ type: 'mapbox_geojson' }, $el).each(function () { // eslint-disable-line
        initializeField($(this))
      })
    })
  } else {
    /*
     *  acf/setup_fields (ACF4)
     *
     *  This event is triggered when ACF adds any new elements to the DOM.
     *
     *  @type    function
     *  @since   1.0.0
     *  @date    01/01/12
     *
     *  @param   event       e: an event object. This can be ignored
     *  @param   Element     postbox: An element which contains the new HTML
     *
     *  @return  n/a
     */

    $(document).on('acf/setup_fields', function (e, postbox) {
      $(postbox)
        .find('.field[data-field_type="mapbox_geojson"]')
        .each(function () {
          initializeField($(this))
        })
    })
  }
})(jQuery) // eslint-disable-line
