import { delegate, scrollTop, getTopOffset } from 'lib/dom'
import getTarget from 'lib/get-hash-target.js'

/**
 * @param {HTMLElement} el Container element to bind <a> clicks to
 */
export default function scrollTo (el, cb = () => {}) {
  delegate('click', function (e) {
    const anchor = e.target
    const target = getTarget(anchor)

    if (target) {
      e.preventDefault()
      scrollTop(getTopOffset(target) - 70)
      cb()
    }
  }, 'a', el)
}
