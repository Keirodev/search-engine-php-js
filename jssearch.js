// polyfills for IE<9
(function (fn) {
  if (!fn.map) {
    fn.map = function (f/*, thisArg */) {
      if (this === void 0 || this === null)
        throw new TypeError()

      const t = Object(this)
      const len = t.length >>> 0
      if (typeof f !== "function")
        throw new TypeError()

      const res = new Array(len)
      const thisArg = arguments.length >= 2 ? arguments[1] : void 0
      for (let i = 0; i < len; i++) {
        if (i in t)
          res[i] = f.call(thisArg, t[i], i, t)
      }

      return res
    }
  }
  if (!fn.forEach) {
    fn.forEach = function (f/*, thisArg */) {
      if (this === void 0 || this === null)
        throw new TypeError()

      const t = Object(this)
      const len = t.length >>> 0
      if (typeof f !== "function")
        throw new TypeError()

      const thisArg = arguments.length >= 2 ? arguments[1] : void 0
      for (let i = 0; i < len; i++) {
        if (i in t)
          f.call(thisArg, t[i], i, t)
      }
    }
  }
})(Array.prototype)

const jssearch = {

  /**
   * the actual words finally used to query (set by last search call)
   */
  queryWords: [],

  search: function (query) {
    let words = jssearch.tokenizeString(query)
    let result = {}

    jssearch.queryWords = words.map(function (i) {
      return i.t
    })

    // do not search when no words given
    if (!words.length) {
      return result
    }

    words = jssearch.completeWords(words)
    jssearch.queryWords = words.map(function (i) {
      return i.t
    })
    result = jssearch.searchForWords(words)

    const res = []
    for (let i in result) {
      res.push(result[i])
    }
    res.sort(function (a, b) {
      return b.weight - a.weight
    })
    return res
  },

  searchForWords: function (words) {
    const result = {}
    words.forEach(function (word) {
      if (jssearch.index[word.t]) {
        jssearch.index[word.t].forEach(function (file) {
          if (result[file.f]) {
            result[file.f].weight *= file.w * word.w
          } else {
            result[file.f] = {
              file: jssearch.files[file.f],
              weight: file.w * word.w
            }
          }
        })
      }
    })
    return result
  },

  completeWords: function (words) {
    const result = []

    words.forEach(function (word) {
      if (!jssearch.index[word.t] && word.t.length > 2) {
        // complete words that are not in the index
        for (let w in jssearch.index) {
          if (w.substr(0, word.t.length) === word.t) {
            result.push({t: w, w: 1})
          }
        }
      } else {
        // keep existing words
        result.push(word)
      }
    })
    return result
  },

  tokenizeString: function (string) {
    if (console) {
      console.log('Error: tokenizeString should have been overwritten by index JS file.')
    }
    return [{t: string, w: 1}]
  }
}
