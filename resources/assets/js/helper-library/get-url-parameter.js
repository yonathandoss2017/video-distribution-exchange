export const getURLParameter = function (name) {
    // this.params = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search) || [null, ''])[1].replace(/\+/g, '%20')) || null;
}