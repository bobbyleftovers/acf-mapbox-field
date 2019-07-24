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
    this.street = document.querySelector('.mapbox-geojson__street')
    this.city = document.querySelector('.mapbox-geojson__city')
    this.state = document.querySelector('.mapbox-geojson__state')
    this.zip = document.querySelector('.mapbox-geojson__zip')
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
    mapboxgl.accessToken = this.key
    this.map = new mapboxgl.Map({
      container: this.container,
      center: [this.lng, this.lat],
      style: this.el.attr('data-map-style'),
      zoom: this.zoom
    })
  }
  buildMarker () {
    this.marker = new mapboxgl.Marker({
      draggable: true
    })
    if (this.fromSaved) {
      this.getLocalityByLngLat(this.lng, this.lat)
    }
  }
  getLocalityByLngLat (lng = '-74.0082994', lat = '40.7249832') {
    this.lng = lng
    this.lat = lat
    console.log(lng, lat)
    axios.get(`https://api.mapbox.com/geocoding/v5/mapbox.places/${this.lng},${this.lat}.json?access_token=${this.key}`)
      .then(res => {
        this.marker
          .setLngLat([this.lng, this.lat])
          .addTo(this.map)
        this.setValue()
      })
  }
  getLocalitiesByText () {
    // if (!this.hasErrors()) {
    const requestText = encodeURI(this.street.value + ' ' + this.city.value + ' ' + this.state.value + ' ' + this.zip.value)
    axios.get(`https://api.mapbox.com/geocoding/v5/mapbox.places/${requestText}.json?access_token=${this.key}&limit=1`)
      .then(res => {
        // re-center the map
        console.log(res.data.features[0])
        const data = res.data.features[0]
        this.map.easeTo({ center: res.data.features[0].center })
        this.getLocalityByLngLat(data.center[0], data.center[1])
        this.setValue()
      })
    // }
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
    // console.log(this.marker)
    this.getLocalityByLngLat(lngLat.lng, lngLat.lat)

    // console.log('LngLat: ', lngLat)
  }
  hasErrors () {
    let hasErrors = false
    document.querySelectorAll('.mapbox-geojson__input').forEach(function (input) {
      input.classList.remove('error')
      if (!input.value) {
        input.classList.add('error')
        hasErrors = true
      }
    })
    return hasErrors
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

    const field = document.querySelector('#mapbox-geojson-value__' + this.fieldKey)
    // console.log(field)
    field.value = value
    // inputField.value
  }
  getValue () {
    const field = document.querySelector('#mapbox-geojson-value__' + this.fieldKey)
    console.log(field.value)
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
  }
  clearHandler () {
    const self = this
    document.querySelector('#mapbox-geojson__clear').addEventListener('click', function (e) {
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
    document.querySelector('#mapbox-geojson__find').addEventListener('click', function (e) {
      e.preventDefault()
      self.getLocalitiesByText()
    })
  }
}
(function ($) {
  function initializeField ($el, acf) {
    const mapbox = new MapboxGeojson($el.find('.mapbox-geojson-map'))
  }

  if (typeof acf.add_action !== 'undefined') {
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

    acf.add_action('ready append', function ($el) {
      // search $el for fields of type 'mapbox_geojson'
      acf.get_fields({ type: 'mapbox_geojson' }, $el).each(function () {
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
      $(postbox).find('.field[data-field_type="mapbox_geojson"]').each(function () {
        initializeField($(this))
      })
    })
  }
})(jQuery)
