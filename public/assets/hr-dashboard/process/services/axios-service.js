axios.interceptors.request.use(
  (config) => {
    const token = window.localStorage.getItem("token");

    config.headers["Authorization"] = token ?? null;
    config.headers["Content-Type"] = "application/json";

    return config;
  },
  (error) => {
    Promise.reject(error);
  }
);

axios.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    if (error.response) {
      const response = error.response;
      switch (response.status) {
        case 401:
          handlerError(response.data);
          break;
        case 403:
          handlerError(response.data);
          break;
        case 408 | 500:
          handlerError("Ooops! Something went wrong. Please check you internet connection or reload the page.", false);
          break;
        default:
          handlerError("Ooops! Something went wrong. Please check you internet connection or reload the page.", false);
          break;
      }
    }
    return Promise.reject(error);
  }
);

function handlerError(message, redirect = true) {
  if (redirect) {
    window.location.href = `${window.location.origin}/business-portal-login`;
  }

  $("#global_modal").modal("show");
  $("#global_message").text(message);
  $("#login-status").show();
}
