require('./bootstrap')

import 'leaflet/dist/leaflet.css'
import 'leaflet'

import 'leaflet-editable'

import 'uPlot/dist/uPlot.min.css'
import uPlot from 'uPlot'
window.uPlot = uPlot

window.ProgressBar = require('progressbar.js')
window.confetti = require('canvas-confetti')

import Pikaday from 'pikaday'
window.Pikaday = Pikaday

window.debounce = (fn, wait) => {
    let t
    return function () {
      clearTimeout(t)
      t = setTimeout(() => fn.apply(this, arguments), wait)
    }
}