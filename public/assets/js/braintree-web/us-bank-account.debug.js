(function(f){if(typeof exports==="object"&&typeof module!=="undefined"){module.exports=f()}else if(typeof define==="function"&&define.amd){define([],f)}else{var g;if(typeof window!=="undefined"){g=window}else if(typeof global!=="undefined"){g=global}else if(typeof self!=="undefined"){g=self}else{g=this}(g.braintree || (g.braintree = {})).usBankAccount = f()}})(function(){var define,module,exports;return (function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(_dereq_,module,exports){
(function (root) {

  // Store setTimeout reference so promise-polyfill will be unaffected by
  // other code modifying setTimeout (like sinon.useFakeTimers())
  var setTimeoutFunc = setTimeout;

  function noop() {}
  
  // Polyfill for Function.prototype.bind
  function bind(fn, thisArg) {
    return function () {
      fn.apply(thisArg, arguments);
    };
  }

  function Promise(fn) {
    if (typeof this !== 'object') throw new TypeError('Promises must be constructed via new');
    if (typeof fn !== 'function') throw new TypeError('not a function');
    this._state = 0;
    this._handled = false;
    this._value = undefined;
    this._deferreds = [];

    doResolve(fn, this);
  }

  function handle(self, deferred) {
    while (self._state === 3) {
      self = self._value;
    }
    if (self._state === 0) {
      self._deferreds.push(deferred);
      return;
    }
    self._handled = true;
    Promise._immediateFn(function () {
      var cb = self._state === 1 ? deferred.onFulfilled : deferred.onRejected;
      if (cb === null) {
        (self._state === 1 ? resolve : reject)(deferred.promise, self._value);
        return;
      }
      var ret;
      try {
        ret = cb(self._value);
      } catch (e) {
        reject(deferred.promise, e);
        return;
      }
      resolve(deferred.promise, ret);
    });
  }

  function resolve(self, newValue) {
    try {
      // Promise Resolution Procedure: https://github.com/promises-aplus/promises-spec#the-promise-resolution-procedure
      if (newValue === self) throw new TypeError('A promise cannot be resolved with itself.');
      if (newValue && (typeof newValue === 'object' || typeof newValue === 'function')) {
        var then = newValue.then;
        if (newValue instanceof Promise) {
          self._state = 3;
          self._value = newValue;
          finale(self);
          return;
        } else if (typeof then === 'function') {
          doResolve(bind(then, newValue), self);
          return;
        }
      }
      self._state = 1;
      self._value = newValue;
      finale(self);
    } catch (e) {
      reject(self, e);
    }
  }

  function reject(self, newValue) {
    self._state = 2;
    self._value = newValue;
    finale(self);
  }

  function finale(self) {
    if (self._state === 2 && self._deferreds.length === 0) {
      Promise._immediateFn(function() {
        if (!self._handled) {
          Promise._unhandledRejectionFn(self._value);
        }
      });
    }

    for (var i = 0, len = self._deferreds.length; i < len; i++) {
      handle(self, self._deferreds[i]);
    }
    self._deferreds = null;
  }

  function Handler(onFulfilled, onRejected, promise) {
    this.onFulfilled = typeof onFulfilled === 'function' ? onFulfilled : null;
    this.onRejected = typeof onRejected === 'function' ? onRejected : null;
    this.promise = promise;
  }

  /**
   * Take a potentially misbehaving resolver function and make sure
   * onFulfilled and onRejected are only called once.
   *
   * Makes no guarantees about asynchrony.
   */
  function doResolve(fn, self) {
    var done = false;
    try {
      fn(function (value) {
        if (done) return;
        done = true;
        resolve(self, value);
      }, function (reason) {
        if (done) return;
        done = true;
        reject(self, reason);
      });
    } catch (ex) {
      if (done) return;
      done = true;
      reject(self, ex);
    }
  }

  Promise.prototype['catch'] = function (onRejected) {
    return this.then(null, onRejected);
  };

  Promise.prototype.then = function (onFulfilled, onRejected) {
    var prom = new (this.constructor)(noop);

    handle(this, new Handler(onFulfilled, onRejected, prom));
    return prom;
  };

  Promise.all = function (arr) {
    var args = Array.prototype.slice.call(arr);

    return new Promise(function (resolve, reject) {
      if (args.length === 0) return resolve([]);
      var remaining = args.length;

      function res(i, val) {
        try {
          if (val && (typeof val === 'object' || typeof val === 'function')) {
            var then = val.then;
            if (typeof then === 'function') {
              then.call(val, function (val) {
                res(i, val);
              }, reject);
              return;
            }
          }
          args[i] = val;
          if (--remaining === 0) {
            resolve(args);
          }
        } catch (ex) {
          reject(ex);
        }
      }

      for (var i = 0; i < args.length; i++) {
        res(i, args[i]);
      }
    });
  };

  Promise.resolve = function (value) {
    if (value && typeof value === 'object' && value.constructor === Promise) {
      return value;
    }

    return new Promise(function (resolve) {
      resolve(value);
    });
  };

  Promise.reject = function (value) {
    return new Promise(function (resolve, reject) {
      reject(value);
    });
  };

  Promise.race = function (values) {
    return new Promise(function (resolve, reject) {
      for (var i = 0, len = values.length; i < len; i++) {
        values[i].then(resolve, reject);
      }
    });
  };

  // Use polyfill for setImmediate for performance gains
  Promise._immediateFn = (typeof setImmediate === 'function' && function (fn) { setImmediate(fn); }) ||
    function (fn) {
      setTimeoutFunc(fn, 0);
    };

  Promise._unhandledRejectionFn = function _unhandledRejectionFn(err) {
    if (typeof console !== 'undefined' && console) {
      console.warn('Possible Unhandled Promise Rejection:', err); // eslint-disable-line no-console
    }
  };

  /**
   * Set the immediate function to execute callbacks
   * @param fn {function} Function to execute
   * @deprecated
   */
  Promise._setImmediateFn = function _setImmediateFn(fn) {
    Promise._immediateFn = fn;
  };

  /**
   * Change the function to execute on unhandled rejection
   * @param {function} fn Function to execute on unhandled rejection
   * @deprecated
   */
  Promise._setUnhandledRejectionFn = function _setUnhandledRejectionFn(fn) {
    Promise._unhandledRejectionFn = fn;
  };
  
  if (typeof module !== 'undefined' && module.exports) {
    module.exports = Promise;
  } else if (!root.Promise) {
    root.Promise = Promise;
  }

})(this);

},{}],2:[function(_dereq_,module,exports){
'use strict';

function deferred(fn) {
  return function () {
    // IE9 doesn't support passing arguments to setTimeout so we have to emulate it.
    var args = arguments;

    setTimeout(function () {
      fn.apply(null, args);
    }, 1);
  };
}

module.exports = deferred;

},{}],3:[function(_dereq_,module,exports){
'use strict';

function once(fn) {
  var called = false;

  return function () {
    if (!called) {
      called = true;
      fn.apply(null, arguments);
    }
  };
}

module.exports = once;

},{}],4:[function(_dereq_,module,exports){
'use strict';

function promiseOrCallback(promise, callback) { // eslint-disable-line consistent-return
  if (callback) {
    promise
      .then(function (data) {
        callback(null, data);
      })
      .catch(function (err) {
        callback(err);
      });
  } else {
    return promise;
  }
}

module.exports = promiseOrCallback;

},{}],5:[function(_dereq_,module,exports){
'use strict';

var deferred = _dereq_('./lib/deferred');
var once = _dereq_('./lib/once');
var promiseOrCallback = _dereq_('./lib/promise-or-callback');

function wrapPromise(fn) {
  return function () {
    var callback;
    var args = Array.prototype.slice.call(arguments);
    var lastArg = args[args.length - 1];

    if (typeof lastArg === 'function') {
      callback = args.pop();
      callback = once(deferred(callback));
    }
    return promiseOrCallback(fn.apply(this, args), callback); // eslint-disable-line no-invalid-this
  };
}

wrapPromise.wrapPrototype = function (target, options) {
  var methods, ignoreMethods, includePrivateMethods;

  options = options || {};
  ignoreMethods = options.ignoreMethods || [];
  includePrivateMethods = options.transformPrivateMethods === true;

  methods = Object.getOwnPropertyNames(target.prototype).filter(function (method) {
    var isNotPrivateMethod;
    var isNonConstructorFunction = method !== 'constructor' &&
      typeof target.prototype[method] === 'function';
    var isNotAnIgnoredMethod = ignoreMethods.indexOf(method) === -1;

    if (includePrivateMethods) {
      isNotPrivateMethod = true;
    } else {
      isNotPrivateMethod = method.charAt(0) !== '_';
    }

    return isNonConstructorFunction &&
      isNotPrivateMethod &&
      isNotAnIgnoredMethod;
  });

  methods.forEach(function (method) {
    var original = target.prototype[method];

    target.prototype[method] = wrapPromise(original);
  });

  return target;
};

module.exports = wrapPromise;

},{"./lib/deferred":2,"./lib/once":3,"./lib/promise-or-callback":4}],6:[function(_dereq_,module,exports){
'use strict';

var createAuthorizationData = _dereq_('./create-authorization-data');
var jsonClone = _dereq_('./json-clone');
var constants = _dereq_('./constants');

function addMetadata(configuration, data) {
  var key;
  var attrs = data ? jsonClone(data) : {};
  var authAttrs = createAuthorizationData(configuration.authorization).attrs;
  var _meta = jsonClone(configuration.analyticsMetadata);

  attrs.braintreeLibraryVersion = constants.BRAINTREE_LIBRARY_VERSION;

  for (key in attrs._meta) {
    if (attrs._meta.hasOwnProperty(key)) {
      _meta[key] = attrs._meta[key];
    }
  }

  attrs._meta = _meta;

  if (authAttrs.tokenizationKey) {
    attrs.tokenizationKey = authAttrs.tokenizationKey;
  } else {
    attrs.authorizationFingerprint = authAttrs.authorizationFingerprint;
  }

  return attrs;
}

module.exports = addMetadata;

},{"./constants":10,"./create-authorization-data":12,"./json-clone":15}],7:[function(_dereq_,module,exports){
'use strict';

var constants = _dereq_('./constants');
var addMetadata = _dereq_('./add-metadata');

function _millisToSeconds(millis) {
  return Math.floor(millis / 1000);
}

function sendAnalyticsEvent(client, kind, callback) {
  var configuration = client.getConfiguration();
  var request = client._request;
  var timestamp = _millisToSeconds(Date.now());
  var url = configuration.gatewayConfiguration.analytics.url;
  var data = {
    analytics: [{
      kind: constants.ANALYTICS_PREFIX + kind,
      timestamp: timestamp
    }]
  };

  request({
    url: url,
    method: 'post',
    data: addMetadata(configuration, data),
    timeout: constants.ANALYTICS_REQUEST_TIMEOUT_MS
  }, callback);
}

module.exports = {
  sendEvent: sendAnalyticsEvent
};

},{"./add-metadata":6,"./constants":10}],8:[function(_dereq_,module,exports){
'use strict';

var enumerate = _dereq_('./enumerate');

/**
 * @class
 * @global
 * @param {object} options Construction options
 * @classdesc This class is used to report error conditions, frequently as the first parameter to callbacks throughout the Braintree SDK.
 * @description <strong>You cannot use this constructor directly. Interact with instances of this class through {@link callback callbacks}.</strong>
 */
function BraintreeError(options) {
  if (!BraintreeError.types.hasOwnProperty(options.type)) {
    throw new Error(options.type + ' is not a valid type.');
  }

  if (!options.code) {
    throw new Error('Error code required.');
  }

  if (!options.message) {
    throw new Error('Error message required.');
  }

  this.name = 'BraintreeError';

  /**
   * @type {string}
   * @description A code that corresponds to specific errors.
   */
  this.code = options.code;

  /**
   * @type {string}
   * @description A short description of the error.
   */
  this.message = options.message;

  /**
   * @type {BraintreeError.types}
   * @description The type of error.
   */
  this.type = options.type;

  /**
   * @type {object=}
   * @description Additional information about the error, such as an underlying network error response.
   */
  this.details = options.details;
}

BraintreeError.prototype = Object.create(Error.prototype);
BraintreeError.prototype.constructor = BraintreeError;

/**
 * Enum for {@link BraintreeError} types.
 * @name BraintreeError.types
 * @enum
 * @readonly
 * @memberof BraintreeError
 * @property {string} CUSTOMER An error caused by the customer.
 * @property {string} MERCHANT An error that is actionable by the merchant.
 * @property {string} NETWORK An error due to a network problem.
 * @property {string} INTERNAL An error caused by Braintree code.
 * @property {string} UNKNOWN An error where the origin is unknown.
 */
BraintreeError.types = enumerate([
  'CUSTOMER',
  'MERCHANT',
  'NETWORK',
  'INTERNAL',
  'UNKNOWN'
]);

BraintreeError.findRootError = function (err) {
  if (err instanceof BraintreeError && err.details && err.details.originalError) {
    return BraintreeError.findRootError(err.details.originalError);
  }

  return err;
};

module.exports = BraintreeError;

},{"./enumerate":13}],9:[function(_dereq_,module,exports){
'use strict';

// Taken from https://github.com/sindresorhus/decamelize/blob/95980ab6fb44c40eaca7792bdf93aff7c210c805/index.js
function transformKey(key) {
  return key.replace(/([a-z\d])([A-Z])/g, '$1_$2')
    .replace(/([A-Z]+)([A-Z][a-z\d]+)/g, '$1_$2')
    .toLowerCase();
}

module.exports = function (obj) {
  return Object.keys(obj).reduce(function (newObj, key) {
    var transformedKey = transformKey(key);

    newObj[transformedKey] = obj[key];

    return newObj;
  }, {});
};

},{}],10:[function(_dereq_,module,exports){
'use strict';

var VERSION = "3.14.0";
var PLATFORM = 'web';

module.exports = {
  ANALYTICS_PREFIX: 'web.',
  ANALYTICS_REQUEST_TIMEOUT_MS: 2000,
  INTEGRATION_TIMEOUT_MS: 60000,
  VERSION: VERSION,
  INTEGRATION: 'custom',
  SOURCE: 'client',
  PLATFORM: PLATFORM,
  BRAINTREE_LIBRARY_VERSION: 'braintree/' + PLATFORM + '/' + VERSION
};

},{}],11:[function(_dereq_,module,exports){
'use strict';

var BraintreeError = _dereq_('./braintree-error');
var sharedErrors = _dereq_('./errors');

module.exports = function (instance, methodNames) {
  methodNames.forEach(function (methodName) {
    instance[methodName] = function () {
      throw new BraintreeError({
        type: sharedErrors.METHOD_CALLED_AFTER_TEARDOWN.type,
        code: sharedErrors.METHOD_CALLED_AFTER_TEARDOWN.code,
        message: methodName + ' cannot be called after teardown.'
      });
    };
  });
};

},{"./braintree-error":8,"./errors":14}],12:[function(_dereq_,module,exports){
'use strict';

var atob = _dereq_('../lib/polyfill').atob;

var apiUrls = {
  production: 'https://api.braintreegateway.com:443',
  sandbox: 'https://api.sandbox.braintreegateway.com:443'
};

// endRemoveIf(production)

function _isTokenizationKey(str) {
  return /^[a-zA-Z0-9]+_[a-zA-Z0-9]+_[a-zA-Z0-9_]+$/.test(str);
}

function _parseTokenizationKey(tokenizationKey) {
  var tokens = tokenizationKey.split('_');
  var environment = tokens[0];
  var merchantId = tokens.slice(2).join('_');

  return {
    merchantId: merchantId,
    environment: environment
  };
}

function createAuthorizationData(authorization) {
  var parsedClientToken, parsedTokenizationKey;
  var data = {
    attrs: {},
    configUrl: ''
  };

  if (_isTokenizationKey(authorization)) {
    parsedTokenizationKey = _parseTokenizationKey(authorization);
    data.attrs.tokenizationKey = authorization;
    data.configUrl = apiUrls[parsedTokenizationKey.environment] + '/merchants/' + parsedTokenizationKey.merchantId + '/client_api/v1/configuration';
  } else {
    parsedClientToken = JSON.parse(atob(authorization));
    data.attrs.authorizationFingerprint = parsedClientToken.authorizationFingerprint;
    data.configUrl = parsedClientToken.configUrl;
  }

  return data;
}

module.exports = createAuthorizationData;

},{"../lib/polyfill":18}],13:[function(_dereq_,module,exports){
'use strict';

function enumerate(values, prefix) {
  prefix = prefix == null ? '' : prefix;

  return values.reduce(function (enumeration, value) {
    enumeration[value] = prefix + value;
    return enumeration;
  }, {});
}

module.exports = enumerate;

},{}],14:[function(_dereq_,module,exports){
'use strict';

var BraintreeError = _dereq_('./braintree-error');

module.exports = {
  CALLBACK_REQUIRED: {
    type: BraintreeError.types.MERCHANT,
    code: 'CALLBACK_REQUIRED'
  },
  INSTANTIATION_OPTION_REQUIRED: {
    type: BraintreeError.types.MERCHANT,
    code: 'INSTANTIATION_OPTION_REQUIRED'
  },
  INVALID_OPTION: {
    type: BraintreeError.types.MERCHANT,
    code: 'INVALID_OPTION'
  },
  INCOMPATIBLE_VERSIONS: {
    type: BraintreeError.types.MERCHANT,
    code: 'INCOMPATIBLE_VERSIONS'
  },
  METHOD_CALLED_AFTER_TEARDOWN: {
    type: BraintreeError.types.MERCHANT,
    code: 'METHOD_CALLED_AFTER_TEARDOWN'
  },
  BRAINTREE_API_ACCESS_RESTRICTED: {
    type: BraintreeError.types.MERCHANT,
    code: 'BRAINTREE_API_ACCESS_RESTRICTED',
    message: 'Your access is restricted and cannot use this part of the Braintree API.'
  }
};

},{"./braintree-error":8}],15:[function(_dereq_,module,exports){
'use strict';

module.exports = function (value) {
  return JSON.parse(JSON.stringify(value));
};

},{}],16:[function(_dereq_,module,exports){
'use strict';

module.exports = function (obj) {
  return Object.keys(obj).filter(function (key) {
    return typeof obj[key] === 'function';
  });
};

},{}],17:[function(_dereq_,module,exports){
arguments[4][3][0].apply(exports,arguments)
},{"dup":3}],18:[function(_dereq_,module,exports){
(function (global){
'use strict';

var atobNormalized = typeof global.atob === 'function' ? global.atob : atob;

function atob(base64String) {
  var a, b, c, b1, b2, b3, b4, i;
  var base64Matcher = new RegExp('^(?:[A-Za-z0-9+/]{4})*(?:[A-Za-z0-9+/]{2}==|[A-Za-z0-9+/]{3}=|[A-Za-z0-9+/]{4})([=]{1,2})?$');
  var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
  var result = '';

  if (!base64Matcher.test(base64String)) {
    throw new Error('Non base64 encoded input passed to window.atob polyfill');
  }

  i = 0;
  do {
    b1 = characters.indexOf(base64String.charAt(i++));
    b2 = characters.indexOf(base64String.charAt(i++));
    b3 = characters.indexOf(base64String.charAt(i++));
    b4 = characters.indexOf(base64String.charAt(i++));

    a = (b1 & 0x3F) << 2 | b2 >> 4 & 0x3;
    b = (b2 & 0xF) << 4 | b3 >> 2 & 0xF;
    c = (b3 & 0x3) << 6 | b4 & 0x3F;

    result += String.fromCharCode(a) + (b ? String.fromCharCode(b) : '') + (c ? String.fromCharCode(c) : '');
  } while (i < base64String.length);

  return result;
}

module.exports = {
  atob: function (base64String) {
    return atobNormalized.call(global, base64String);
  },
  _atob: atob
};

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
},{}],19:[function(_dereq_,module,exports){
(function (global){
'use strict';

var Promise = global.Promise || _dereq_('promise-polyfill');

module.exports = Promise;

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
},{"promise-polyfill":1}],20:[function(_dereq_,module,exports){
'use strict';

module.exports = {
  PLAID_LINK_JS: 'https://cdn.plaid.com/link/v2/stable/link-initialize.js'
};

},{}],21:[function(_dereq_,module,exports){
'use strict';

var BraintreeError = _dereq_('../lib/braintree-error');

module.exports = {
  US_BANK_ACCOUNT_OPTION_REQUIRED: {
    type: BraintreeError.types.MERCHANT,
    code: 'US_BANK_ACCOUNT_OPTION_REQUIRED'
  },
  US_BANK_ACCOUNT_MUTUALLY_EXCLUSIVE_OPTIONS: {
    type: BraintreeError.types.MERCHANT,
    code: 'US_BANK_ACCOUNT_MUTUALLY_EXCLUSIVE_OPTIONS'
  },
  US_BANK_ACCOUNT_LOGIN_LOAD_FAILED: {
    type: BraintreeError.types.NETWORK,
    code: 'US_BANK_ACCOUNT_LOGIN_LOAD_FAILED',
    message: 'Bank login flow failed to load.'
  },
  US_BANK_ACCOUNT_LOGIN_CLOSED: {
    type: BraintreeError.types.CUSTOMER,
    code: 'US_BANK_ACCOUNT_LOGIN_CLOSED',
    message: 'Customer closed bank login flow before authorizing.'
  },
  US_BANK_ACCOUNT_LOGIN_REQUEST_ACTIVE: {
    type: BraintreeError.types.MERCHANT,
    code: 'US_BANK_ACCOUNT_LOGIN_REQUEST_ACTIVE',
    message: 'Another bank login tokenization request is active.'
  },
  US_BANK_ACCOUNT_TOKENIZATION_NETWORK_ERROR: {
    type: BraintreeError.types.NETWORK,
    code: 'US_BANK_ACCOUNT_TOKENIZATION_NETWORK_ERROR',
    message: 'A tokenization network error occurred.'
  },
  US_BANK_ACCOUNT_FAILED_TOKENIZATION: {
    type: BraintreeError.types.CUSTOMER,
    code: 'US_BANK_ACCOUNT_FAILED_TOKENIZATION',
    message: 'The supplied data failed tokenization.'
  },
  US_BANK_ACCOUNT_NOT_ENABLED: {
    type: BraintreeError.types.MERCHANT,
    code: 'US_BANK_ACCOUNT_NOT_ENABLED',
    message: 'US bank account is not enabled.'
  },
  US_BANK_ACCOUNT_BANK_LOGIN_NOT_ENABLED: {
    type: BraintreeError.types.MERCHANT,
    code: 'US_BANK_ACCOUNT_BANK_LOGIN_NOT_ENABLED',
    message: 'Bank login is not enabled.'
  }
};

},{"../lib/braintree-error":8}],22:[function(_dereq_,module,exports){
'use strict';
/**
 * @module braintree-web/us-bank-account
 * @description This module is for accepting payments of US bank accounts.
 */

var BraintreeError = _dereq_('../lib/braintree-error');
var errors = _dereq_('./errors');
var USBankAccount = _dereq_('./us-bank-account');
var VERSION = "3.14.0";
var sharedErrors = _dereq_('../lib/errors');
var Promise = _dereq_('../lib/promise');
var wrapPromise = _dereq_('wrap-promise');

/**
 * @static
 * @function create
 * @param {object} options Creation options:
 * @param {Client} options.client A {@link Client} instance.
 * @param {callback} [callback] The second argument, `data`, is the {@link USBankAccount} instance. If no callback is provided, `create` returns a promise that resolves with the {@link USBankAccount} instance.
 * @returns {Promise|void} Returns a promise if no callback is provided.
 */
function create(options) {
  var clientVersion, braintreeApi, usBankAccount;

  if (options.client == null) {
    return Promise.reject(new BraintreeError({
      type: sharedErrors.INSTANTIATION_OPTION_REQUIRED.type,
      code: sharedErrors.INSTANTIATION_OPTION_REQUIRED.code,
      message: 'options.client is required when instantiating US Bank Account.'
    }));
  }

  clientVersion = options.client.getConfiguration().analyticsMetadata.sdkVersion;
  if (clientVersion !== VERSION) {
    return Promise.reject(new BraintreeError({
      type: sharedErrors.INCOMPATIBLE_VERSIONS.type,
      code: sharedErrors.INCOMPATIBLE_VERSIONS.code,
      message: 'Client (version ' + clientVersion + ') and US Bank Account (version ' + VERSION + ') components must be from the same SDK version.'
    }));
  }

  braintreeApi = options.client.getConfiguration().gatewayConfiguration.braintreeApi;
  if (!braintreeApi) {
    return Promise.reject(new BraintreeError(sharedErrors.BRAINTREE_API_ACCESS_RESTRICTED));
  }

  usBankAccount = options.client.getConfiguration().gatewayConfiguration.usBankAccount;
  if (!usBankAccount) {
    return Promise.reject(new BraintreeError(errors.US_BANK_ACCOUNT_NOT_ENABLED));
  }

  return Promise.resolve(new USBankAccount(options));
}

module.exports = {
  create: wrapPromise(create),
  /**
   * @description The current version of the SDK, i.e. `{@pkg version}`.
   * @type {string}
   */
  VERSION: VERSION
};

},{"../lib/braintree-error":8,"../lib/errors":14,"../lib/promise":19,"./errors":21,"./us-bank-account":23,"wrap-promise":5}],23:[function(_dereq_,module,exports){
(function (global){
'use strict';

var BraintreeError = _dereq_('../lib/braintree-error');
var constants = _dereq_('./constants');
var errors = _dereq_('./errors');
var sharedErrors = _dereq_('../lib/errors');
var analytics = _dereq_('../lib/analytics');
var once = _dereq_('../lib/once');
var convertMethodsToError = _dereq_('../lib/convert-methods-to-error');
var methods = _dereq_('../lib/methods');
var camelCaseToSnakeCase = _dereq_('../lib/camel-case-to-snake-case');
var Promise = _dereq_('../lib/promise');
var wrapPromise = _dereq_('wrap-promise');

/**
 * @typedef {object} USBankAccount~tokenizePayload
 * @property {string} nonce The payment method nonce.
 * @property {string} type The payment method type, always `us_bank_account`.
 * @property {object} details Additional account details. Currently empty.
 */

/**
 * @class
 * @param {object} options See {@link module:braintree-web/us-bank-account.create|us-bank-account.create}.
 * @classdesc This class represents a US Bank Account component. Instances of this class can tokenize raw bank details or present a bank login. <strong>You cannot use this constructor directly. Use {@link module:braintree-web/us-bank-account.create|braintree.us-bank-account.create} instead.</strong>
 */
function USBankAccount(options) {
  this._client = options.client;

  this._isTokenizingBankLogin = false;

  analytics.sendEvent(this._client, 'usbankaccount.initialized');
}

/**
 * Tokenizes bank information to return a payment method nonce. You can tokenize bank details by providing information like account and routing numbers. You can also tokenize with a bank login UI that prompts the customer to log into their bank account.
 * @public
 * @param {object} options All tokenization options for the US Bank Account component.
 * @param {string} options.mandateText A string for proof of customer authorization. For example, `'I authorize Braintree to debit my bank account on behalf of My Online Store.'`.
 * @param {object} [options.bankDetails] Bank detail information (such as account and routing numbers). `bankDetails` or `bankLogin` option must be provided.
 * @param {string} options.bankDetails.routingNumber The customer's bank routing number, such as `'307075259'`.
 * @param {string} options.bankDetails.accountNumber The customer's bank account number, such as `'999999999'`.
 * @param {string} options.bankDetails.accountType The customer's bank account type. Must be `'checking'` or `'savings'`.
 * @param {string} options.bankDetails.ownershipType The customer's bank account ownership type. Must be `'personal'` or `'business'`.
 * @param {string} [options.bankDetails.firstName] The customer's first name. Required when account ownership type is `personal`.
 * @param {string} [options.bankDetails.lastName] The customer's last name. Required when account ownership type is `personal`.
 * @param {string} [options.bankDetails.businessName] The customer's business name. Required when account ownership type is `business`.
 * @param {object} options.bankDetails.billingAddress The customer's billing address.
 * @param {string} options.bankDetails.billingAddress.streetAddress The street address for the customer's billing address, such as `'123 Fake St'`.
 * @param {string} [options.bankDetails.billingAddress.extendedAddress] The extended street address for the customer's billing address, such as `'Apartment B'`.
 * @param {string} options.bankDetails.billingAddress.locality The locality for the customer's billing address. This is typically a city, such as `'San Francisco'`.
 * @param {string} options.bankDetails.billingAddress.region The region for the customer's billing address. This is typically a state, such as `'CA'`.
 * @param {string} options.bankDetails.billingAddress.postalCode The postal code for the customer's billing address. This is typically a ZIP code, such as `'94119'`.
 * @param {object} [options.bankLogin] Bank login information. `bankLogin` or `bankDetails` option must be provided.
 * @param {string} options.bankLogin.displayName Display name for the bank login UI, such as `'My Store'`.
 * @param {string} options.bankLogin.ownershipType The customer's bank account ownership type. Must be `'personal'` or `'business'`.
 * @param {string} [options.bankLogin.firstName] The customer's first name. Required when account ownership type is `personal`.
 * @param {string} [options.bankLogin.lastName] The customer's last name. Required when account ownership type is `personal`.
 * @param {string} [options.bankLogin.businessName] The customer's business name. Required when account ownership type is `business`.
 * @param {object} options.bankLogin.billingAddress The customer's billing address.
 * @param {string} options.bankLogin.billingAddress.streetAddress The street address for the customer's billing address, such as `'123 Fake St'`.
 * @param {string} [options.bankLogin.billingAddress.extendedAddress] The extended street address for the customer's billing address, such as `'Apartment B'`.
 * @param {string} options.bankLogin.billingAddress.locality The locality for the customer's billing address. This is typically a city, such as `'San Francisco'`.
 * @param {string} options.bankLogin.billingAddress.region The region for the customer's billing address. This is typically a state, such as `'CA'`.
 * @param {string} options.bankLogin.billingAddress.postalCode The postal code for the customer's billing address. This is typically a ZIP code, such as `'94119'`.
 * @param {callback} [callback] The second argument, <code>data</code>, is a {@link USBankAccount~tokenizePayload|tokenizePayload}. If no callback is provided, `tokenize` returns a promise that resolves with {@link USBankAccount~tokenizePayload|tokenizePayload}.
 * @returns {Promise|void} Returns a promise if no callback is provided.
 * @example
 * <caption>Tokenizing raw bank details</caption>
 * var routingNumberInput = document.querySelector('input[name="routing-number"]');
 * var accountNumberInput = document.querySelector('input[name="account-number"]');
 * var accountTypeInput = document.querySelector('input[name="account-type"]:checked');
 * var ownershipTypeInput = document.querySelector('input[name="ownership-type"]:checked');
 * var firstNameInput = document.querySelector('input[name="first-name"]');
 * var lastNameInput = document.querySelector('input[name="last-name"]');
 * var businessNameInput = document.querySelector('input[name="business-name"]');
 * var billingAddressStreetInput = document.querySelector('input[name="street-address"]');
 * var billingAddressExtendedInput = document.querySelector('input[name="extended-address"]');
 * var billingAddressLocalityInput = document.querySelector('input[name="locality"]');
 * var billingAddressRegionSelect = document.querySelector('select[name="region"]');
 * var billingAddressPostalInput = document.querySelector('input[name="postal-code"]');
 *
 * submitButton.addEventListener('click', function (event) {
 *   var bankDetails = {
 *     routingNumber: routingNumberInput.value,
 *     accountNumber: accountNumberInput.value,
 *     accountType: accountTypeInput.value,
 *     ownershipType: ownershipTypeInput.value,
 *     billingAddress: {
 *       streetAddress: billingAddressStreetInput.value,
 *       extendedAddress: billingAddressExtendedInput.value,
 *       locality: billingAddressLocalityInput.value,
 *       region: billingAddressRegionSelect.value,
 *       postalCode: billingAddressPostalInput.value
 *     }
 *   };
 *
 *   if (bankDetails.ownershipType === 'personal') {
 *     bankDetails.firstName = firstNameInput.value;
 *     bankDetails.lastName = lastNameInput.value;
 *   } else {
 *     bankDetails.businessName = businessNameInput.value;
 *   }
 *
 *   event.preventDefault();
 *
 *   usBankAccountInstance.tokenize({
 *     bankDetails: bankDetails,
 *     mandateText: 'I authorize Braintree to debit my bank account on behalf of My Online Store.'
 *   }, function (tokenizeErr, tokenizedPayload) {
 *     if (tokenizeErr) {
 *       console.error('There was an error tokenizing the bank details.');
 *       return;
 *     }
 *
 *     // Send tokenizePayload.nonce to your server here!
 *   });
 * });
 * @example
 * <caption>Tokenizing with bank login UI</caption>
 * var ownershipTypeInput = document.querySelector('input[name="ownership-type"]:checked');
 * var firstNameInput = document.querySelector('input[name="first-name"]');
 * var lastNameInput = document.querySelector('input[name="last-name"]');
 * var businessNameInput = document.querySelector('input[name="business-name"]');
 * var billingAddressStreetInput = document.querySelector('input[name="street-address"]');
 * var billingAddressExtendedInput = document.querySelector('input[name="extended-address"]');
 * var billingAddressLocalityInput = document.querySelector('input[name="locality"]');
 * var billingAddressRegionSelect = document.querySelector('select[name="region"]');
 * var billingAddressPostalInput = document.querySelector('input[name="postal-code"]');
 *
 * bankLoginButton.addEventListener('click', function (event) {
 *   var bankLogin = {
 *     displayName: 'My Online Store',
 *     ownershipType: ownershipTypeInput.value,
 *     billingAddress: {
 *       streetAddress: billingAddressStreetInput.value,
 *       extendedAddress: billingAddressExtendedInput.value,
 *       locality: billingAddressLocalityInput.value,
 *       region: billingAddressRegionSelect.value,
 *       postalCode: billingAddressPostalInput.value
 *     }
 *   }
 *   event.preventDefault();
 *
 *   if (bankLogin.ownershipType === 'personal') {
 *     bankLogin.firstName = firstNameInput.value;
 *     bankLogin.lastName = lastNameInput.value;
 *   } else {
 *     bankLogin.businessName = businessNameInput.value;
 *   }
 *
 *   usBankAccountInstance.tokenize({
 *     bankLogin: bankLogin,
 *     mandateText: 'I authorize Braintree to debit my bank account on behalf of My Online Store.'
 *   }, function (tokenizeErr, tokenizedPayload) {
 *     if (tokenizeErr) {
 *       console.error('There was an error tokenizing the bank details.');
 *       return;
 *     }
 *
 *     // Send tokenizePayload.nonce to your server here!
 *   });
 * });
 */
USBankAccount.prototype.tokenize = function (options) {
  options = options || {};

  if (!options.mandateText) {
    return Promise.reject(new BraintreeError({
      type: errors.US_BANK_ACCOUNT_OPTION_REQUIRED.type,
      code: errors.US_BANK_ACCOUNT_OPTION_REQUIRED.code,
      message: 'mandateText property is required.'
    }));
  }

  if (options.bankDetails && options.bankLogin) {
    return Promise.reject(new BraintreeError({
      type: errors.US_BANK_ACCOUNT_MUTUALLY_EXCLUSIVE_OPTIONS.type,
      code: errors.US_BANK_ACCOUNT_MUTUALLY_EXCLUSIVE_OPTIONS.code,
      message: 'tokenize must be called with bankDetails or bankLogin, not both.'
    }));
  } else if (options.bankDetails) {
    return this._tokenizeBankDetails(options);
  } else if (options.bankLogin) {
    return this._tokenizeBankLogin(options);
  }

  return Promise.reject(new BraintreeError({
    type: errors.US_BANK_ACCOUNT_OPTION_REQUIRED.type,
    code: errors.US_BANK_ACCOUNT_OPTION_REQUIRED.code,
    message: 'tokenize must be called with bankDetails or bankLogin.'
  }));
};

USBankAccount.prototype._tokenizeBankDetails = function (options) {
  var client = this._client;
  var bankDetails = options.bankDetails;

  return client.request({
    method: 'POST',
    endpoint: 'tokens',
    api: 'braintreeApi',
    data: camelCaseToSnakeCase({
      type: 'us_bank_account',
      routingNumber: bankDetails.routingNumber,
      accountNumber: bankDetails.accountNumber,
      firstName: bankDetails.firstName,
      lastName: bankDetails.lastName,
      businessName: bankDetails.businessName,
      accountType: bankDetails.accountType,
      ownershipType: bankDetails.ownershipType,
      billingAddress: camelCaseToSnakeCase(bankDetails.billingAddress || {}),
      achMandate: {
        text: options.mandateText
      }
    })
  }).then(function (response) {
    analytics.sendEvent(client, 'usbankaccount.bankdetails.tokenization.succeeded');

    return Promise.resolve(formatTokenizeResponse(response));
  }).catch(function (err) {
    var error = errorFrom(err);

    analytics.sendEvent(client, 'usbankaccount.bankdetails.tokenization.failed');

    return Promise.reject(error);
  });
};

USBankAccount.prototype._tokenizeBankLogin = function (options) {
  var self = this;
  var client = this._client;
  var gatewayConfiguration = client.getConfiguration().gatewayConfiguration;
  var isProduction = gatewayConfiguration.environment === 'production';
  var plaidConfig = gatewayConfiguration.usBankAccount.plaid;

  if (!options.bankLogin.displayName) {
    return Promise.reject(new BraintreeError({
      type: errors.US_BANK_ACCOUNT_OPTION_REQUIRED.type,
      code: errors.US_BANK_ACCOUNT_OPTION_REQUIRED.code,
      message: 'displayName property is required when using bankLogin.'
    }));
  }

  if (!plaidConfig) {
    return Promise.reject(new BraintreeError(errors.US_BANK_ACCOUNT_BANK_LOGIN_NOT_ENABLED));
  }

  if (this._isTokenizingBankLogin) {
    return Promise.reject(new BraintreeError(errors.US_BANK_ACCOUNT_LOGIN_REQUEST_ACTIVE));
  }
  this._isTokenizingBankLogin = true;

  return new Promise(function (resolve, reject) {
    self._loadPlaid(function (plaidLoadErr, plaid) {
      if (plaidLoadErr) {
        reject(plaidLoadErr);
        return;
      }

      plaid.create({
        clientName: options.bankLogin.displayName,
        env: isProduction ? 'production' : 'tartan',
        key: isProduction ? plaidConfig.publicKey : 'test_key',
        product: 'auth',
        selectAccount: true,
        onExit: function () {
          self._isTokenizingBankLogin = false;

          analytics.sendEvent(client, 'usbankaccount.banklogin.tokenization.closed.by-user');

          reject(new BraintreeError(errors.US_BANK_ACCOUNT_LOGIN_CLOSED));
        },
        onSuccess: function (publicToken, metadata) {
          client.request({
            method: 'POST',
            endpoint: 'tokens',
            api: 'braintreeApi',
            data: camelCaseToSnakeCase({
              type: 'plaid_public_token',
              publicToken: publicToken,
              accountId: metadata.account_id,
              achMandate: {
                text: options.mandateText
              },
              ownershipType: options.bankLogin.ownershipType,
              firstName: options.bankLogin.firstName,
              lastName: options.bankLogin.lastName,
              businessName: options.bankLogin.businessName,
              billingAddress: camelCaseToSnakeCase(options.bankLogin.billingAddress || {})
            })
          }).then(function (response) {
            self._isTokenizingBankLogin = false;

            analytics.sendEvent(client, 'usbankaccount.banklogin.tokenization.succeeded');

            resolve(formatTokenizeResponse(response));
          }).catch(function (tokenizeErr) {
            var error;

            self._isTokenizingBankLogin = false;
            error = errorFrom(tokenizeErr);

            analytics.sendEvent(client, 'usbankaccount.banklogin.tokenization.failed');

            reject(error);
          });
        }
      }).open();

      analytics.sendEvent(client, 'usbankaccount.banklogin.tokenization.started');
    });
  });
};

function errorFrom(err) {
  var error;
  var status = err.details && err.details.httpStatus;

  if (status === 401) {
    error = new BraintreeError(sharedErrors.BRAINTREE_API_ACCESS_RESTRICTED);
  } else if (status < 500) {
    error = new BraintreeError(errors.US_BANK_ACCOUNT_FAILED_TOKENIZATION);
  } else {
    error = new BraintreeError(errors.US_BANK_ACCOUNT_TOKENIZATION_NETWORK_ERROR);
  }
  error.details = {originalError: err};
  return error;
}

function formatTokenizeResponse(response) {
  return {
    nonce: response.data.id,
    details: {},
    description: response.data.description,
    type: response.data.type
  };
}

USBankAccount.prototype._loadPlaid = function (callback) {
  var existingScript, script;

  callback = once(callback);

  if (global.Plaid) {
    callback(null, global.Plaid);
    return;
  }

  existingScript = document.querySelector('script[src="' + constants.PLAID_LINK_JS + '"]');

  if (existingScript) {
    addLoadListeners(existingScript, callback);
  } else {
    script = document.createElement('script');

    script.src = constants.PLAID_LINK_JS;
    script.async = true;

    addLoadListeners(script, callback);

    document.body.appendChild(script);

    this._plaidScript = script;
  }
};

function addLoadListeners(script, callback) {
  function loadHandler() {
    var readyState = this.readyState; // eslint-disable-line no-invalid-this

    if (!readyState || readyState === 'loaded' || readyState === 'complete') {
      removeLoadListeners();
      callback(null, global.Plaid);
    }
  }

  function errorHandler() {
    script.parentNode.removeChild(script);

    callback(new BraintreeError(errors.US_BANK_ACCOUNT_LOGIN_LOAD_FAILED));
  }

  function removeLoadListeners() {
    script.removeEventListener('error', errorHandler);
    script.removeEventListener('load', loadHandler);
    script.removeEventListener('readystatechange', loadHandler);
  }

  script.addEventListener('error', errorHandler);
  script.addEventListener('load', loadHandler);
  script.addEventListener('readystatechange', loadHandler);
}

/**
 * Cleanly tear down anything set up by {@link module:braintree-web/us-bank-account.create|create}.
 * @public
 * @param {callback} [callback] Called once teardown is complete. No data is returned if teardown completes successfully.
 * @example
 * usBankAccountInstance.teardown();
 * @example <caption>With callback</caption>
 * usBankAccountInstance.teardown(function () {
 *   // teardown is complete
 * });
 * @returns {Promise|void} Returns a promise if no callback is provided.
 */
USBankAccount.prototype.teardown = function () {
  if (this._plaidScript) {
    document.body.removeChild(this._plaidScript);
  }

  convertMethodsToError(this, methods(USBankAccount.prototype));

  return Promise.resolve();
};

module.exports = wrapPromise.wrapPrototype(USBankAccount);

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
},{"../lib/analytics":7,"../lib/braintree-error":8,"../lib/camel-case-to-snake-case":9,"../lib/convert-methods-to-error":11,"../lib/errors":14,"../lib/methods":16,"../lib/once":17,"../lib/promise":19,"./constants":20,"./errors":21,"wrap-promise":5}]},{},[22])(22)
});